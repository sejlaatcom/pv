<?php
/** إدارة المجموعات والفصول والحلقات. */

function groups_page(): void
{
    require_login();
    $entityId = current_entity_id();

    view('app/groups', [
        'title'       => 'المجموعات',
        'groups'      => groups_list($entityId),
        'supervisors' => db_all('SELECT id, name FROM users WHERE entity_id = ? AND is_active = 1', [$entityId]),
    ], 'app');
}

function groups_create(): void
{
    require_login();

    $name = input('name', '');
    if (mb_strlen($name) < 2) {
        flash('error', 'أدخل اسم المجموعة');
        redirect('/groups');
    }

    $palette = ['#0d9488', '#8b5cf6', '#f59e0b', '#0ea5e9', '#ec4899', '#22c55e'];
    group_create(current_entity_id(), [
        'name'          => $name,
        'description'   => input('description', ''),
        'level'         => input('level', ''),
        'color'         => $palette[array_rand($palette)],
        'supervisor_id' => input_int('supervisor_id'),
    ]);

    flash('success', 'تمت إضافة المجموعة');
    redirect('/groups');
}

function groups_delete(int $id): void
{
    require_admin();
    guard_entity(group_get($id));
    db_delete('student_groups', $id);

    flash('success', 'تم حذف المجموعة (وبقي الطلاب بدون مجموعة)');
    redirect('/groups');
}
