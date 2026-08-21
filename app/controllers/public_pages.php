<?php
/** الصفحات العامة للجهة: التسجيل الذاتي ولوحة الشرف. */

function entity_by_slug(string $slug): array
{
    $entity = db_one('SELECT * FROM entities WHERE slug = ?', [$slug]);
    if (!$entity) {
        http_response_code(404);
        view('errors/404', ['message' => 'الجهة غير موجودة'], 'site');
        exit;
    }
    return $entity;
}

/** صفحة التسجيل الذاتي للطالب: /join/{slug} */
function join_page(string $slug, array $old = [], ?string $error = null): void
{
    $entity = entity_by_slug($slug);

    if (!$entity['allow_self_register']) {
        http_response_code(403);
        view('errors/404', ['message' => 'رابط التسجيل مغلق حالياً لهذه الجهة'], 'site');
        exit;
    }

    view('site/join', [
        'title'  => 'التسجيل في ' . $entity['name'],
        'entity' => $entity,
        'groups' => db_all('SELECT id, name FROM student_groups WHERE entity_id = ? AND is_active = 1 ORDER BY name', [$entity['id']]),
        'old'    => $old,
        'error'  => $error,
    ], 'site');
}

function join_submit(string $slug): void
{
    $entity = entity_by_slug($slug);

    if (!$entity['allow_self_register']) {
        http_response_code(403);
        exit('رابط التسجيل مغلق');
    }

    $data = [
        'name'           => input('name', ''),
        'group_id'       => input_int('group_id'),
        'code'           => input('code', ''),
        'guardian_phone' => input('guardian_phone', ''),
        'gender'         => input('gender', 'male') === 'female' ? 'female' : 'male',
    ];

    if (mb_strlen($data['name']) < 3) {
        join_page($slug, $data, 'الرجاء كتابة الاسم الثلاثي');
        return;
    }

    // منع تكرار التسجيل بنفس الاسم والجوال
    $duplicate = db_value(
        'SELECT id FROM students WHERE entity_id = ? AND name = ? AND IFNULL(guardian_phone, "") = ?',
        [$entity['id'], $data['name'], $data['guardian_phone']]
    );
    if ($duplicate) {
        $student = student_get((int) $duplicate);
        view('site/join_done', [
            'title'   => 'تم التسجيل مسبقاً',
            'entity'  => $entity,
            'student' => $student,
            'repeat'  => true,
        ], 'site');
        return;
    }

    // التأكد أن المجموعة المختارة تتبع الجهة نفسها
    if ($data['group_id']) {
        $group = db_one('SELECT id FROM student_groups WHERE id = ? AND entity_id = ?', [$data['group_id'], $entity['id']]);
        if (!$group) {
            $data['group_id'] = null;
        }
    }

    $studentId = student_create((int) $entity['id'], $data);

    view('site/join_done', [
        'title'   => 'تم التسجيل بنجاح',
        'entity'  => $entity,
        'student' => student_get($studentId),
        'repeat'  => false,
    ], 'site');
}

/** لوحة شرف عامة للجهة: /e/{slug} */
function public_board_page(string $slug): void
{
    $entity = entity_by_slug($slug);

    if (!$entity['public_board']) {
        http_response_code(403);
        view('errors/404', ['message' => 'لوحة الشرف غير متاحة للعرض العام'], 'site');
        exit;
    }

    $entityId = (int) $entity['id'];
    $period = input('period', 'month');
    if (!in_array($period, ['week', 'month', 'term', 'all'], true)) {
        $period = 'month';
    }

    view('site/public_board', [
        'title'   => 'لوحة شرف ' . $entity['name'],
        'entity'  => $entity,
        'period'  => $period,
        'rows'    => leaderboard($entityId, $period, null, 20),
        'stats'   => [
            'students' => (int) db_value('SELECT COUNT(*) FROM students WHERE entity_id = ? AND is_active = 1', [$entityId]),
            'points'   => (int) db_value('SELECT COALESCE(SUM(total_points),0) FROM students WHERE entity_id = ?', [$entityId]),
            'badges'   => (int) db_value(
                'SELECT COUNT(*) FROM student_badges sb JOIN students s ON s.id = sb.student_id WHERE s.entity_id = ?',
                [$entityId]
            ),
            'groups'   => (int) db_value('SELECT COUNT(*) FROM student_groups WHERE entity_id = ?', [$entityId]),
        ],
    ], 'site');
}
