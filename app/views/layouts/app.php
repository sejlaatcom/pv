<?php
$navUser = current_user();
$currentPath = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '', '/');
$basePath = rtrim(config('app')['base_url'], '/');
if ($basePath !== '' && str_starts_with($currentPath, $basePath)) {
    $currentPath = '/' . trim(substr($currentPath, strlen($basePath)), '/');
}

$navItems = [
    ['/dashboard',   '📊', 'لوحة القيادة'],
    ['/award',       '⚡', 'رصد النقاط'],
    ['/students',    '👥', 'الطلاب'],
    ['/groups',      '🗂️', 'المجموعات'],
    ['/attendance',  '📅', 'الحضور والانصراف'],
    ['/leaderboard', '🏆', 'لوحة المتصدرين'],
    ['/badges',      '🎖️', 'الأوسمة'],
    ['/rewards',     '🎁', 'متجر الجوائز'],
    ['/tasks',       '📝', 'المهام والدرجات'],
    ['/quizzes',     '❓', 'المسابقات والاستبانات'],
    ['/reports',     '📈', 'التقارير'],
    ['/point-items', '⚙️', 'بنود النقاط'],
    ['/settings',    '🛠️', 'الإعدادات'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'لوحة التحكم') ?> — <?= e(brand('name')) ?></title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⭐</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="app-body">

<input type="checkbox" id="side-toggle" class="side-toggle">

<aside class="sidebar">
  <a href="<?= url('/dashboard') ?>" class="logo sidebar-logo">
    <span class="logo-mark">⭐</span>
    <span>
      <b><?= e(brand('name')) ?></b>
      <small><?= e($navUser['entity_name'] ?? '') ?></small>
    </span>
  </a>

  <nav class="sidebar-nav">
    <?php foreach ($navItems as [$href, $icon, $label]): ?>
      <a href="<?= url($href) ?>" class="side-link<?= $currentPath === $href || str_starts_with($currentPath, $href . '/') ? ' active' : '' ?>">
        <span class="side-icon"><?= $icon ?></span><?= e($label) ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <div class="sidebar-foot">
    <a class="side-link" href="<?= url('/') ?>"><span class="side-icon">🌐</span>الموقع التعريفي</a>
    <form method="post" action="<?= url('/logout') ?>">
      <?= csrf_field() ?>
      <button type="submit" class="side-link side-logout"><span class="side-icon">🚪</span>تسجيل الخروج</button>
    </form>
  </div>
</aside>

<label for="side-toggle" class="side-backdrop"></label>

<div class="app-main">
  <header class="app-header">
    <label for="side-toggle" class="side-burger" aria-label="القائمة">☰</label>
    <div class="app-title">
      <h1><?= e($title ?? '') ?></h1>
      <?php if (!empty($subtitle)): ?><p><?= e($subtitle) ?></p><?php endif; ?>
    </div>
    <div class="app-user">
      <span class="user-chip">
        <b><?= e($navUser['name'] ?? '') ?></b>
        <small><?= e(label('role', $navUser['role'] ?? 'supervisor')) ?></small>
      </span>
    </div>
  </header>

  <?php partial('partials/flash'); ?>

  <div class="app-content"><?= $content ?></div>
</div>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
