<?php $old = $old ?? []; ?>
<div class="auth-card">
  <a href="<?= url('/') ?>" class="logo" style="justify-content:center;margin-bottom:22px">
    <span class="logo-mark">⭐</span>
    <span><b><?= e(brand('name')) ?></b><small><?= e(brand('tagline')) ?></small></span>
  </a>

  <h1>مرحباً بعودتك</h1>
  <p class="sub">سجّل دخولك للوصول إلى لوحة تحكم جهتك ورصد النقاط.</p>

  <?php if (!empty($error)): ?>
    <div class="flash flash-error" style="position:static;margin-bottom:16px"><span><?= e($error) ?></span></div>
  <?php endif; ?>

  <form method="post" action="<?= url('/login') ?>">
    <?= csrf_field() ?>
    <div class="field">
      <label for="email">البريد الإلكتروني</label>
      <input class="input" type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autofocus>
    </div>
    <div class="field">
      <label for="password">كلمة المرور</label>
      <input class="input" type="password" id="password" name="password" required>
    </div>
    <button class="btn btn-primary btn-block" type="submit">تسجيل الدخول</button>
  </form>

  <div class="auth-foot">
    ليس لديك حساب؟ <a href="<?= url('/register') ?>">أنشئ حساب جهتك مجاناً</a>
    <div class="muted small mt-4">طالب؟ <a href="<?= url('/student-login') ?>">ادخل برمز المتابعة</a></div>
  </div>
</div>
