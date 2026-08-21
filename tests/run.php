<?php
/**
 * اختبارات ذاتية لمنطق المنصة — تُشغَّل من الطرفية:
 *
 *   php tests/run.php
 *
 * تنشئ جهة اختبار مؤقتة داخل قاعدة البيانات ثم تحذفها بالكامل في النهاية،
 * ولا تمسّ بيانات الجهات الحقيقية.
 */

if (PHP_SAPI !== 'cli') {
    exit('هذا الملف يعمل من سطر الأوامر فقط.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

$passed = 0;
$failed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  ✅ $label\n";
    } else {
        $failed++;
        echo "  ❌ $label\n";
    }
}

function throws(string $label, callable $callback, string $expect = ''): void
{
    try {
        $callback();
        check($label, false);
    } catch (Throwable $e) {
        check($label . ($expect ? " ($expect)" : ''), $expect === '' || str_contains($e->getMessage(), $expect));
    }
}

if (!db_is_installed()) {
    exit("✗ قاعدة البيانات غير مهيأة. شغّل: php database/install.php\n");
}

echo "== اختبارات منصة " . brand('name') . " ==\n\n";

// ===== تهيئة جهة اختبار =====
$entityId = entity_create(['name' => 'جهة اختبار مؤقتة', 'type' => 'school']);
entity_seed_defaults($entityId);

$groupId = group_create($entityId, ['name' => 'مجموعة اختبار', 'description' => '', 'level' => '', 'color' => '', 'supervisor_id' => null]);
$ali   = student_create($entityId, ['name' => 'علي التجريبي', 'group_id' => $groupId, 'code' => 'X-1']);
$sara  = student_create($entityId, ['name' => 'سارة التجريبية', 'group_id' => $groupId, 'code' => 'X-2']);

try {
    echo "رصد النقاط:\n";
    points_award($entityId, [$ali], ['points' => 30, 'reason' => 'اختبار']);
    check('يزيد رصيد الطالب بعد الرصد', (int) student_get($ali)['total_points'] === 30);
    check('تُسجَّل حركة النقاط', (int) db_value('SELECT COUNT(*) FROM point_transactions WHERE student_id = ?', [$ali]) === 1);

    points_award($entityId, [$ali, $sara], ['points' => 10, 'reason' => 'رصد جماعي']);
    check('الرصد الجماعي يشمل الجميع',
        (int) student_get($ali)['total_points'] === 40 && (int) student_get($sara)['total_points'] === 10);

    $penalty = db_one('SELECT * FROM point_items WHERE entity_id = ? AND points < 0 LIMIT 1', [$entityId]);
    points_award($entityId, [$sara], ['item_id' => (int) $penalty['id']]);
    check('البند السالب يخصم من الرصيد',
        (int) student_get($sara)['total_points'] === 10 + (int) $penalty['points']);

    throws('رفض الرصد بدون قيمة نقاط', fn() => points_award($entityId, [$ali], []));
    throws('رفض الرصد بدون طلاب', fn() => points_award($entityId, [], ['points' => 5]));

    echo "\nالأوسمة:\n";
    points_award($entityId, [$ali], ['points' => 100, 'reason' => 'قفزة نقاط']);
    $milestone = db_one('SELECT * FROM badges WHERE entity_id = ? AND required_points = 100', [$entityId]);
    check('منح الوسام تلقائياً عند بلوغ الحد',
        (int) db_value('SELECT COUNT(*) FROM student_badges WHERE student_id = ? AND badge_id = ?', [$ali, $milestone['id']]) === 1);
    check('عدم تكرار الوسام نفسه', badge_award($ali, (int) $milestone['id']) === false);

    echo "\nلوحة المتصدرين:\n";
    $board = leaderboard($entityId, 'all');
    check('الترتيب تنازلي حسب النقاط', (int) $board[0]['id'] === $ali && (int) $board[0]['rank'] === 1);
    $weekly = leaderboard($entityId, 'week');
    check('حساب نقاط الفترة الأسبوعية', count($weekly) === 2);

    echo "\nمتجر الجوائز:\n";
    $reward = db_one('SELECT * FROM rewards WHERE entity_id = ? ORDER BY cost LIMIT 1', [$entityId]);
    throws('رفض الاستبدال عند نقص الرصيد',
        fn() => redemption_request(student_get($sara), (int) $reward['id']), 'رصيد');

    redemption_request(student_get($ali), (int) $reward['id']);
    $redemptionId = (int) db_value('SELECT id FROM redemptions WHERE student_id = ? ORDER BY id DESC LIMIT 1', [$ali]);
    check('إنشاء طلب الاستبدال بحالة معلّقة',
        db_value('SELECT status FROM redemptions WHERE id = ?', [$redemptionId]) === 'pending');

    $before = (int) student_get($ali)['total_points'];
    $stockBefore = (int) db_value('SELECT stock FROM rewards WHERE id = ?', [$reward['id']]);
    redemption_set_status($entityId, $redemptionId, 'approved');
    check('خصم النقاط عند الاعتماد',
        (int) student_get($ali)['total_points'] === $before - (int) $reward['cost']);
    check('نقص المخزون عند الاعتماد',
        (int) db_value('SELECT stock FROM rewards WHERE id = ?', [$reward['id']]) === $stockBefore - 1);

    redemption_set_status($entityId, $redemptionId, 'delivered');
    check('عدم تكرار الخصم عند التسليم',
        (int) student_get($ali)['total_points'] === $before - (int) $reward['cost']);

    redemption_set_status($entityId, $redemptionId, 'rejected');
    check('إعادة النقاط عند التراجع عن الاعتماد', (int) student_get($ali)['total_points'] === $before);
    check('إعادة المخزون عند التراجع',
        (int) db_value('SELECT stock FROM rewards WHERE id = ?', [$reward['id']]) === $stockBefore);

    echo "\nالحضور:\n";
    $day = today();
    attendance_save($entityId, $day, [$ali => 'present', $sara => 'absent']);
    attendance_save($entityId, $day, [$ali => 'late']);
    check('تحديث حالة الحضور بدل تكرار السجل',
        (int) db_value('SELECT COUNT(*) FROM attendance WHERE student_id = ? AND day = ?', [$ali, $day]) === 1
        && db_value('SELECT status FROM attendance WHERE student_id = ? AND day = ?', [$ali, $day]) === 'late');
    $stats = attendance_stats($entityId, $day);
    check('حساب نسبة الحضور اليومية', $stats['total'] === 2 && $stats['rate'] === 50);

    echo "\nالمهام:\n";
    $taskId = task_create($entityId, ['title' => 'مهمة اختبار', 'description' => '', 'group_id' => $groupId, 'due_date' => null, 'max_score' => 10, 'points' => 20]);
    $submissions = task_submissions($taskId);
    check('توليد سجل تسليم لكل طالب في المجموعة', count($submissions) === 2);

    $pointsBefore = (int) student_get($ali)['total_points'];
    $aliSubmission = current(array_filter($submissions, fn($s) => (int) $s['student_id'] === $ali));
    task_grade($entityId, (int) $aliSubmission['id'], 10);
    check('منح نقاط المهمة عند الدرجة الكاملة',
        (int) student_get($ali)['total_points'] === $pointsBefore + 20);

    $saraSubmission = current(array_filter($submissions, fn($s) => (int) $s['student_id'] === $sara));
    $saraBefore = (int) student_get($sara)['total_points'];
    task_grade($entityId, (int) $saraSubmission['id'], 2);
    check('عدم منح نقاط عند درجة أقل من النصف',
        (int) student_get($sara)['total_points'] === $saraBefore);

    echo "\nالمسابقات والتصحيح الآلي:\n";
    $quizId = quiz_create($entityId, ['title' => 'مسابقة اختبار', 'description' => '', 'type' => 'quiz', 'category' => 'عام', 'points_per_correct' => 5, 'time_limit_minutes' => null], [
        ['question' => 'ما عاصمة السعودية؟', 'options' => ['الرياض', 'جدة', 'الدمام'], 'correct' => 0],
        ['question' => 'كم عدد أيام الأسبوع؟', 'options' => ['5', '6', '7'], 'correct' => 2],
    ]);

    throws('منع المشاركة قبل النشر',
        fn() => quiz_submit(student_get($ali), $quizId, []), 'غير متاحة');

    db_update('quizzes', $quizId, ['is_published' => 1]);
    $questions = quiz_questions($quizId, true);
    check('إخفاء الإجابات الصحيحة عن الطالب',
        !array_key_exists('correct_answer', quiz_questions($quizId)[0]));

    $answers = [];
    foreach ($questions as $question) {
        $answers[$question['id']] = $question['correct_answer'];
    }
    $quizBefore = (int) student_get($ali)['total_points'];
    $result = quiz_submit(student_get($ali), $quizId, $answers);
    check('التصحيح الآلي يحتسب الإجابات الصحيحة',
        $result['correct'] === 2 && $result['points_awarded'] === 10);
    check('إضافة نقاط المسابقة لرصيد الطالب',
        (int) student_get($ali)['total_points'] === $quizBefore + 10);
    throws('منع تكرار المشاركة في نفس المسابقة',
        fn() => quiz_submit(student_get($ali), $quizId, $answers), 'سبق');

    // إجابة خاطئة لا تُحتسب
    $wrong = [];
    foreach ($questions as $question) {
        $wrong[$question['id']] = 'إجابة خاطئة';
    }
    $saraResult = quiz_submit(student_get($sara), $quizId, $wrong);
    check('عدم احتساب الإجابات الخاطئة', $saraResult['correct'] === 0 && $saraResult['points_awarded'] === 0);

    echo "\nالبوابة والتقارير:\n";
    $student = student_get($ali);
    check('استرجاع الطالب برمز المتابعة',
        (int) student_by_token($student['access_token'])['id'] === $ali);
    check('رفض رمز متابعة غير صحيح', student_by_token('غير-موجود') === null);

    $profile = student_profile($student);
    check('بناء ملف الطالب مع الرسم البياني', count($profile['chart']) === 14 && $profile['rank'] >= 1);

    $stats = dashboard_stats($entityId);
    check('مؤشرات لوحة القيادة متسقة',
        $stats['students'] === 2 && count($stats['trend']) === 14);

    $report = reports_data($entityId, 'month');
    check('بناء التقارير الشهرية',
        count($report['monthly']) === 6 && count($report['attendance_trend']) === 14);

    echo "\nبنك المسابقات الجاهزة:\n";
    $bankCount = quiz_bank_import($entityId);
    check('نسخ مسابقات البنك إلى الجهة', $bankCount === count(quiz_bank()));
    check('نسخ الأسئلة مع المسابقات',
        (int) db_value('SELECT COUNT(*) FROM quiz_questions q JOIN quizzes z ON z.id = q.quiz_id WHERE z.entity_id = ?', [$entityId]) > 20);
    check('المسابقات المنسوخة تُحفظ كمسودات',
        (int) db_value('SELECT COUNT(*) FROM quizzes WHERE entity_id = ? AND is_published = 1', [$entityId]) === 1);
    check('عدم تكرار النسخ عند الاستيراد مرة أخرى', quiz_bank_import($entityId) === 0);
    $survey = db_one("SELECT * FROM quizzes WHERE entity_id = ? AND type = 'survey' LIMIT 1", [$entityId]);
    check('استبانات البنك بلا إجابات صحيحة',
        $survey !== null
        && (int) db_value('SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = ? AND correct_answer IS NOT NULL', [$survey['id']]) === 0);

    echo "\nاستيراد الطلاب:\n";
    $imported = students_import($entityId, "أحمد المستورد، A-1، 0551111111\nخالد المستورد\n\n", $groupId);
    check('استيراد الأسماء وتجاهل الأسطر الفارغة', $imported === 2);
    check('حفظ رقم الطالب من سطر الاستيراد',
        db_value('SELECT code FROM students WHERE entity_id = ? AND name = ?', [$entityId, 'أحمد المستورد']) === 'A-1');
} finally {
    // حذف جهة الاختبار وكل بياناتها
    db_delete('entities', $entityId);
}

echo "\n=====================================\n";
printf("النتيجة: %d ناجح · %d فاشل\n", $passed, $failed);
exit($failed === 0 ? 0 : 1);
