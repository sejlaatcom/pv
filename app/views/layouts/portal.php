<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? '') ?> — <?= e(brand('name')) ?></title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⭐</text></svg>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="portal-body">
<header class="portal-header">
  <div class="container portal-header-inner">
    <div class="logo logo-light">
      <span class="logo-mark">⭐</span>
      <span>
        <b><?= e($student['entity_name'] ?? brand('name')) ?></b>
        <small>متابعة النقاط والتحفيز</small>
      </span>
    </div>
    <a class="btn btn-ghost btn-sm" href="<?= url('/p/' . ($student['access_token'] ?? '')) ?>">الرئيسية</a>
  </div>
</header>

<?php partial('partials/flash'); ?>

<main class="container portal-main"><?= $content ?></main>

<footer class="portal-footer">
  <div class="container">مدعوم بواسطة <b><?= e(brand('full_name')) ?></b></div>
</footer>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
