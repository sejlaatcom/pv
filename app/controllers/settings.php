<?php
/** إعدادات الجهة وإدارة المشرفين. */

function settings_page(): void
{
    $user = require_login();
    $entityId = current_entity_id();

    view('app/settings', [
        'title'  => 'الإعدادات',
        'entity' => entity_get($entityId),
        'users'  => db_all('SELECT * FROM users WHERE entity_id = ? ORDER BY role, name', [$entityId]),
        'user'   => $user,
    ], 'app');
}

function settings_entity_update(): void
{
    require_admin();
    $entityId = current_entity_id();

    $name = input('name', '');
    if (mb_strlen($name) < 2) {
        flash('error', 'أدخل اسم الجهة');
        redirect('/settings');
    }

    db_update('entities', $entityId, [
        'name'                => $name,
        'type'                => input('type', 'school'),
        'city'                => input('city', '') ?: null,
        'phone'               => input('phone', '') ?: null,
        'email'               => input('email', '') ?: null,
        'primary_color'       => input('primary_color', '#0d9488') ?: '#0d9488',
        'allow_self_register' => input('allow_self_register') ? 1 : 0,
        'public_board'        => input('public_board') ? 1 : 0,
    ]);

    flash('success', 'تم حفظ بيانات الجهة');
    redirect('/settings');
}

function settings_user_create(): void
{
    require_admin();

    $name = input('name', '');
    $email = mb_strtolower(input('email', ''));
    $password = (string) input('password', '');

    if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        flash('error', 'أدخل الاسم وبريداً صحيحاً وكلمة مرور لا تقل عن 6 أحرف');
        redirect('/settings');
    }
    if (db_value('SELECT id FROM users WHERE email = ?', [$email])) {
        flash('error', 'هذا البريد مسجّل مسبقاً');
        redirect('/settings');
    }

    db_insert('users', [
        'entity_id'     => current_entity_id(),
        'name'          => $name,
        'email'         => $email,
        'phone'         => input('phone', '') ?: null,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role'          => input('role', 'supervisor') === 'admin' ? 'admin' : 'supervisor',
    ]);

    flash('success', 'تمت إضافة المستخدم');
    redirect('/settings');
}

function settings_user_delete(int $id): void
{
    $current = require_admin();

    if ($id === (int) $current['id']) {
        flash('error', 'لا يمكنك حذف حسابك الحالي');
        redirect('/settings');
    }

    $target = db_one('SELECT * FROM users WHERE id = ?', [$id]);
    guard_entity($target);
    db_delete('users', $id);

    flash('success', 'تم حذف المستخدم');
    redirect('/settings');
}

/** تغيير كلمة مرور المستخدم الحالي */
function settings_password_update(): void
{
    $user = require_login();

    $current = (string) input('current_password', '');
    $new     = (string) input('new_password', '');
    $confirm = (string) input('confirm_password', '');

    if (!password_verify($current, $user['password_hash'])) {
        flash('error', 'كلمة المرور الحالية غير صحيحة');
        redirect('/settings');
    }
    if (strlen($new) < 6) {
        flash('error', 'كلمة المرور الجديدة يجب ألا تقل عن 6 أحرف');
        redirect('/settings');
    }
    if ($new !== $confirm) {
        flash('error', 'كلمتا المرور غير متطابقتين');
        redirect('/settings');
    }

    db_update('users', (int) $user['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
    flash('success', 'تم تغيير كلمة المرور بنجاح');
    redirect('/settings');
}
