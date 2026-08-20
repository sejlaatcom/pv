<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تهيئة المنصة</title>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="auth-body">
<div class="auth-card" style="max-width:640px">
  <h1>لم يتم تهيئة قاعدة البيانات بعد</h1>
  <p class="muted">اتبع الخطوات التالية مرة واحدة فقط:</p>
  <ol class="install-steps">
    <li>أنشئ قاعدة بيانات MySQL جديدة من لوحة الاستضافة (مثلاً <code>noqati</code>).</li>
    <li>افتح الملف <code>app/config.php</code> واكتب اسم القاعدة والمستخدم وكلمة المرور.</li>
    <li>استورد الملف <code>database/schema.sql</code> عبر phpMyAdmin، أو نفّذ في الطرفية:
      <br><code dir="ltr">php database/install.php --demo</code>
    </li>
  </ol>
  <p class="muted">الخيار <code dir="ltr">--demo</code> يضيف بيانات تجريبية جاهزة لتجربة المنصة فوراً.</p>
  <a class="btn btn-primary" href="<?= url('/') ?>">إعادة المحاولة</a>
</div>
</body>
</html>
