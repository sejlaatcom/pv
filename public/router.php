<?php
/**
 * موجّه خادم PHP المدمج للتطوير المحلي:
 *   php -S localhost:8000 -t public public/router.php
 * يخدم الملفات الثابتة مباشرة ويمرر بقية الطلبات إلى index.php
 */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
