<?php
/**
 * بيانات تجريبية جاهزة لتجربة المنصة (تُستدعى من install.php --demo).
 * تُنشئ جهة كاملة: مجموعات، طلاب، بنود، نقاط، حضور، مهام، مسابقات، جوائز.
 */

function seed_demo_data(): array
{
    mt_srand(20260820); // بيانات ثابتة عند كل تشغيل

    $entityId = entity_create([
        'name'  => 'مجمع الأندلس التعليمي',
        'slug'  => 'andalus',
        'type'  => 'school',
        'city'  => 'الرياض',
        'phone' => '0551234567',
        'email' => 'info@andalus.example',
    ]);
    db_update('entities', $entityId, [
        'subscription_status'  => 'active',
        'subscription_ends_at' => date('Y-m-d', strtotime('+1 year')),
    ]);
    entity_seed_defaults($entityId);

    // ===== المستخدمون =====
    $adminId = db_insert('users', [
        'entity_id'     => $entityId,
        'name'          => 'أ. سامي القحطاني',
        'email'         => 'admin@demo.local',
        'phone'         => '0551234567',
        'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
        'role'          => 'admin',
    ]);
    $supervisorId = db_insert('users', [
        'entity_id'     => $entityId,
        'name'          => 'أ. ريم العتيبي',
        'email'         => 'supervisor@demo.local',
        'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
        'role'          => 'supervisor',
    ]);

    // ===== المجموعات =====
    $groupIds = [];
    foreach ([
        ['أول متوسط - أ', 'الفصل الدراسي الأول', 'متوسط', '#0ea5e9'],
        ['ثاني متوسط - ب', 'الفصل الدراسي الأول', 'متوسط', '#8b5cf6'],
        ['حلقة الفرقان', 'حلقة تحفيظ مسائية', 'تحفيظ', '#10b981'],
        ['فريق كرة القدم', 'التدريب الرياضي', 'نشاط', '#f59e0b'],
    ] as $i => [$name, $description, $level, $color]) {
        $groupIds[] = db_insert('student_groups', [
            'entity_id'     => $entityId,
            'name'          => $name,
            'description'   => $description,
            'level'         => $level,
            'color'         => $color,
            'supervisor_id' => $i % 2 === 0 ? $adminId : $supervisorId,
        ]);
    }

    // ===== الطلاب =====
    $names = [
        'عبدالله الحربي', 'محمد العتيبي', 'سلطان القحطاني', 'فيصل الشمري', 'أنس الزهراني',
        'يوسف الغامدي', 'خالد الدوسري', 'ريان المطيري', 'عمر السبيعي', 'تركي البقمي',
        'بندر الشهري', 'ماجد العمري', 'زياد الخالدي', 'نواف الرشيدي', 'سعود الجهني',
        'إبراهيم الأنصاري', 'حمزة الثبيتي', 'طلال الحارثي', 'نورة السالم', 'لمى العنزي',
        'جوري الفهد', 'رغد الشمري', 'سارة المالكي', 'دانة القحطاني', 'شهد الحربي',
        'ريما العتيبي', 'أروى الزهراني', 'هيا الدوسري',
    ];

    $studentIds = [];
    foreach ($names as $i => $name) {
        $studentIds[] = db_insert('students', [
            'entity_id'      => $entityId,
            'group_id'       => $groupIds[$i % count($groupIds)],
            'name'           => $name,
            'code'           => 'ST-' . (1001 + $i),
            'gender'         => $i < 18 ? 'male' : 'female',
            'guardian_name'  => 'والد ' . explode(' ', $name)[0],
            'guardian_phone' => '05' . str_pad((string) mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            'access_token'   => random_token(16),
            'joined_at'      => date('Y-m-d', strtotime('-' . (180 - $i) . ' days')),
        ]);
    }

    // ===== حركات النقاط خلال 45 يوماً =====
    $items = point_items_list($entityId);
    $supervisorNames = ['أ. سامي القحطاني', 'أ. ريم العتيبي', 'أ. خالد الشهري'];
    $totals = array_fill_keys($studentIds, 0);

    for ($day = 45; $day >= 0; $day--) {
        $date = strtotime("-$day days");
        $weekday = (int) date('w', $date);
        // الجمعة والسبت إجازة، ما عدا اليوم الحالي حتى تظهر اللوحة بمؤشرات حيّة
        if (($weekday === 5 || $weekday === 6) && $day !== 0) {
            continue;
        }
        foreach ($studentIds as $studentId) {
            $events = mt_rand(0, 2);
            for ($e = 0; $e < $events; $e++) {
                $item = $items[array_rand($items)];
                $points = (int) $item['points'];
                $timestamp = date('Y-m-d H:i:s', $date + mt_rand(8, 13) * 3600 + mt_rand(0, 59) * 60);
                db_insert('point_transactions', [
                    'entity_id'       => $entityId,
                    'student_id'      => $studentId,
                    'item_id'         => $item['id'],
                    'points'          => $points,
                    'type'            => $points >= 0 ? 'earn' : 'deduct',
                    'reason'          => $item['title'],
                    'awarded_by'      => $adminId,
                    'awarded_by_name' => $supervisorNames[array_rand($supervisorNames)],
                    'created_at'      => $timestamp,
                ]);
                $totals[$studentId] += $points;
            }
        }
    }
    foreach ($totals as $studentId => $total) {
        db_update('students', (int) $studentId, ['total_points' => $total]);
    }

    // ===== الحضور خلال 20 يوماً =====
    for ($day = 20; $day >= 0; $day--) {
        $date = strtotime("-$day days");
        $weekday = (int) date('w', $date);
        if (($weekday === 5 || $weekday === 6) && $day !== 0) {
            continue;
        }
        foreach ($studentIds as $index => $studentId) {
            $roll = mt_rand(1, 100);
            $status = $roll > 14 ? 'present' : ($roll > 8 ? 'late' : ($roll > 3 ? 'absent' : 'excused'));
            db_insert('attendance', [
                'entity_id'  => $entityId,
                'group_id'   => $groupIds[$index % count($groupIds)],
                'student_id' => $studentId,
                'day'        => date('Y-m-d', $date),
                'status'     => $status,
                'check_in'   => $status === 'absent' ? null : ($status === 'late' ? '07:35' : '07:00'),
                'check_out'  => $status === 'absent' ? null : '13:00',
                'recorded_by' => $adminId,
            ]);
        }
    }

    // ===== المهام =====
    foreach ([
        ['حفظ سورة الملك', 'حفظ السورة كاملة مع التجويد', 3, 10, 20],
        ['ورقة عمل الرياضيات', 'حل تمارين الوحدة الثالثة', 6, 10, 10],
        ['تلخيص قصة قصيرة', 'تلخيص في صفحة واحدة', 9, 10, 12],
    ] as $i => [$title, $description, $dueIn, $maxScore, $points]) {
        $taskId = task_create($entityId, [
            'title'       => $title,
            'description' => $description,
            'group_id'    => $groupIds[$i % count($groupIds)],
            'due_date'    => date('Y-m-d', strtotime("+$dueIn days")),
            'max_score'   => $maxScore,
            'points'      => $points,
        ], $adminId);

        foreach (task_submissions($taskId) as $submission) {
            $roll = mt_rand(1, 100);
            if ($roll > 55) {
                db_update('task_submissions', (int) $submission['id'], [
                    'status'       => 'submitted',
                    'submitted_at' => date('Y-m-d H:i:s', strtotime('-' . mt_rand(1, 4) . ' days')),
                ]);
            }
        }
    }

    // ===== المسابقات =====
    $quizBank = [
        [
            'مسابقة السيرة النبوية', 'شرعي', 'أسئلة مختارة في سيرة النبي صلى الله عليه وسلم', true,
            [
                ['في أي عام هجري كانت غزوة بدر؟', ['السنة الأولى', 'السنة الثانية', 'السنة الثالثة', 'السنة الخامسة'], 1],
                ['من أول من أسلم من الرجال؟', ['أبو بكر الصديق', 'عمر بن الخطاب', 'علي بن أبي طالب', 'عثمان بن عفان'], 0],
                ['كم عدد سنوات الدعوة في مكة؟', ['عشر سنوات', 'ثلاث عشرة سنة', 'خمس عشرة سنة', 'ثماني سنوات'], 1],
            ],
        ],
        [
            'مسابقة الرياضيات الذهنية', 'رياضيات', 'عمليات حسابية سريعة لقياس سرعة البديهة', true,
            [
                ['كم ناتج ٧ × ٨ ؟', ['54', '56', '58', '64'], 1],
                ['ما هو العدد الأولي؟', ['9', '15', '17', '21'], 2],
                ['كم ناتج ١٤٤ ÷ ١٢ ؟', ['10', '11', '12', '14'], 2],
            ],
        ],
        [
            'مسابقة اللغة العربية', 'لغة عربية', 'نحو وإملاء ومفردات', true,
            [
                ['ما إعراب كلمة (العلمُ) في: العلمُ نورٌ؟', ['مبتدأ مرفوع', 'خبر مرفوع', 'فاعل مرفوع', 'مفعول به'], 0],
                ['جمع كلمة (كتاب) هو؟', ['كتابات', 'كُتُب', 'مكاتب', 'كاتبون'], 1],
                ['الهمزة في كلمة (استخراج) همزة؟', ['قطع', 'وصل', 'متطرفة', 'متوسطة'], 1],
            ],
        ],
        [
            'استبانة رضا المشاركين', 'استبانة', 'قياس رضا الطلاب عن البرنامج', false,
            [
                ['ما مدى رضاك عن الأنشطة؟', ['ممتاز', 'جيد جداً', 'جيد', 'يحتاج تحسين'], 0],
                ['هل النقاط والجوائز محفّزة لك؟', ['نعم كثيراً', 'إلى حد ما', 'قليلاً', 'لا'], 0],
            ],
        ],
    ];

    foreach ($quizBank as [$title, $category, $description, $published, $questions]) {
        $prepared = [];
        foreach ($questions as [$text, $options, $correct]) {
            $prepared[] = ['question' => $text, 'options' => $options, 'correct' => $correct];
        }
        $quizId = quiz_create($entityId, [
            'title'              => $title,
            'description'        => $description,
            'type'               => $category === 'استبانة' ? 'survey' : 'quiz',
            'category'           => $category,
            'points_per_correct' => 5,
            'time_limit_minutes' => $category === 'استبانة' ? null : 10,
        ], $prepared, $adminId);

        if ($published) {
            db_update('quizzes', $quizId, ['is_published' => 1]);
        }
    }

    // ===== الأوسمة التلقائية وطلبات الاستبدال =====
    badges_auto_grant($entityId, $studentIds);

    $topStudents = leaderboard($entityId, 'all', null, 4);
    $rewards = rewards_list($entityId);
    foreach ($topStudents as $i => $row) {
        $reward = $rewards[$i % count($rewards)];
        if ((int) $reward['cost'] > (int) $row['points']) {
            continue;
        }
        db_insert('redemptions', [
            'entity_id'  => $entityId,
            'student_id' => $row['id'],
            'reward_id'  => $reward['id'],
            'cost'       => $reward['cost'],
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s', strtotime('-' . ($i + 1) . ' days')),
        ]);
    }

    db_insert('contact_messages', [
        'name'        => 'أحمد الشهري',
        'email'       => 'ahmad@example.com',
        'phone'       => '0555555555',
        'entity_type' => 'حلقة تحفيظ',
        'subject'     => 'طلب عرض تقديمي',
        'message'     => 'السلام عليكم، نرغب في تفعيل المنصة لحلقات التحفيظ لدينا. كم عدد الحلقات المسموح بها؟',
    ]);

    return [
        'entity_id'   => $entityId,
        'students'    => count($studentIds),
        'admin_email' => 'admin@demo.local',
        'password'    => '123456',
        'sample_link' => '/p/' . db_value('SELECT access_token FROM students ORDER BY total_points DESC LIMIT 1'),
    ];
}
