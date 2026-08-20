<?php
/**
 * طبقة الوصول لقاعدة البيانات (PDO / MySQL).
 * كل الاستعلامات تستخدم عبارات مُجهّزة (prepared statements) لمنع حقن SQL.
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = config('db');
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $cfg['host'],
        $cfg['port'],
        $cfg['name'],
        $cfg['charset']
    );

    try {
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

        // توحيد المنطقة الزمنية بين PHP وMySQL حتى تتطابق مقارنات التواريخ
        // (تقارير اليوم، كشف الحضور، الرسوم البيانية) على أي استضافة.
        $offset = (new DateTime('now', new DateTimeZone(config('app')['timezone'])))->format('P');
        $stmt = $pdo->prepare('SET time_zone = ?');
        $stmt->execute([$offset]);
    } catch (PDOException $e) {
        if (config('app')['debug']) {
            throw $e;
        }
        http_response_code(500);
        exit('تعذّر الاتصال بقاعدة البيانات. تأكد من إعدادات app/config.php');
    }

    return $pdo;
}

/** تنفيذ استعلام مع قيم مُجهّزة */
function db_run(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/** إرجاع جميع الصفوف */
function db_all(string $sql, array $params = []): array
{
    return db_run($sql, $params)->fetchAll();
}

/** إرجاع صف واحد أو null */
function db_one(string $sql, array $params = []): ?array
{
    $row = db_run($sql, $params)->fetch();
    return $row === false ? null : $row;
}

/** إرجاع قيمة عمود واحد */
function db_value(string $sql, array $params = [], $default = null)
{
    $value = db_run($sql, $params)->fetchColumn();
    return $value === false ? $default : $value;
}

/** إدراج صف وإرجاع المعرّف الجديد */
function db_insert(string $table, array $data): int
{
    $columns = array_keys($data);
    $sql = sprintf(
        'INSERT INTO `%s` (%s) VALUES (%s)',
        $table,
        implode(', ', array_map(fn($c) => "`$c`", $columns)),
        implode(', ', array_map(fn($c) => ":$c", $columns))
    );
    db_run($sql, $data);
    return (int) db()->lastInsertId();
}

/** تحديث صف بالمعرّف */
function db_update(string $table, int $id, array $data): void
{
    if (!$data) {
        return;
    }
    $sets = implode(', ', array_map(fn($c) => "`$c` = :$c", array_keys($data)));
    $data['__id'] = $id;
    db_run("UPDATE `$table` SET $sets WHERE id = :__id", $data);
}

/** حذف صف بالمعرّف */
function db_delete(string $table, int $id): void
{
    db_run("DELETE FROM `$table` WHERE id = ?", [$id]);
}

/** هل قاعدة البيانات مُهيّأة (تحتوي على الجداول)؟ */
function db_is_installed(): bool
{
    try {
        db_value('SELECT 1 FROM entities LIMIT 1');
        return true;
    } catch (Throwable $e) {
        return false;
    }
}
