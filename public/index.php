<?php
/**
 * نقطة الدخول الوحيدة للموقع (Front Controller).
 * جميع الطلبات تمر من هنا ثم تُوجَّه إلى المتحكم المناسب.
 */

require dirname(__DIR__) . '/app/bootstrap.php';

// المسار المطلوب بعد إزالة مجلد التثبيت والمعاملات
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim(config('app')['base_url'], '/');
if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$requestPath = '/' . trim($requestPath, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/**
 * جدول المسارات: [الطريقة, النمط, الدالة المسؤولة]
 * {id} و {token} تُمرَّر كمعاملات للدالة.
 */
$routes = [
    // ===== الموقع التعريفي =====
    ['GET',  '/',            'page_home'],
    ['GET',  '/features',    'page_features'],
    ['GET',  '/pricing',     'page_pricing'],
    ['GET',  '/faq',         'page_faq'],
    ['GET',  '/contact',     'page_contact'],
    ['POST', '/contact',     'page_contact_submit'],

    // ===== الدخول والتسجيل =====
    ['GET',  '/login',       'auth_login_page'],
    ['POST', '/login',       'auth_login_submit'],
    ['GET',  '/register',    'auth_register_page'],
    ['POST', '/register',    'auth_register_submit'],
    ['POST', '/logout',      'auth_logout_action'],

    // ===== لوحة التحكم =====
    ['GET',  '/dashboard',   'dashboard_page'],
    ['GET',  '/reports',     'reports_page'],

    // ===== الطلاب =====
    ['GET',  '/students',            'students_page'],
    ['POST', '/students',            'students_create'],
    ['POST', '/students/import',     'students_import_action'],
    ['GET',  '/students/{id}',       'student_page'],
    ['POST', '/students/{id}/update', 'student_update_action'],
    ['POST', '/students/{id}/delete', 'student_delete_action'],
    ['POST', '/students/{id}/badge',  'student_badge_action'],
    ['POST', '/students/{id}/notify', 'student_notify_action'],

    // ===== المجموعات =====
    ['GET',  '/groups',              'groups_page'],
    ['POST', '/groups',              'groups_create'],
    ['POST', '/groups/{id}/delete',  'groups_delete'],

    // ===== رصد النقاط =====
    ['GET',  '/award',               'award_page'],
    ['POST', '/award',               'award_submit'],
    ['GET',  '/point-items',         'point_items_page'],
    ['POST', '/point-items',         'point_items_create'],
    ['POST', '/point-items/{id}/delete', 'point_items_delete'],

    // ===== الحضور =====
    ['GET',  '/attendance',          'attendance_page'],
    ['POST', '/attendance',          'attendance_submit'],

    // ===== المتصدرون والأوسمة والجوائز =====
    ['GET',  '/leaderboard',         'leaderboard_page'],
    ['GET',  '/badges',              'badges_page'],
    ['POST', '/badges',              'badges_create'],
    ['POST', '/badges/{id}/delete',  'badges_delete'],
    ['GET',  '/rewards',             'rewards_page'],
    ['POST', '/rewards',             'rewards_create'],
    ['POST', '/rewards/{id}/delete', 'rewards_delete'],
    ['POST', '/redemptions/{id}',    'redemption_update'],

    // ===== المهام =====
    ['GET',  '/tasks',               'tasks_page'],
    ['POST', '/tasks',               'tasks_create'],
    ['GET',  '/tasks/{id}',          'task_page'],
    ['POST', '/tasks/{id}/grade',    'task_grade_action'],

    // ===== المسابقات =====
    ['GET',  '/quizzes',             'quizzes_page'],
    ['GET',  '/quizzes/new',         'quiz_new_page'],
    ['POST', '/quizzes',             'quiz_create_action'],
    ['POST', '/quizzes/import-bank', 'quiz_bank_action'],
    ['GET',  '/quizzes/{id}',        'quiz_page'],
    ['POST', '/quizzes/{id}/publish', 'quiz_publish_action'],
    ['POST', '/quizzes/{id}/delete',  'quiz_delete_action'],

    // ===== الإعدادات =====
    ['GET',  '/settings',            'settings_page'],
    ['POST', '/settings/entity',     'settings_entity_update'],
    ['POST', '/settings/users',      'settings_user_create'],
    ['POST', '/settings/password',   'settings_password_update'],
    ['POST', '/settings/users/{id}/delete', 'settings_user_delete'],
    ['GET',  '/messages',            'messages_page'],

    // ===== التنبيهات =====
    ['GET',  '/notifications',        'notifications_page'],
    ['POST', '/notifications',        'notifications_send'],

    // ===== التصدير إلى Excel =====
    ['GET',  '/export/students',      'export_students'],
    ['GET',  '/export/leaderboard',   'export_leaderboard'],
    ['GET',  '/export/report',        'export_report'],
    ['GET',  '/students/{id}/export', 'export_student'],

    // ===== صفحات الجهة العامة =====
    ['GET',  '/join/{token}',         'join_page'],
    ['POST', '/join/{token}',         'join_submit'],
    ['GET',  '/e/{token}',            'public_board_page'],

    // ===== بوابة الطالب وولي الأمر =====
    ['GET',  '/p/{token}',                 'portal_page'],
    ['POST', '/p/{token}/redeem',          'portal_redeem'],
    ['GET',  '/p/{token}/quiz/{id}',       'portal_quiz_page'],
    ['POST', '/p/{token}/quiz/{id}',       'portal_quiz_submit'],
    ['GET',  '/student-login',             'portal_login_page'],
    ['POST', '/student-login',             'portal_login_submit'],
];

// التحقق من رمز الحماية لكل طلبات POST
csrf_verify();

foreach ($routes as [$routeMethod, $pattern, $handler]) {
    $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#u';
    if (!preg_match($regex, $requestPath, $matches)) {
        continue;
    }
    if ($routeMethod !== $method) {
        continue;
    }
    if (!function_exists($handler)) {
        http_response_code(500);
        exit('المتحكم غير موجود: ' . e($handler));
    }

    $args = [];
    foreach ($matches as $key => $value) {
        if (!is_int($key)) {
            $args[] = $key === 'id' ? (int) $value : $value;
        }
    }

    // تأكد من تهيئة قاعدة البيانات قبل أي صفحة تعتمد عليها
    if (!db_is_installed()) {
        require dirname(__DIR__) . '/app/views/install_notice.php';
        exit;
    }

    $handler(...$args);
    exit;
}

http_response_code(404);
view('errors/404', [], 'site');
