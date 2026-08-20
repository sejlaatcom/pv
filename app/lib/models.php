<?php
/**
 * منطق الأعمال: الجهات، المجموعات، الطلاب، النقاط، الأوسمة، الجوائز.
 */

// =====================================================================
//  الجهات
// =====================================================================

function entity_get(int $id): ?array
{
    return db_one('SELECT * FROM entities WHERE id = ?', [$id]);
}

function entity_create(array $data): int
{
    // أسماء الجهات عربية غالباً، لذا نستخدم اللاتيني إن وُجد وإلا رمزاً عشوائياً
    $ascii = trim((string) preg_replace('/[^a-z0-9]+/i', '-', $data['name']), '-');
    $slug = $data['slug'] ?? (($ascii !== '' ? $ascii . '-' : 'entity-') . random_token(6));
    return db_insert('entities', [
        'name'                => $data['name'],
        'slug'                => mb_strtolower(trim($slug, '-')),
        'type'                => $data['type'] ?? 'school',
        'city'                => $data['city'] ?? null,
        'phone'               => $data['phone'] ?? null,
        'email'               => $data['email'] ?? null,
        'subscription_status' => 'trial',
        'subscription_ends_at' => date('Y-m-d', strtotime('+14 days')),
    ]);
}

/** إنشاء البنود والأوسمة والجوائز الافتراضية لجهة جديدة */
function entity_seed_defaults(int $entityId): void
{
    $items = [
        ['حضور مبكر', 5, 'attendance', '#10b981'],
        ['تسميع متقن', 15, 'memorization', '#0ea5e9'],
        ['حل الواجب كاملاً', 10, 'homework', '#22c55e'],
        ['مشاركة فعّالة', 8, 'participation', '#8b5cf6'],
        ['مساعدة زميل', 6, 'behavior', '#ec4899'],
        ['تفوق في اختبار', 20, 'other', '#f59e0b'],
        ['تأخر عن الطابور', -5, 'penalty', '#ef4444'],
        ['عدم حل الواجب', -8, 'penalty', '#dc2626'],
        ['إزعاج داخل الفصل', -10, 'penalty', '#b91c1c'],
    ];
    foreach ($items as [$title, $points, $category, $color]) {
        db_insert('point_items', [
            'entity_id' => $entityId,
            'title'     => $title,
            'points'    => $points,
            'category'  => $category,
            'color'     => $color,
        ]);
    }

    $badges = [
        ['نجم الأسبوع', 'الأعلى نقاطاً خلال الأسبوع', '⭐', '#f59e0b', null],
        ['المواظب', 'حضور كامل لمدة شهر', '📅', '#10b981', null],
        ['المئوي', 'بلوغ 100 نقطة', '🏅', '#8b5cf6', 100],
        ['الخمسمئة', 'بلوغ 500 نقطة', '🏆', '#ec4899', 500],
        ['الألفي', 'بلوغ 1000 نقطة', '👑', '#eab308', 1000],
    ];
    foreach ($badges as [$title, $desc, $icon, $color, $required]) {
        db_insert('badges', [
            'entity_id'       => $entityId,
            'title'           => $title,
            'description'     => $desc,
            'icon'            => $icon,
            'color'           => $color,
            'required_points' => $required,
        ]);
    }

    $rewards = [
        ['بطاقة شكر وتقدير', 'بطاقة رسمية باسم الطالب', 50, 100, '📜'],
        ['قصة مصورة', 'قصة من مكتبة الجهة', 120, 25, '📚'],
        ['بطاقة ألعاب', 'بطاقة شحن رقمية', 300, 15, '🎮'],
        ['سماعة رأس', 'سماعة سلكية للمذاكرة', 650, 6, '🎧'],
        ['حقيبة مدرسية', 'حقيبة ظهر متينة', 800, 4, '🎒'],
    ];
    foreach ($rewards as [$title, $desc, $cost, $stock, $icon]) {
        db_insert('rewards', [
            'entity_id'   => $entityId,
            'title'       => $title,
            'description' => $desc,
            'cost'        => $cost,
            'stock'       => $stock,
            'icon'        => $icon,
        ]);
    }
}

// =====================================================================
//  المجموعات
// =====================================================================

function groups_list(int $entityId): array
{
    return db_all(
        'SELECT g.*,
                (SELECT COUNT(*) FROM students s WHERE s.group_id = g.id AND s.is_active = 1) AS student_count,
                (SELECT COALESCE(SUM(s.total_points),0) FROM students s WHERE s.group_id = g.id AND s.is_active = 1) AS total_points,
                u.name AS supervisor_name
         FROM student_groups g
         LEFT JOIN users u ON u.id = g.supervisor_id
         WHERE g.entity_id = ?
         ORDER BY g.name',
        [$entityId]
    );
}

function group_get(int $id): ?array
{
    return db_one('SELECT * FROM student_groups WHERE id = ?', [$id]);
}

function group_create(int $entityId, array $data): int
{
    return db_insert('student_groups', [
        'entity_id'     => $entityId,
        'name'          => $data['name'],
        'description'   => ($data['description'] ?? null) ?: null,
        'level'         => ($data['level'] ?? null) ?: null,
        'color'         => ($data['color'] ?? null) ?: '#0d9488',
        'supervisor_id' => ($data['supervisor_id'] ?? null) ?: null,
    ]);
}

// =====================================================================
//  الطلاب
// =====================================================================

function students_list(int $entityId, array $filters = []): array
{
    $sql = 'SELECT s.*, g.name AS group_name
            FROM students s
            LEFT JOIN student_groups g ON g.id = s.group_id
            WHERE s.entity_id = ?';
    $params = [$entityId];

    if (!empty($filters['group_id'])) {
        $sql .= ' AND s.group_id = ?';
        $params[] = (int) $filters['group_id'];
    }
    if (!empty($filters['search'])) {
        $sql .= ' AND (s.name LIKE ? OR s.code LIKE ?)';
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
    }
    if (!empty($filters['only_active'])) {
        $sql .= ' AND s.is_active = 1';
    }

    $sql .= ' ORDER BY s.total_points DESC, s.name';
    return db_all($sql, $params);
}

function student_get(int $id): ?array
{
    return db_one(
        'SELECT s.*, g.name AS group_name
         FROM students s LEFT JOIN student_groups g ON g.id = s.group_id
         WHERE s.id = ?',
        [$id]
    );
}

function student_by_token(string $token): ?array
{
    return db_one(
        'SELECT s.*, g.name AS group_name, e.name AS entity_name, e.type AS entity_type
         FROM students s
         LEFT JOIN student_groups g ON g.id = s.group_id
         JOIN entities e ON e.id = s.entity_id
         WHERE s.access_token = ?',
        [$token]
    );
}

function student_create(int $entityId, array $data): int
{
    return db_insert('students', [
        'entity_id'      => $entityId,
        'group_id'       => $data['group_id'] ?? null ?: null,
        'name'           => $data['name'],
        'code'           => ($data['code'] ?? null) ?: null,
        'gender'         => $data['gender'] ?? 'male',
        'guardian_name'  => ($data['guardian_name'] ?? null) ?: null,
        'guardian_phone' => ($data['guardian_phone'] ?? null) ?: null,
        'access_token'   => $data['access_token'] ?? random_token(16),
        'joined_at'      => $data['joined_at'] ?? today(),
    ]);
}

/** استيراد دفعة طلاب: كل سطر «الاسم، الرقم، جوال ولي الأمر» */
function students_import(int $entityId, string $raw, ?int $groupId): int
{
    $lines = preg_split('/\r\n|\r|\n/', $raw);
    $count = 0;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = array_map('trim', preg_split('/[،,\t;]+/u', $line));
        $name = $parts[0] ?? '';
        if (mb_strlen($name) < 2) {
            continue;
        }
        student_create($entityId, [
            'name'           => $name,
            'code'           => $parts[1] ?? null,
            'guardian_phone' => $parts[2] ?? null,
            'group_id'       => $groupId,
        ]);
        $count++;
    }
    return $count;
}

/** ترتيب الطالب داخل جهته */
function student_rank(array $student): int
{
    return 1 + (int) db_value(
        'SELECT COUNT(*) FROM students WHERE entity_id = ? AND is_active = 1 AND total_points > ?',
        [$student['entity_id'], $student['total_points']]
    );
}

/** ملف الطالب الكامل: نقاط، أوسمة، حضور، جوائز، رسم بياني */
function student_profile(array $student): array
{
    $id = (int) $student['id'];

    $transactions = db_all(
        'SELECT t.*, i.title AS item_title
         FROM point_transactions t LEFT JOIN point_items i ON i.id = t.item_id
         WHERE t.student_id = ? ORDER BY t.created_at DESC LIMIT 50',
        [$id]
    );

    $badges = db_all(
        'SELECT b.*, sb.awarded_at FROM student_badges sb
         JOIN badges b ON b.id = sb.badge_id
         WHERE sb.student_id = ? ORDER BY sb.awarded_at DESC',
        [$id]
    );

    $attendance = db_one(
        "SELECT
            SUM(status = 'present') AS present,
            SUM(status = 'late')    AS late,
            SUM(status = 'absent')  AS absent,
            SUM(status = 'excused') AS excused,
            COUNT(*)                AS total
         FROM attendance WHERE student_id = ?",
        [$id]
    ) ?? [];

    $total = (int) ($attendance['total'] ?? 0);
    $rate = $total > 0
        ? (int) round(((int) $attendance['present'] + (int) $attendance['late']) / $total * 100)
        : 0;

    $redemptions = db_all(
        'SELECT r.*, w.title AS reward_title FROM redemptions r
         JOIN rewards w ON w.id = r.reward_id
         WHERE r.student_id = ? ORDER BY r.created_at DESC',
        [$id]
    );

    $chart = [];
    for ($i = 13; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $chart[] = [
            'label'  => ar_day_name($day),
            'day'    => $day,
            'value'  => (int) db_value(
                'SELECT COALESCE(SUM(points),0) FROM point_transactions WHERE student_id = ? AND DATE(created_at) = ?',
                [$id, $day]
            ),
        ];
    }

    return [
        'transactions'     => $transactions,
        'badges'           => $badges,
        'attendance'       => [
            'present' => (int) ($attendance['present'] ?? 0),
            'late'    => (int) ($attendance['late'] ?? 0),
            'absent'  => (int) ($attendance['absent'] ?? 0),
            'excused' => (int) ($attendance['excused'] ?? 0),
            'rate'    => $rate,
        ],
        'redemptions'      => $redemptions,
        'rank'             => student_rank($student),
        'total_students'   => (int) db_value('SELECT COUNT(*) FROM students WHERE entity_id = ? AND is_active = 1', [$student['entity_id']]),
        'points_week'      => (int) db_value(
            'SELECT COALESCE(SUM(points),0) FROM point_transactions WHERE student_id = ? AND created_at >= ?',
            [$id, date('Y-m-d 00:00:00', strtotime('-6 days'))]
        ),
        'points_month'     => (int) db_value(
            'SELECT COALESCE(SUM(points),0) FROM point_transactions WHERE student_id = ? AND created_at >= ?',
            [$id, date('Y-m-d 00:00:00', strtotime('-29 days'))]
        ),
        'chart'            => $chart,
    ];
}

// =====================================================================
//  بنود النقاط ورصدها
// =====================================================================

function point_items_list(int $entityId, bool $onlyActive = false): array
{
    $sql = 'SELECT * FROM point_items WHERE entity_id = ?';
    if ($onlyActive) {
        $sql .= ' AND is_active = 1';
    }
    return db_all($sql . ' ORDER BY points DESC', [$entityId]);
}

/**
 * رصد نقاط لمجموعة من الطلاب.
 * يعيد عدد الطلاب وقيمة النقاط المرصودة لكل طالب.
 */
function points_award(int $entityId, array $studentIds, array $options): array
{
    $studentIds = array_values(array_unique(array_map('intval', $studentIds)));
    if (!$studentIds) {
        throw new InvalidArgumentException('اختر طالباً واحداً على الأقل');
    }

    $itemId = $options['item_id'] ?? null;
    $points = isset($options['points']) && $options['points'] !== '' ? (int) $options['points'] : null;
    $reason = $options['reason'] ?? '';

    if ($itemId) {
        $item = db_one('SELECT * FROM point_items WHERE id = ? AND entity_id = ?', [$itemId, $entityId]);
        if (!$item) {
            throw new InvalidArgumentException('البند غير موجود');
        }
        $points = $points ?? (int) $item['points'];
        $reason = $reason !== '' ? $reason : $item['title'];
    }

    if (!$points) {
        throw new InvalidArgumentException('اختر بنداً جاهزاً أو أدخل قيمة النقاط');
    }

    // التأكد أن جميع الطلاب يتبعون هذه الجهة
    $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
    $valid = db_all(
        "SELECT id FROM students WHERE entity_id = ? AND id IN ($placeholders)",
        array_merge([$entityId], $studentIds)
    );
    $validIds = array_map(fn($r) => (int) $r['id'], $valid);
    if (!$validIds) {
        throw new InvalidArgumentException('لا يوجد طلاب صالحون للرصد');
    }

    $type = $points >= 0 ? 'earn' : 'deduct';
    $user = current_user();

    db()->beginTransaction();
    try {
        foreach ($validIds as $studentId) {
            db_insert('point_transactions', [
                'entity_id'       => $entityId,
                'student_id'      => $studentId,
                'item_id'         => $itemId ?: null,
                'points'          => $points,
                'type'            => $options['type'] ?? $type,
                'reason'          => $reason ?: null,
                'awarded_by'      => $user['id'] ?? null,
                'awarded_by_name' => $options['awarded_by_name'] ?? ($user['name'] ?? 'النظام'),
            ]);
        }
        $in = implode(',', array_fill(0, count($validIds), '?'));
        db_run(
            "UPDATE students SET total_points = total_points + ? WHERE id IN ($in)",
            array_merge([$points], $validIds)
        );
        db()->commit();
    } catch (Throwable $e) {
        db()->rollBack();
        throw $e;
    }

    badges_auto_grant($entityId, $validIds);

    return ['count' => count($validIds), 'points' => $points];
}

function transactions_list(int $entityId, int $limit = 20): array
{
    $limit = max(1, min(200, $limit));
    return db_all(
        "SELECT t.*, s.name AS student_name
         FROM point_transactions t JOIN students s ON s.id = t.student_id
         WHERE t.entity_id = ?
         ORDER BY t.created_at DESC, t.id DESC LIMIT $limit",
        [$entityId]
    );
}

// =====================================================================
//  لوحة المتصدرين
// =====================================================================

/** الفترات المدعومة: week | month | term | all */
function leaderboard(int $entityId, string $period = 'all', ?int $groupId = null, int $limit = 50): array
{
    $limit = max(1, min(500, $limit));

    if ($period === 'all') {
        $sql = 'SELECT s.id, s.name, s.code, s.group_id, g.name AS group_name, s.total_points AS points,
                       (SELECT COUNT(*) FROM student_badges sb WHERE sb.student_id = s.id) AS badge_count
                FROM students s LEFT JOIN student_groups g ON g.id = s.group_id
                WHERE s.entity_id = ? AND s.is_active = 1';
        $params = [$entityId];
        if ($groupId) {
            $sql .= ' AND s.group_id = ?';
            $params[] = $groupId;
        }
        $sql .= " ORDER BY points DESC, s.name LIMIT $limit";
    } else {
        $days = $period === 'week' ? 7 : ($period === 'month' ? 30 : 120);
        $since = date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days'));
        $sql = 'SELECT s.id, s.name, s.code, s.group_id, g.name AS group_name,
                       COALESCE(SUM(t.points), 0) AS points,
                       (SELECT COUNT(*) FROM student_badges sb WHERE sb.student_id = s.id) AS badge_count
                FROM students s
                LEFT JOIN student_groups g ON g.id = s.group_id
                LEFT JOIN point_transactions t ON t.student_id = s.id AND t.created_at >= ?
                WHERE s.entity_id = ? AND s.is_active = 1';
        $params = [$since, $entityId];
        if ($groupId) {
            $sql .= ' AND s.group_id = ?';
            $params[] = $groupId;
        }
        $sql .= " GROUP BY s.id, s.name, s.code, s.group_id, g.name ORDER BY points DESC, s.name LIMIT $limit";
    }

    $rows = db_all($sql, $params);
    foreach ($rows as $i => $row) {
        $rows[$i]['rank'] = $i + 1;
        $rows[$i]['points'] = (int) $row['points'];
    }
    return $rows;
}

// =====================================================================
//  الأوسمة
// =====================================================================

function badges_list(int $entityId): array
{
    return db_all(
        'SELECT b.*, (SELECT COUNT(*) FROM student_badges sb WHERE sb.badge_id = b.id) AS awarded_count
         FROM badges b WHERE b.entity_id = ? ORDER BY b.required_points IS NULL DESC, b.required_points',
        [$entityId]
    );
}

function badge_award(int $studentId, int $badgeId, ?int $userId = null): bool
{
    $exists = db_value('SELECT id FROM student_badges WHERE student_id = ? AND badge_id = ?', [$studentId, $badgeId]);
    if ($exists) {
        return false;
    }
    db_insert('student_badges', [
        'student_id' => $studentId,
        'badge_id'   => $badgeId,
        'awarded_by' => $userId,
    ]);
    return true;
}

/** منح الأوسمة التي لها حد نقاط تلقائياً */
function badges_auto_grant(int $entityId, array $studentIds): void
{
    $badges = db_all(
        'SELECT * FROM badges WHERE entity_id = ? AND is_active = 1 AND required_points IS NOT NULL',
        [$entityId]
    );
    if (!$badges) {
        return;
    }
    foreach ($studentIds as $studentId) {
        $points = (int) db_value('SELECT total_points FROM students WHERE id = ?', [$studentId], 0);
        foreach ($badges as $badge) {
            if ($points >= (int) $badge['required_points']) {
                badge_award((int) $studentId, (int) $badge['id']);
            }
        }
    }
}

// =====================================================================
//  متجر الجوائز
// =====================================================================

function rewards_list(int $entityId, bool $onlyActive = false): array
{
    $sql = 'SELECT * FROM rewards WHERE entity_id = ?';
    if ($onlyActive) {
        $sql .= ' AND is_active = 1';
    }
    return db_all($sql . ' ORDER BY cost', [$entityId]);
}

function redemptions_list(int $entityId, ?string $status = null): array
{
    $sql = 'SELECT r.*, s.name AS student_name, w.title AS reward_title
            FROM redemptions r
            JOIN students s ON s.id = r.student_id
            JOIN rewards w ON w.id = r.reward_id
            WHERE r.entity_id = ?';
    $params = [$entityId];
    if ($status) {
        $sql .= ' AND r.status = ?';
        $params[] = $status;
    }
    return db_all($sql . ' ORDER BY r.created_at DESC', $params);
}

/** طلب استبدال من الطالب (عبر بوابته) */
function redemption_request(array $student, int $rewardId): void
{
    $reward = db_one('SELECT * FROM rewards WHERE id = ? AND entity_id = ?', [$rewardId, $student['entity_id']]);
    if (!$reward || !$reward['is_active']) {
        throw new RuntimeException('الجائزة غير متاحة');
    }
    if ((int) $reward['stock'] <= 0) {
        throw new RuntimeException('نفدت الكمية من هذه الجائزة');
    }
    if ((int) $student['total_points'] < (int) $reward['cost']) {
        throw new RuntimeException('رصيد النقاط لا يكفي لاستبدال هذه الجائزة');
    }
    $pending = db_value(
        "SELECT id FROM redemptions WHERE student_id = ? AND reward_id = ? AND status = 'pending'",
        [$student['id'], $rewardId]
    );
    if ($pending) {
        throw new RuntimeException('لديك طلب سابق على هذه الجائزة بانتظار الاعتماد');
    }

    db_insert('redemptions', [
        'entity_id'  => $student['entity_id'],
        'student_id' => $student['id'],
        'reward_id'  => $rewardId,
        'cost'       => $reward['cost'],
        'status'     => 'pending',
    ]);
}

/** تغيير حالة الطلب: يُخصم الرصيد وتنقص الكمية عند الاعتماد فقط */
function redemption_set_status(int $entityId, int $redemptionId, string $status): void
{
    $redemption = db_one('SELECT * FROM redemptions WHERE id = ? AND entity_id = ?', [$redemptionId, $entityId]);
    if (!$redemption) {
        throw new RuntimeException('الطلب غير موجود');
    }
    if ($redemption['status'] === $status) {
        return;
    }

    $wasCharged  = in_array($redemption['status'], ['approved', 'delivered'], true);
    $willCharge  = in_array($status, ['approved', 'delivered'], true);

    // لا يُعتمد الطلب إذا نقص رصيد الطالب بعد تقديمه (رصد سلبي أو استبدال آخر)
    if (!$wasCharged && $willCharge) {
        $currentPoints = (int) db_value('SELECT total_points FROM students WHERE id = ?', [$redemption['student_id']], 0);
        if ($currentPoints < (int) $redemption['cost']) {
            throw new RuntimeException(
                'رصيد الطالب الحالي (' . $currentPoints . ') لا يكفي لتكلفة الجائزة (' . (int) $redemption['cost'] . ')'
            );
        }
    }

    db_update('redemptions', $redemptionId, [
        'status'     => $status,
        'handled_at' => date('Y-m-d H:i:s'),
    ]);

    $reward = db_one('SELECT * FROM rewards WHERE id = ?', [$redemption['reward_id']]);

    // خصم النقاط ونقص المخزون عند أول اعتماد
    if (!$wasCharged && $willCharge) {
        points_award($entityId, [(int) $redemption['student_id']], [
            'points'          => -1 * (int) $redemption['cost'],
            'reason'          => 'استبدال جائزة: ' . ($reward['title'] ?? ''),
            'type'            => 'redeem',
            'awarded_by_name' => 'متجر الجوائز',
        ]);
        if ($reward) {
            db_update('rewards', (int) $reward['id'], ['stock' => max(0, (int) $reward['stock'] - 1)]);
        }
    }

    // إعادة النقاط والمخزون عند التراجع عن طلب سبق اعتماده
    if ($wasCharged && !$willCharge) {
        points_award($entityId, [(int) $redemption['student_id']], [
            'points'          => (int) $redemption['cost'],
            'reason'          => 'إلغاء استبدال: ' . ($reward['title'] ?? ''),
            'type'            => 'adjust',
            'awarded_by_name' => 'متجر الجوائز',
        ]);
        if ($reward) {
            db_update('rewards', (int) $reward['id'], ['stock' => (int) $reward['stock'] + 1]);
        }
    }
}
