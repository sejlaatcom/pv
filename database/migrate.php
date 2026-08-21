<?php
/**
 * ترقية قاعدة بيانات قائمة إلى أحدث مخطط، دون المساس بالبيانات.
 * يُشغَّل بأمان أكثر من مرة:  php database/migrate.php
 */

if (PHP_SAPI !== 'cli') {
    exit('هذا الملف يعمل من سطر الأوامر فقط.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

if (!db_is_installed()) {
    exit("✗ قاعدة البيانات غير مهيأة بعد. شغّل: php database/install.php\n");
}

/** الأعمدة المطلوبة: [الجدول, العمود, تعريف العمود] */
$columns = [
    ['entities', 'allow_self_register', "TINYINT(1) NOT NULL DEFAULT 0"],
    ['entities', 'public_board',        "TINYINT(1) NOT NULL DEFAULT 1"],
];

$database = config('db')['name'];
$applied = 0;

foreach ($columns as [$table, $column, $definition]) {
    $exists = db_value(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
        [$database, $table, $column]
    );

    if ((int) $exists > 0) {
        echo "  • $table.$column موجود\n";
        continue;
    }

    db()->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    echo "  ✓ أُضيف $table.$column\n";
    $applied++;
}

echo $applied === 0
    ? "\nقاعدة البيانات محدّثة بالفعل ✅\n"
    : "\nاكتملت الترقية ($applied تعديل) ✅\n";
