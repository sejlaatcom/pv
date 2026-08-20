<section class="section center-block">
  <div class="container narrow center">
    <div class="error-code">404</div>
    <h1>الصفحة غير موجودة</h1>
    <p class="muted"><?= e($message ?? 'تأكد من الرابط أو عد إلى الصفحة الرئيسية.') ?></p>
    <a class="btn btn-primary" href="<?= url('/') ?>">العودة للرئيسية</a>
  </div>
</section>
