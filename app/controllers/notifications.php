<?php
/** التنبيهات والملاحظات المرسلة لأولياء الأمور. */

function notifications_page(): void
{
    require_login();
    $entityId = current_entity_id();

    view('app/notifications', [
        'title'         => 'التنبيهات والملاحظات',
        'notifications' => db_all(
            'SELECT n.*, s.name AS student_name, s.guardian_phone, s.access_token, u.name AS sender
             FROM notifications n
             LEFT JOIN students s ON s.id = n.student_id
             LEFT JOIN users u ON u.id = n.created_by
             WHERE n.entity_id = ? ORDER BY n.created_at DESC LIMIT 100',
            [$entityId]
        ),
        'students'      => students_list($entityId, ['only_active' => true]),
    ], 'app');
}

function notifications_send(): void
{
    require_login();
    $entityId = current_entity_id();

    $title = input('title', '');
    $body  = input('body', '');
    $studentId = input_int('student_id');

    if (mb_strlen($title) < 2) {
        flash('error', 'اكتب عنوان التنبيه');
        redirect('/notifications');
    }

    if ($studentId) {
        guard_entity(student_get($studentId));
    }

    db_insert('notifications', [
        'entity_id'  => $entityId,
        'student_id' => $studentId ?: null,
        'title'      => $title,
        'body'       => $body ?: null,
        'channel'    => input('channel', 'whatsapp') === 'app' ? 'app' : 'whatsapp',
        'created_by' => (int) current_user()['id'],
    ]);

    flash('success', 'تم تسجيل التنبيه، ويمكنك إرساله عبر الواتساب من القائمة');
    redirect('/notifications');
}
