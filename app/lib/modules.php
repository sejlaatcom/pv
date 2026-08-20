<?php
/** الحضور، المهام، المسابقات، الإحصاءات والتقارير. */

// =====================================================================
//  الحضور والانصراف
// =====================================================================

/** كشف الحضور ليوم محدد مع حالة كل طالب */
function attendance_sheet(int $entityId, string $day, ?int $groupId = null): array
{
    $sql = 'SELECT s.id AS student_id, s.name, s.code, s.group_id, g.name AS group_name,
                   a.id AS record_id, a.status, a.check_in, a.check_out
            FROM students s
            LEFT JOIN student_groups g ON g.id = s.group_id
            LEFT JOIN attendance a ON a.student_id = s.id AND a.day = ?
            WHERE s.entity_id = ? AND s.is_active = 1';
    $params = [$day, $entityId];
    if ($groupId) {
        $sql .= ' AND s.group_id = ?';
        $params[] = $groupId;
    }
    return db_all($sql . ' ORDER BY g.name, s.name', $params);
}

/** حفظ الكشف: يحدّث السجل الموجود أو ينشئ سجلاً جديداً */
function attendance_save(int $entityId, string $day, array $entries, ?int $userId = null): int
{
    $allowed = ['present', 'absent', 'late', 'excused'];
    $saved = 0;

    foreach ($entries as $studentId => $status) {
        $studentId = (int) $studentId;
        if (!in_array($status, $allowed, true)) {
            continue;
        }
        $student = db_one('SELECT id, group_id FROM students WHERE id = ? AND entity_id = ?', [$studentId, $entityId]);
        if (!$student) {
            continue;
        }
        db_run(
            'INSERT INTO attendance (entity_id, group_id, student_id, day, status, check_in, recorded_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status = VALUES(status), check_in = VALUES(check_in), recorded_by = VALUES(recorded_by)',
            [
                $entityId,
                $student['group_id'],
                $studentId,
                $day,
                $status,
                $status === 'absent' ? null : ($status === 'late' ? '07:35' : '07:00'),
                $userId,
            ]
        );
        $saved++;
    }
    return $saved;
}

function attendance_stats(int $entityId, string $day): array
{
    $row = db_one(
        "SELECT
            SUM(status = 'present') AS present,
            SUM(status = 'late')    AS late,
            SUM(status = 'absent')  AS absent,
            SUM(status = 'excused') AS excused,
            COUNT(*) AS total
         FROM attendance WHERE entity_id = ? AND day = ?",
        [$entityId, $day]
    ) ?? [];

    $total = (int) ($row['total'] ?? 0);
    $present = (int) ($row['present'] ?? 0) + (int) ($row['late'] ?? 0);

    return [
        'present' => (int) ($row['present'] ?? 0),
        'late'    => (int) ($row['late'] ?? 0),
        'absent'  => (int) ($row['absent'] ?? 0),
        'excused' => (int) ($row['excused'] ?? 0),
        'total'   => $total,
        'rate'    => $total > 0 ? (int) round($present / $total * 100) : 0,
    ];
}

// =====================================================================
//  المهام والدرجات
// =====================================================================

function tasks_list(int $entityId): array
{
    return db_all(
        "SELECT t.*, g.name AS group_name,
                (SELECT COUNT(*) FROM task_submissions x WHERE x.task_id = t.id) AS total,
                (SELECT COUNT(*) FROM task_submissions x WHERE x.task_id = t.id AND x.status = 'graded') AS graded
         FROM tasks t LEFT JOIN student_groups g ON g.id = t.group_id
         WHERE t.entity_id = ? ORDER BY t.created_at DESC",
        [$entityId]
    );
}

function task_get(int $id): ?array
{
    return db_one(
        'SELECT t.*, g.name AS group_name FROM tasks t
         LEFT JOIN student_groups g ON g.id = t.group_id WHERE t.id = ?',
        [$id]
    );
}

/** إنشاء مهمة وتوليد سجلات تسليم لكل طالب في المجموعة */
function task_create(int $entityId, array $data, ?int $userId = null): int
{
    $taskId = db_insert('tasks', [
        'entity_id'   => $entityId,
        'group_id'    => ($data['group_id'] ?? null) ?: null,
        'title'       => $data['title'],
        'description' => ($data['description'] ?? null) ?: null,
        'due_date'    => ($data['due_date'] ?? null) ?: null,
        'max_score'   => (int) (($data['max_score'] ?? 0) ?: 10),
        'points'      => (int) (($data['points'] ?? 0) ?: 10),
        'created_by'  => $userId,
    ]);

    $students = students_list($entityId, [
        'group_id'    => $data['group_id'] ?: null,
        'only_active' => true,
    ]);
    foreach ($students as $student) {
        db_insert('task_submissions', [
            'task_id'    => $taskId,
            'student_id' => $student['id'],
            'status'     => 'pending',
        ]);
    }
    return $taskId;
}

function task_submissions(int $taskId): array
{
    return db_all(
        'SELECT x.*, s.name AS student_name FROM task_submissions x
         JOIN students s ON s.id = x.student_id
         WHERE x.task_id = ? ORDER BY s.name',
        [$taskId]
    );
}

/** رصد درجة: تُمنح نقاط المهمة عند تحقيق نصف الدرجة فأكثر */
function task_grade(int $entityId, int $submissionId, int $score): void
{
    $submission = db_one('SELECT * FROM task_submissions WHERE id = ?', [$submissionId]);
    if (!$submission) {
        throw new RuntimeException('سجل التسليم غير موجود');
    }
    $task = db_one('SELECT * FROM tasks WHERE id = ? AND entity_id = ?', [$submission['task_id'], $entityId]);
    if (!$task) {
        throw new RuntimeException('المهمة غير موجودة');
    }

    $score = max(0, min((int) $task['max_score'], $score));
    $alreadyGraded = $submission['status'] === 'graded';

    db_update('task_submissions', $submissionId, [
        'score'        => $score,
        'status'       => 'graded',
        'submitted_at' => $submission['submitted_at'] ?: date('Y-m-d H:i:s'),
    ]);

    $ratio = (int) $task['max_score'] > 0 ? $score / (int) $task['max_score'] : 0;
    if (!$alreadyGraded && $ratio >= 0.5) {
        points_award($entityId, [(int) $submission['student_id']], [
            'points' => max(1, (int) round((int) $task['points'] * $ratio)),
            'reason' => 'مهمة: ' . $task['title'],
        ]);
    }
}

// =====================================================================
//  المسابقات والاستبانات
// =====================================================================

function quizzes_list(int $entityId): array
{
    return db_all(
        'SELECT q.*,
                (SELECT COUNT(*) FROM quiz_questions x WHERE x.quiz_id = q.id) AS question_count,
                (SELECT COUNT(*) FROM quiz_attempts a WHERE a.quiz_id = q.id) AS attempt_count
         FROM quizzes q WHERE q.entity_id = ? ORDER BY q.created_at DESC',
        [$entityId]
    );
}

function quiz_get(int $id): ?array
{
    return db_one('SELECT * FROM quizzes WHERE id = ?', [$id]);
}

function quiz_questions(int $quizId, bool $withAnswers = false): array
{
    $rows = db_all('SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY sort_order, id', [$quizId]);
    foreach ($rows as $i => $row) {
        $rows[$i]['options'] = $row['options_json'] ? (json_decode($row['options_json'], true) ?: []) : [];
        if (!$withAnswers) {
            unset($rows[$i]['correct_answer']);
        }
    }
    return $rows;
}

/** إنشاء مسابقة مع أسئلتها */
function quiz_create(int $entityId, array $data, array $questions, ?int $userId = null): int
{
    $quizId = db_insert('quizzes', [
        'entity_id'          => $entityId,
        'title'              => $data['title'],
        'description'        => ($data['description'] ?? null) ?: null,
        'type'               => $data['type'] ?? 'quiz',
        'category'           => ($data['category'] ?? null) ?: null,
        'points_per_correct' => (int) (($data['points_per_correct'] ?? 0) ?: 5),
        'time_limit_minutes' => !empty($data['time_limit_minutes']) ? (int) $data['time_limit_minutes'] : null,
        'is_published'       => 0,
        'created_by'         => $userId,
    ]);

    $order = 0;
    foreach ($questions as $question) {
        $text = trim($question['question'] ?? '');
        $options = array_values(array_filter(array_map('trim', $question['options'] ?? [])));
        if (mb_strlen($text) < 2 || count($options) < 2) {
            continue;
        }
        $correctIndex = isset($question['correct']) ? (int) $question['correct'] : 0;
        db_insert('quiz_questions', [
            'quiz_id'        => $quizId,
            'question'       => $text,
            'options_json'   => json_encode($options, JSON_UNESCAPED_UNICODE),
            'correct_answer' => ($data['type'] ?? 'quiz') === 'survey' ? null : ($options[$correctIndex] ?? $options[0]),
            'points'         => 1,
            'sort_order'     => $order++,
        ]);
    }

    if ($order === 0) {
        db_delete('quizzes', $quizId);
        throw new RuntimeException('أضف سؤالاً واحداً على الأقل بخيارين');
    }

    return $quizId;
}

/** التصحيح الآلي واحتساب النقاط */
function quiz_submit(array $student, int $quizId, array $answers): array
{
    $quiz = db_one('SELECT * FROM quizzes WHERE id = ? AND entity_id = ?', [$quizId, $student['entity_id']]);
    if (!$quiz || !$quiz['is_published']) {
        throw new RuntimeException('المسابقة غير متاحة');
    }
    $already = db_value('SELECT id FROM quiz_attempts WHERE quiz_id = ? AND student_id = ?', [$quizId, $student['id']]);
    if ($already) {
        throw new RuntimeException('سبق أن شاركت في هذه المسابقة');
    }

    $questions = quiz_questions($quizId, true);
    $correct = 0;
    foreach ($questions as $question) {
        $answer = $answers[$question['id']] ?? null;
        if ($question['correct_answer'] === null || $answer === null) {
            continue;
        }
        if (trim((string) $answer) === trim((string) $question['correct_answer'])) {
            $correct++;
        }
    }

    $pointsAwarded = $quiz['type'] === 'survey'
        ? (int) $quiz['points_per_correct']
        : $correct * (int) $quiz['points_per_correct'];

    db_insert('quiz_attempts', [
        'quiz_id'         => $quizId,
        'student_id'      => $student['id'],
        'correct_count'   => $correct,
        'total_questions' => count($questions),
        'points_awarded'  => $pointsAwarded,
        'answers_json'    => json_encode($answers, JSON_UNESCAPED_UNICODE),
    ]);

    if ($pointsAwarded > 0) {
        points_award((int) $student['entity_id'], [(int) $student['id']], [
            'points'          => $pointsAwarded,
            'reason'          => ($quiz['type'] === 'survey' ? 'استبانة: ' : 'مسابقة: ') . $quiz['title'],
            'awarded_by_name' => 'التصحيح الآلي',
        ]);
    }

    return [
        'correct'        => $correct,
        'total'          => count($questions),
        'points_awarded' => $pointsAwarded,
    ];
}

function quiz_attempts(int $quizId): array
{
    return db_all(
        'SELECT a.*, s.name AS student_name FROM quiz_attempts a
         JOIN students s ON s.id = a.student_id
         WHERE a.quiz_id = ? ORDER BY a.correct_count DESC, a.completed_at',
        [$quizId]
    );
}

// =====================================================================
//  الإحصاءات والتقارير
// =====================================================================

function dashboard_stats(int $entityId): array
{
    $today = today();
    $weekStart = date('Y-m-d 00:00:00', strtotime('-6 days'));

    $trend = [];
    for ($i = 13; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $trend[] = [
            'label' => ar_day_name($day) . ' ' . date('j', strtotime($day)),
            'day'   => $day,
            'value' => (int) db_value(
                'SELECT COALESCE(SUM(points),0) FROM point_transactions WHERE entity_id = ? AND DATE(created_at) = ? AND points > 0',
                [$entityId, $day]
            ),
        ];
    }

    $categories = db_all(
        "SELECT COALESCE(i.category, 'other') AS category, SUM(t.points) AS value
         FROM point_transactions t LEFT JOIN point_items i ON i.id = t.item_id
         WHERE t.entity_id = ? AND t.points > 0
         GROUP BY COALESCE(i.category, 'other') ORDER BY value DESC",
        [$entityId]
    );
    foreach ($categories as $i => $row) {
        $categories[$i]['label'] = label('point_category', $row['category']);
        $categories[$i]['value'] = (int) $row['value'];
    }

    $groupPerformance = db_all(
        'SELECT g.name AS label, COALESCE(SUM(s.total_points), 0) AS value
         FROM student_groups g LEFT JOIN students s ON s.group_id = g.id AND s.is_active = 1
         WHERE g.entity_id = ? GROUP BY g.id, g.name ORDER BY value DESC',
        [$entityId]
    );
    foreach ($groupPerformance as $i => $row) {
        $groupPerformance[$i]['value'] = (int) $row['value'];
    }

    $attendance = attendance_stats($entityId, $today);

    return [
        'students'      => (int) db_value('SELECT COUNT(*) FROM students WHERE entity_id = ? AND is_active = 1', [$entityId]),
        'groups'        => (int) db_value('SELECT COUNT(*) FROM student_groups WHERE entity_id = ? AND is_active = 1', [$entityId]),
        'total_points'  => (int) db_value('SELECT COALESCE(SUM(total_points),0) FROM students WHERE entity_id = ?', [$entityId]),
        'points_today'  => (int) db_value(
            'SELECT COALESCE(SUM(points),0) FROM point_transactions WHERE entity_id = ? AND DATE(created_at) = ? AND points > 0',
            [$entityId, $today]
        ),
        'points_week'   => (int) db_value(
            'SELECT COALESCE(SUM(points),0) FROM point_transactions WHERE entity_id = ? AND created_at >= ? AND points > 0',
            [$entityId, $weekStart]
        ),
        'attendance'    => $attendance,
        'pending_redemptions' => (int) db_value(
            "SELECT COUNT(*) FROM redemptions WHERE entity_id = ? AND status = 'pending'",
            [$entityId]
        ),
        'badges_awarded' => (int) db_value(
            'SELECT COUNT(*) FROM student_badges sb JOIN students s ON s.id = sb.student_id WHERE s.entity_id = ?',
            [$entityId]
        ),
        'active_quizzes' => (int) db_value(
            'SELECT COUNT(*) FROM quizzes WHERE entity_id = ? AND is_published = 1',
            [$entityId]
        ),
        'trend'          => $trend,
        'categories'     => $categories,
        'groups_chart'   => $groupPerformance,
        'top_students'   => leaderboard($entityId, 'all', null, 5),
        'recent'         => transactions_list($entityId, 10),
    ];
}

function reports_data(int $entityId, string $period = 'month'): array
{
    $days = $period === 'week' ? 7 : ($period === 'month' ? 30 : ($period === 'term' ? 120 : 3650));
    $since = date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days'));

    $byGroup = db_all(
        'SELECT g.name AS label,
                COALESCE(SUM(t.points), 0) AS value,
                (SELECT COUNT(*) FROM students s2 WHERE s2.group_id = g.id AND s2.is_active = 1) AS students
         FROM student_groups g
         LEFT JOIN students s ON s.group_id = g.id
         LEFT JOIN point_transactions t ON t.student_id = s.id AND t.created_at >= ?
         WHERE g.entity_id = ?
         GROUP BY g.id, g.name ORDER BY value DESC',
        [$since, $entityId]
    );
    foreach ($byGroup as $i => $row) {
        $students = (int) $row['students'];
        $byGroup[$i]['value'] = (int) $row['value'];
        $byGroup[$i]['average'] = $students > 0 ? (int) round((int) $row['value'] / $students) : 0;
    }

    $attendanceTrend = [];
    for ($i = 13; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $stats = attendance_stats($entityId, $day);
        $attendanceTrend[] = [
            'label' => ar_day_name($day) . ' ' . date('j', strtotime($day)),
            'day'   => $day,
            'value' => $stats['rate'],
        ];
    }

    $categories = db_all(
        "SELECT COALESCE(i.category, 'other') AS category, SUM(t.points) AS value
         FROM point_transactions t LEFT JOIN point_items i ON i.id = t.item_id
         WHERE t.entity_id = ? AND t.points > 0 AND t.created_at >= ?
         GROUP BY COALESCE(i.category, 'other') ORDER BY value DESC",
        [$entityId, $since]
    );
    foreach ($categories as $i => $row) {
        $categories[$i]['label'] = label('point_category', $row['category']);
        $categories[$i]['value'] = (int) $row['value'];
    }

    $topStudents = db_all(
        'SELECT s.name, g.name AS group_name, COALESCE(SUM(t.points), 0) AS value
         FROM students s
         LEFT JOIN student_groups g ON g.id = s.group_id
         JOIN point_transactions t ON t.student_id = s.id AND t.created_at >= ?
         WHERE s.entity_id = ?
         GROUP BY s.id, s.name, g.name ORDER BY value DESC LIMIT 10',
        [$since, $entityId]
    );

    $behavior = db_one(
        'SELECT COALESCE(SUM(CASE WHEN points > 0 THEN points END), 0) AS positive,
                COALESCE(ABS(SUM(CASE WHEN points < 0 THEN points END)), 0) AS negative
         FROM point_transactions WHERE entity_id = ? AND created_at >= ?',
        [$entityId, $since]
    ) ?? ['positive' => 0, 'negative' => 0];

    $monthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthly[] = [
            'label' => ar_month_name($month),
            'value' => (int) db_value(
                "SELECT COALESCE(SUM(points),0) FROM point_transactions
                 WHERE entity_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ? AND points > 0",
                [$entityId, $month]
            ),
        ];
    }

    return [
        'period'           => $period,
        'by_group'         => $byGroup,
        'attendance_trend' => $attendanceTrend,
        'categories'       => $categories,
        'top_students'     => $topStudents,
        'behavior'         => [
            'positive' => (int) $behavior['positive'],
            'negative' => (int) $behavior['negative'],
        ],
        'monthly'          => $monthly,
    ];
}

function ar_month_name(string $ym): string
{
    $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    $index = (int) date('n', strtotime($ym . '-01')) - 1;
    return $months[$index] ?? $ym;
}
