<?php
/** كشف الحضور والانصراف. */

function attendance_page(): void
{
    require_login();
    $entityId = current_entity_id();

    $day = input('day', today());
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $day)) {
        $day = today();
    }
    $groupId = input_int('group_id');

    view('app/attendance', [
        'title'   => 'الحضور والانصراف',
        'day'     => $day,
        'groupId' => $groupId,
        'groups'  => groups_list($entityId),
        'rows'    => attendance_sheet($entityId, $day, $groupId),
        'stats'   => attendance_stats($entityId, $day),
    ], 'app');
}

function attendance_submit(): void
{
    require_login();
    $entityId = current_entity_id();

    $day = input('day', today());
    $entries = (array) ($_POST['status'] ?? []);
    $saved = attendance_save($entityId, $day, $entries, (int) current_user()['id']);

    flash($saved ? 'success' : 'error', $saved ? "تم حفظ حضور $saved طالباً" : 'لم يتم تحديد أي حالة');
    redirect('/attendance?day=' . urlencode($day) . (input_int('group_id') ? '&group_id=' . input_int('group_id') : ''));
}
