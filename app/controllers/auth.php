<?php
/** تسجيل الدخول وإنشاء حساب جهة جديدة. */

function auth_login_page(array $old = [], ?string $error = null): void
{
    if (is_logged_in()) {
        redirect('/dashboard');
    }
    view('auth/login', [
        'title' => 'تسجيل الدخول — ' . brand('name'),
        'old'   => $old,
        'error' => $error,
    ], 'blank');
}

function auth_login_submit(): void
{
    $email = mb_strtolower(input('email', ''));
    $password = (string) input('password', '');

    $user = attempt_login($email, $password);
    if (!$user) {
        auth_login_page(['email' => $email], 'البريد الإلكتروني أو كلمة المرور غير صحيحة');
        return;
    }

    auth_login($user);
    flash('success', 'مرحباً بك ' . $user['name']);
    redirect('/dashboard');
}

function auth_register_page(array $old = [], array $errors = []): void
{
    if (is_logged_in()) {
        redirect('/dashboard');
    }
    view('auth/register', [
        'title'  => 'إنشاء حساب جهة — ' . brand('name'),
        'old'    => $old,
        'errors' => $errors,
    ], 'blank');
}

function auth_register_submit(): void
{
    $data = [
        'entity_name' => input('entity_name', ''),
        'entity_type' => input('entity_type', 'school'),
        'city'        => input('city', ''),
        'name'        => input('name', ''),
        'email'       => mb_strtolower(input('email', '')),
        'phone'       => input('phone', ''),
        'password'    => (string) input('password', ''),
    ];

    $errors = [];
    if (mb_strlen($data['entity_name']) < 2) {
        $errors['entity_name'] = 'أدخل اسم الجهة';
    }
    if (mb_strlen($data['name']) < 2) {
        $errors['name'] = 'أدخل اسمك';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'بريد إلكتروني غير صحيح';
    } elseif (db_value('SELECT id FROM users WHERE email = ?', [$data['email']])) {
        $errors['email'] = 'هذا البريد مسجّل مسبقاً';
    }
    if (strlen($data['password']) < 6) {
        $errors['password'] = 'كلمة المرور يجب ألا تقل عن 6 أحرف';
    }

    if ($errors) {
        auth_register_page($data, $errors);
        return;
    }

    $entityId = entity_create([
        'name'  => $data['entity_name'],
        'type'  => $data['entity_type'],
        'city'  => $data['city'],
        'phone' => $data['phone'],
        'email' => $data['email'],
    ]);
    entity_seed_defaults($entityId);

    $userId = db_insert('users', [
        'entity_id'     => $entityId,
        'name'          => $data['name'],
        'email'         => $data['email'],
        'phone'         => $data['phone'] ?: null,
        'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        'role'          => 'admin',
    ]);

    auth_login(['id' => $userId]);
    flash('success', 'تم إنشاء حساب جهتك، وأضفنا لك بنود نقاط وأوسمة وجوائز جاهزة للبدء.');
    redirect('/dashboard');
}

function auth_logout_action(): void
{
    auth_logout();
    flash('success', 'تم تسجيل الخروج');
    redirect('/');
}
