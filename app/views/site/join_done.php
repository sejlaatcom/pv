<?php $link = absolute_url('/p/' . $student['access_token']); ?>
<section class="section">
  <div class="container narrow">
    <div class="card card-pad center">
      <div style="font-size:56px"><?= $repeat ? '👋' : '🎉' ?></div>
      <h1 style="font-size:26px;margin-bottom:8px">
        <?= $repeat ? 'أنت مسجّل مسبقاً' : 'تم تسجيلك بنجاح' ?>
      </h1>
      <p class="muted">مرحباً <b><?= e($student['name']) ?></b> في <?= e($entity['name']) ?></p>

      <div class="card card-pad mt-6" style="background:#f9fafb">
        <h3 style="font-size:16px;margin-bottom:8px">رابط متابعة نقاطك</h3>
        <p class="muted small">احفظ هذا الرابط أو أرسله لولي أمرك — يعرض نقاطك وأوسمتك وحضورك لحظياً.</p>
        <div class="small" style="background:#fff;border:1px solid var(--line);border-radius:12px;padding:10px;margin:12px 0;word-break:break-all" dir="ltr">
          <?= e($link) ?>
        </div>
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
          <a class="btn btn-primary" href="<?= e($link) ?>">فتح صفحتي</a>
          <button type="button" class="btn btn-outline" data-copy="<?= e($link) ?>">نسخ الرابط</button>
        </div>
        <p class="muted small mt-4">
          رمز الدخول من صفحة <a href="<?= url('/student-login') ?>">دخول الطالب</a>:
          <b dir="ltr"><?= e($student['access_token']) ?></b>
        </p>
      </div>
    </div>
  </div>
</section>
