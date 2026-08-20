<?php $brand = brand(); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? $brand['full_name']) ?></title>
<meta name="description" content="<?= e($brand['tagline']) ?>">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⭐</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <a href="<?= url('/') ?>" class="logo">
      <span class="logo-mark">⭐</span>
      <span>
        <b><?= e($brand['name']) ?></b>
        <small>نقاط · تحفيز · متابعة</small>
      </span>
    </a>

    <input type="checkbox" id="nav-toggle" class="nav-toggle">
    <label for="nav-toggle" class="nav-burger" aria-label="القائمة">☰</label>

    <nav class="site-nav">
      <a href="<?= url('/') ?>">الرئيسية</a>
      <a href="<?= url('/features') ?>">المميزات</a>
      <a href="<?= url('/pricing') ?>">الاشتراك</a>
      <a href="<?= url('/faq') ?>">الأسئلة الشائعة</a>
      <a href="<?= url('/contact') ?>">تواصل معنا</a>
      <?php if (is_logged_in()): ?>
        <a class="btn btn-primary btn-sm" href="<?= url('/dashboard') ?>">لوحة التحكم</a>
      <?php else: ?>
        <a class="btn btn-ghost btn-sm" href="<?= url('/login') ?>">تسجيل الدخول</a>
        <a class="btn btn-primary btn-sm" href="<?= url('/register') ?>">ابدأ مجاناً</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<?php partial('partials/flash'); ?>

<main><?= $content ?></main>

<footer class="site-footer">
  <div class="container trust-row">
    <div><span>🔒</span> خصوصية وحماية البيانات</div>
    <div><span>☁️</span> نسخ احتياطي يومي</div>
    <div><span>🎧</span> دعم فني متواصل</div>
    <div><span>🛡️</span> آمن وموثوق 100%</div>
  </div>
  <div class="container footer-grid">
    <div>
      <div class="logo logo-light">
        <span class="logo-mark">⭐</span>
        <span><b><?= e($brand['full_name']) ?></b><small><?= e($brand['tagline']) ?></small></span>
      </div>
      <p class="footer-about">
        منصة متكاملة لإدارة نقاط الطلاب والمشاركين: رصد النقاط، الأوسمة، متجر الجوائز، الحضور،
        المهام، المسابقات، والتقارير اللحظية — في مكان واحد.
      </p>
    </div>
    <div>
      <h4>روابط سريعة</h4>
      <ul>
        <li><a href="<?= url('/features') ?>">المميزات</a></li>
        <li><a href="<?= url('/pricing') ?>">الاشتراك والأسعار</a></li>
        <li><a href="<?= url('/faq') ?>">الأسئلة الشائعة</a></li>
        <li><a href="<?= url('/student-login') ?>">دخول الطالب</a></li>
      </ul>
    </div>
    <div>
      <h4>للتواصل والاستفسار</h4>
      <ul>
        <li>📱 <span dir="ltr"><?= e($brand['phone']) ?></span></li>
        <li>✉️ <span dir="ltr"><?= e($brand['email']) ?></span></li>
        <li>🌐 <span dir="ltr"><?= e($brand['website']) ?></span></li>
        <li>📷 <span dir="ltr"><?= e($brand['social']) ?></span></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      © <?= date('Y') ?> <?= e($brand['full_name']) ?> — جميع الحقوق محفوظة ·
      يخدم: المدارس · حلقات التحفيظ · الدور النسائية · الأكاديميات الرياضية · النوادي الصيفية
    </div>
  </div>
</footer>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
