<?php
/**
 * إعدادات منصة نقاطي.
 *
 * عدّل القيم هنا مباشرة على الاستضافة، أو اضبط متغيرات البيئة، أو أنشئ ملف
 * app/config.local.php ليتجاوز هذه القيم (لا يُرفع إلى المستودع).
 */

$config = [
    // ===== قاعدة البيانات =====
    'db' => [
        'host'     => getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => getenv('DB_PORT') ?: '3306',
        'name'     => getenv('DB_NAME') ?: 'noqati',
        'user'     => getenv('DB_USER') ?: 'root',
        'pass'     => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
        'charset'  => 'utf8mb4',
    ],

    // ===== هوية المنصة =====
    'brand' => [
        'name'      => 'نقاطي',
        'full_name' => 'منصة نقاطي',
        'tagline'   => 'منصة متكاملة لإدارة التفاعل والتحفيز للطلاب والمشاركين',
        'subline'   => 'كل ما تحتاجه لتحفيز المشاركين وإدارة البرامج التعليمية والتربوية في منصة واحدة',
        'phone'     => '0500000000',
        'whatsapp'  => '966500000000',
        'email'     => 'info@example.com',
        'website'   => 'example.com',
        'social'    => '@noqatyapp',
        'price'     => 350,
        'currency'  => 'ريال',
    ],

    // ===== عام =====
    'app' => [
        'base_url'  => getenv('APP_BASE_URL') ?: '',   // اتركه فارغاً إذا كان الموقع في جذر النطاق
        'timezone'  => 'Asia/Riyadh',
        'debug'     => (getenv('APP_DEBUG') === '1'),
        'session'   => 'noqati_session',
    ],
];

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $override = require $localConfig;
    if (is_array($override)) {
        foreach ($override as $overrideSection => $overrideValues) {
            $config[$overrideSection] = array_merge(
                $config[$overrideSection] ?? [],
                (array) $overrideValues
            );
        }
    }
}

return $config;
