<?php
/**
 * مثبّت المنصة — يُشغَّل من الطرفية:
 *
 *   php database/install.php            إنشاء قاعدة البيانات والجداول
 *   php database/install.php --demo     مع إضافة بيانات تجريبية جاهزة
 *   php database/install.php --fresh    حذف الجداول وإعادة إنشائها
 */

if (PHP_SAPI !== 'cli') {
    exit('هذا الملف يعمل من سطر الأوامر فقط.');
}

require dirname(__DIR__) . '/app/bootstrap.php';
require __DIR__ . '/seed.php';

$options = $argv ?? [];
$withDemo = in_array('--demo', $options, true);

$cfg = config('db');
echo "== تهيئة منصة " . brand('name') . " ==\n";
echo "الخادم: {$cfg['host']}:{$cfg['port']} · القاعدة: {$cfg['name']}\n";

// 1) إنشاء قاعدة البيانات إن لم تكن موجودة
try {
    $rootPdo = new PDO(
        "mysql:host={$cfg['host']};port={$cfg['port']}",
        $cfg['user'],
        $cfg['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $rootPdo->exec(
        "CREATE DATABASE IF NOT EXISTS `{$cfg['name']}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );
    echo "✓ قاعدة البيانات جاهزة\n";
} catch (PDOException $e) {
    exit("✗ تعذّر الاتصال بخادم قاعدة البيانات: " . $e->getMessage() . "\n");
}

// 2) تنفيذ ملف الجداول
$schema = file_get_contents(__DIR__ . '/schema.sql');
if ($schema === false) {
    exit("✗ لم يتم العثور على database/schema.sql\n");
}

try {
    db()->exec($schema);
    echo "✓ تم إنشاء الجداول\n";
} catch (PDOException $e) {
    exit("✗ خطأ أثناء تنفيذ المخطط: " . $e->getMessage() . "\n");
}

// 3) البيانات التجريبية
if ($withDemo) {
    $result = seed_demo_data();
    echo "✓ تمت إضافة بيانات تجريبية ({$result['students']} طالباً)\n\n";
    echo "  بيانات الدخول:\n";
    echo "    البريد: {$result['admin_email']}\n";
    echo "    كلمة المرور: {$result['password']}\n";
    echo "    مشرف إضافي: supervisor@demo.local / 123456\n";
    echo "    رابط طالب للتجربة: {$result['sample_link']}\n\n";
} else {
    echo "  (استخدم --demo لإضافة بيانات تجريبية)\n";
    echo "  أنشئ حساب جهتك من صفحة /register\n\n";
}

echo "اكتمل التثبيت ✅\n";
echo "لتشغيل المنصة محلياً: php -S localhost:8000 -t public public/router.php\n";
