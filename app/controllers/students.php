<?php
/** إدارة الطلاب/المشاركين. */

function students_page(): void
{
    require_login();
    $entityId = current_entity_id();

    $filters = [
        'group_id' => input_int('group_id'),
        'search'   => input('q', ''),
    ];

    view('app/students', [
        'title'    => 'الطلاب والمشاركون',
        'students' => students_list($entityId, $filters),
        'groups'   => groups_list($entityId),
        'filters'  => $filters,
    ], 'app');
}

function students_create(): void
{
    require_login();
    $entityId = current_entity_id();

    $name = input('name', '');
    if (mb_strlen($name) < 2) {
        flash('error', 'أدخل اسم الطالب');
        redirect('/students');
    }

    student_create($entityId, [
        'name'           => $name,
        'group_id'       => input_int('group_id'),
        'code'           => input('code', ''),
        'gender'         => input('gender', 'male') === 'female' ? 'female' : 'male',
        'guardian_name'  => input('guardian_name', ''),
        'guardian_phone' => input('guardian_phone', ''),
    ]);

    flash('success', 'تمت إضافة الطالب بنجاح');
    redirect('/students');
}

function students_import_action(): void
{
    require_login();
    $raw = (string) input('rows', '');
    $groupId = input_int('group_id');

    if (trim($raw) === '') {
        flash('error', 'الصق قائمة الأسماء أولاً');
        redirect('/students');
    }

    $count = students_import(current_entity_id(), $raw, $groupId);
    flash($count > 0 ? 'success' : 'error', $count > 0 ? "تم استيراد $count طالباً" : 'لم يتم استيراد أي اسم صالح');
    redirect('/students');
}

function student_page(int $id): void
{
    require_login();
    $student = guard_entity(student_get($id));
    $entityId = current_entity_id();

    view('app/student', [
        'title'   => $student['name'],
        'student' => $student,
        'profile' => student_profile($student),
        'groups'  => groups_list($entityId),
        'badges'  => badges_list($entityId),
        'items'   => point_items_list($entityId, true),
    ], 'app');
}

function student_update_action(int $id): void
{
    require_login();
    $student = guard_entity(student_get($id));

    db_update('students', $id, [
        'name'           => input('name', $student['name']),
        'group_id'       => input_int('group_id') ?: null,
        'code'           => input('code', '') ?: null,
        'guardian_name'  => input('guardian_name', '') ?: null,
        'guardian_phone' => input('guardian_phone', '') ?: null,
        'is_active'      => input('is_active') ? 1 : 0,
    ]);

    flash('success', 'تم تحديث بيانات الطالب');
    redirect('/students/' . $id);
}

function student_delete_action(int $id): void
{
    require_admin();
    $student = guard_entity(student_get($id));
    db_delete('students', $id);

    flash('success', 'تم حذف الطالب ' . $student['name']);
    redirect('/students');
}

function student_badge_action(int $id): void
{
    require_login();
    $student = guard_entity(student_get($id));
    $badgeId = input_int('badge_id');
    $badge = $badgeId ? db_one('SELECT * FROM badges WHERE id = ? AND entity_id = ?', [$badgeId, $student['entity_id']]) : null;

    if (!$badge) {
        flash('error', 'الوسام غير موجود');
        redirect('/students/' . $id);
    }

    $awarded = badge_award($id, (int) $badge['id'], (int) current_user()['id']);
    flash($awarded ? 'success' : 'info', $awarded ? 'تم منح الوسام' : 'الطالب حاصل على هذا الوسام مسبقاً');
    redirect('/students/' . $id);
}

/** تسجيل تنبيه لولي الأمر (يُرسل عبر رابط واتساب من الواجهة) */
function student_notify_action(int $id): void
{
    require_login();
    $student = guard_entity(student_get($id));

    $title = input('title', 'تنبيه');
    $body = input('body', '');

    db_insert('notifications', [
        'entity_id'  => $student['entity_id'],
        'student_id' => $id,
        'title'      => $title,
        'body'       => $body ?: null,
        'channel'    => 'whatsapp',
        'created_by' => (int) current_user()['id'],
    ]);

    flash('success', 'تم تسجيل التنبيه، ويمكنك إرساله لولي الأمر عبر الواتساب');
    redirect('/students/' . $id);
}
