<?php
/** دوال مساعدة عامة: الإعدادات، العرض، الروابط، الحماية، التنسيق. */

/** قراءة قسم من الإعدادات */
function config(string $section = null)
{
    static $config = null;
    if ($config === null) {
        // يُحمَّل الملف داخل نطاق مستقل حتى لا تتداخل متغيراته مع متغيرات هذه الدالة
        $loader = static fn(string $file) => require $file;
        $config = $loader(dirname(__DIR__) . '/config.php');
    }
    if ($section === null) {
        return $config;
    }
    return $config[$section] ?? null;
}

function brand(string $key = null)
{
    $brand = config('brand');
    return $key === null ? $brand : ($brand[$key] ?? '');
}

/** تهريب النصوص قبل الطباعة */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** بناء رابط داخل الموقع */
function url(string $path = '/'): string
{
    $base = rtrim(config('app')['base_url'], '/');
    if ($path === '' || $path[0] !== '/') {
        $path = '/' . $path;
    }
    return $base . $path;
}

function asset(string $path): string
{
    return url('/assets/' . ltrim($path, '/'));
}

/**
 * رابط كامل يشمل النطاق — يُستخدم لكل رابط يُنسخ أو يُرسل خارج الموقع
 * (رابط ولي الأمر، التسجيل الذاتي، لوحة الشرف، رسائل الواتساب).
 */
function absolute_url(string $path = '/'): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

    return $scheme . '://' . $host . url($path);
}

function redirect(string $path): void
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** قراءة قيمة من الطلب */
function input(string $key, $default = null)
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

function input_int(string $key, ?int $default = null): ?int
{
    $value = input($key);
    if ($value === null || $value === '') {
        return $default;
    }
    return (int) $value;
}

// ===== الجلسة والحماية =====

function session_start_once(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name(config('app')['session']);
        session_start();
    }
}

function csrf_token(): string
{
    session_start_once();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/** التحقق من رمز الحماية في كل طلب POST */
function csrf_verify(): void
{
    if (!is_post()) {
        return;
    }
    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('انتهت صلاحية الجلسة، أعد تحميل الصفحة وحاول مرة أخرى.');
    }
}

// ===== الرسائل المؤقتة =====

function flash(string $type, string $message): void
{
    session_start_once();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_pull(): array
{
    session_start_once();
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

// ===== العرض =====

/** عرض صفحة داخل قالب */
function view(string $template, array $data = [], string $layout = 'site'): void
{
    extract($data, EXTR_SKIP);
    $__viewFile = dirname(__DIR__) . '/views/' . $template . '.php';
    if (!is_file($__viewFile)) {
        http_response_code(500);
        exit('الصفحة غير موجودة: ' . e($template));
    }

    ob_start();
    require $__viewFile;
    $content = ob_get_clean();

    require dirname(__DIR__) . '/views/layouts/' . $layout . '.php';
}

function partial(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require dirname(__DIR__) . '/views/' . $template . '.php';
}

// ===== التنسيق =====

function num($value): string
{
    return number_format((float) $value);
}

/** تنسيق التاريخ بالعربية */
function ar_date($value, bool $withTime = false): string
{
    if (!$value) {
        return '—';
    }
    $timestamp = is_numeric($value) ? (int) $value : strtotime((string) $value);
    if (!$timestamp) {
        return '—';
    }
    $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    $text = date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
    return $withTime ? $text . ' - ' . date('h:i A', $timestamp) : $text;
}

function ar_day_name(string $date): string
{
    $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
    return $days[(int) date('w', strtotime($date))];
}

function today(): string
{
    return date('Y-m-d');
}

/** رابط رسالة واتساب جاهزة */
function whatsapp_link(?string $phone, string $message): string
{
    $number = preg_replace('/\D+/', '', (string) $phone);
    if ($number === '') {
        return '#';
    }
    if (str_starts_with($number, '0')) {
        $number = '966' . substr($number, 1);
    }
    return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
}

/** اختصار الاسم إلى حرف للأفاتار */
function initials(string $name): string
{
    $name = trim($name);
    return $name === '' ? '؟' : mb_substr($name, 0, 1, 'UTF-8');
}

function random_token(int $length = 16): string
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

/** تسميات ثابتة */
function label(string $group, string $key): string
{
    $labels = [
        'entity_type' => [
            'school' => 'مدرسة',
            'quran'  => 'حلقة تحفيظ',
            'women'  => 'دار نسائية',
            'sports' => 'أكاديمية رياضية',
            'club'   => 'نادٍ صيفي',
            'other'  => 'جهة أخرى',
        ],
        'point_category' => [
            'behavior'      => 'سلوك',
            'attendance'    => 'حضور',
            'memorization'  => 'تسميع',
            'homework'      => 'واجبات',
            'participation' => 'مشاركة',
            'penalty'       => 'مخالفات',
            'other'         => 'أخرى',
        ],
        'attendance' => [
            'present' => 'حاضر',
            'late'    => 'متأخر',
            'absent'  => 'غائب',
            'excused' => 'بعذر',
        ],
        'redemption' => [
            'pending'   => 'بانتظار الاعتماد',
            'approved'  => 'معتمد',
            'delivered' => 'تم التسليم',
            'rejected'  => 'مرفوض',
        ],
        'submission' => [
            'pending'   => 'لم يسلّم',
            'submitted' => 'سلّم',
            'graded'    => 'مُصحح',
            'late'      => 'متأخر',
        ],
        'role' => [
            'admin'      => 'مدير الجهة',
            'supervisor' => 'مشرف',
        ],
    ];
    return $labels[$group][$key] ?? $key;
}

/**
 * إخراج ملف CSV للتحميل (يفتح مباشرة في Excel).
 * تُضاف علامة BOM حتى تظهر الحروف العربية بشكل صحيح.
 */
function csv_download(string $filename, array $headers, array $rows): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, $headers);
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
