<?php
/** تصدير البيانات إلى ملفات CSV تُفتح في Excel. */

function export_students(): void
{
    require_login();
    $entityId = current_entity_id();

    $students = students_list($entityId, ['group_id' => input_int('group_id')]);
    $rows = [];
    foreach ($students as $index => $student) {
        $rows[] = [
            $index + 1,
            $student['name'],
            $student['code'],
            $student['group_name'],
            $student['total_points'],
            $student['guardian_name'],
            $student['guardian_phone'],
            $student['is_active'] ? 'نشط' : 'موقوف',
            absolute_url('/p/' . $student['access_token']),
        ];
    }

    csv_download(
        'students-' . today(),
        ['#', 'الاسم', 'الرقم', 'المجموعة', 'النقاط', 'ولي الأمر', 'جوال ولي الأمر', 'الحالة', 'رابط المتابعة'],
        $rows
    );
}

function export_leaderboard(): void
{
    require_login();
    $entityId = current_entity_id();

    $period = input('period', 'month');
    if (!in_array($period, ['week', 'month', 'term', 'all'], true)) {
        $period = 'month';
    }

    $rows = [];
    foreach (leaderboard($entityId, $period, input_int('group_id'), 500) as $row) {
        $rows[] = [$row['rank'], $row['name'], $row['code'], $row['group_name'], $row['points'], $row['badge_count']];
    }

    csv_download(
        'leaderboard-' . $period . '-' . today(),
        ['الترتيب', 'الطالب', 'الرقم', 'المجموعة', 'النقاط', 'عدد الأوسمة'],
        $rows
    );
}

function export_report(): void
{
    require_login();
    $entityId = current_entity_id();

    $period = input('period', 'month');
    if (!in_array($period, ['week', 'month', 'term', 'all'], true)) {
        $period = 'month';
    }
    $report = reports_data($entityId, $period);

    $rows = [['— أداء المجموعات —', '', '', '']];
    foreach ($report['by_group'] as $group) {
        $rows[] = ['مجموعة', $group['label'], $group['value'], $group['average']];
    }

    $rows[] = ['', '', '', ''];
    $rows[] = ['— توزيع النقاط حسب النوع —', '', '', ''];
    foreach ($report['categories'] as $category) {
        $rows[] = ['تصنيف', $category['label'], $category['value'], ''];
    }

    $rows[] = ['', '', '', ''];
    $rows[] = ['— الأعلى نقاطاً —', '', '', ''];
    foreach ($report['top_students'] as $student) {
        $rows[] = ['طالب', $student['name'], $student['value'], $student['group_name']];
    }

    $rows[] = ['', '', '', ''];
    $rows[] = ['— النقاط الشهرية —', '', '', ''];
    foreach ($report['monthly'] as $month) {
        $rows[] = ['شهر', $month['label'], $month['value'], ''];
    }

    $rows[] = ['', '', '', ''];
    $rows[] = ['إجمالي النقاط الإيجابية', $report['behavior']['positive'], '', ''];
    $rows[] = ['إجمالي النقاط المخصومة', $report['behavior']['negative'], '', ''];

    csv_download('report-' . $period . '-' . today(), ['النوع', 'البيان', 'القيمة', 'تفصيل'], $rows);
}

function export_student(int $id): void
{
    require_login();
    $student = guard_entity(student_get($id));
    $profile = student_profile($student);

    $rows = [];
    foreach ($profile['transactions'] as $transaction) {
        $rows[] = [
            date('Y-m-d H:i', strtotime($transaction['created_at'])),
            $transaction['reason'] ?: $transaction['item_title'],
            $transaction['points'],
            $transaction['awarded_by_name'],
        ];
    }

    csv_download(
        'student-' . $id . '-' . today(),
        ['التاريخ', 'البيان', 'النقاط', 'الراصد'],
        $rows
    );
}
