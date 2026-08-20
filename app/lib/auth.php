<?php
/** المصادقة والصلاحيات. */

function auth_login(array $user): void
{
    session_start_once();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
}

function auth_logout(): void
{
    session_start_once();
    unset($_SESSION['user_id']);
    session_regenerate_id(true);
}

/** المستخدم الحالي أو null */
function current_user(): ?array
{
    static $user = null;
    static $loaded = false;

    if ($loaded) {
        return $user;
    }
    $loaded = true;

    session_start_once();
    $id = $_SESSION['user_id'] ?? null;
    if (!$id) {
        return null;
    }

    $user = db_one(
        'SELECT u.*, e.name AS entity_name, e.slug AS entity_slug, e.type AS entity_type
         FROM users u LEFT JOIN entities e ON e.id = u.entity_id
         WHERE u.id = ? AND u.is_active = 1',
        [$id]
    );

    return $user;
}

function current_entity_id(): int
{
    $user = current_user();
    return (int) ($user['entity_id'] ?? 0);
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && $user['role'] === 'admin';
}

/** حماية صفحات لوحة التحكم */
function require_login(): array
{
    $user = current_user();
    if (!$user) {
        flash('error', 'الرجاء تسجيل الدخول للمتابعة');
        redirect('/login');
    }
    return $user;
}

/** حماية الصفحات الخاصة بمدير الجهة */
function require_admin(): array
{
    $user = require_login();
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        flash('error', 'هذه الصفحة متاحة لمدير الجهة فقط');
        redirect('/dashboard');
    }
    return $user;
}

/** التأكد أن السجل يعود لجهة المستخدم الحالي */
function guard_entity(?array $row): array
{
    if (!$row || (int) ($row['entity_id'] ?? 0) !== current_entity_id()) {
        http_response_code(404);
        exit('السجل غير موجود');
    }
    return $row;
}

function attempt_login(string $email, string $password): ?array
{
    $user = db_one('SELECT * FROM users WHERE email = ? AND is_active = 1', [mb_strtolower($email)]);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return null;
    }
    db_update('users', (int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
    return $user;
}
