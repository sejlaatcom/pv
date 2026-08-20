<div class="auth-card">
  <a href="<?= url('/') ?>" class="logo" style="justify-content:center;margin-bottom:22px">
    <span class="logo-mark">⭐</span>
    <span><b><?= e(brand('name')) ?></b><small>بوابة الطالب وولي الأمر</small></span>
  </a>

  <h1>دخول الطالب</h1>
  <p class="sub">أدخل رمز المتابعة الذي زوّدك به المشرف لعرض نقاطك وأوسمتك.</p>

  <?php if (!empty($error)): ?>
    <div class="flash flash-error" style="position:static;margin-bottom:16px"><span><?= e($error) ?></span></div>
  <?php endif; ?>

  <form method="post" action="<?= url('/student-login') ?>">
    <?= csrf_field() ?>
    <div class="field">
      <label for="token">رمز المتابعة</label>
      <input class="input" id="token" name="token" dir="ltr" style="text-align:center;letter-spacing:2px" required autofocus>
    </div>
    <button class="btn btn-primary btn-block" type="submit">دخول</button>
  </form>

  <div class="auth-foot">
    <a href="<?= url('/') ?>">العودة للصفحة الرئيسية</a>
  </div>
</div>
