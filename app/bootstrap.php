<?php
/** تحميل النواة: الإعدادات، الدوال، قاعدة البيانات، المتحكمات. */

declare(strict_types=1);

mb_internal_encoding('UTF-8');

require __DIR__ . '/lib/helpers.php';

date_default_timezone_set(config('app')['timezone']);

if (config('app')['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/auth.php';
require __DIR__ . '/lib/models.php';
require __DIR__ . '/lib/modules.php';
require __DIR__ . '/lib/charts.php';

foreach (glob(__DIR__ . '/controllers/*.php') as $controller) {
    require $controller;
}

session_start_once();
