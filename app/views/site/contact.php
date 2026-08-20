<?php $old = $old ?? []; $errors = $errors ?? []; ?>
<section class="hero">
  <div class="container center" style="padding:56px 20px">
    <span class="hero-badge">💬 تواصل معنا</span>
    <h1 style="font-size:40px;color:#fff">نسعد بخدمتك</h1>
    <p class="lead" style="margin:12px auto 0">اطلب عرضاً تقديمياً أو استفسر عن الاشتراك.</p>
  </div>
</section>

<section class="section">
  <div class="container grid" style="grid-template-columns:2fr 1fr">
    <div class="card card-pad">
      <h2 style="font-size:20px;margin-bottom:18px">أرسل رسالتك</h2>
      <form method="post" action="<?= url('/contact') ?>" class="form-grid">
        <?= csrf_field() ?>
        <div class="field">
          <label for="name">الاسم *</label>
          <input class="input" id="name" name="name" value="<?= e($old['name'] ?? '') ?>" required>
          <?php if (!empty($errors['name'])): ?><div class="field-error"><?= e($errors['name']) ?></div><?php endif; ?>
        </div>
        <div class="field">
          <label for="email">البريد الإلكتروني *</label>
          <input class="input" type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
          <?php if (!empty($errors['email'])): ?><div class="field-error"><?= e($errors['email']) ?></div><?php endif; ?>
        </div>
        <div class="field">
          <label for="phone">رقم الجوال</label>
          <input class="input" type="tel" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" placeholder="05xxxxxxxx">
        </div>
        <div class="field">
          <label for="entity_type">نوع الجهة</label>
          <select class="select" id="entity_type" name="entity_type">
            <option value="">اختر نوع الجهة</option>
            <?php foreach (['مدرسة', 'حلقة تحفيظ', 'دار نسائية', 'أكاديمية رياضية', 'نادٍ صيفي', 'أخرى'] as $type): ?>
              <option value="<?= e($type) ?>" <?= ($old['entity_type'] ?? '') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field full">
          <label for="subject">الموضوع</label>
          <input class="input" id="subject" name="subject" value="<?= e($old['subject'] ?? '') ?>" placeholder="طلب عرض تقديمي">
        </div>
        <div class="field full">
          <label for="message">الرسالة *</label>
          <textarea class="textarea" id="message" name="message" required><?= e($old['message'] ?? '') ?></textarea>
          <?php if (!empty($errors['message'])): ?><div class="field-error"><?= e($errors['message']) ?></div><?php endif; ?>
        </div>
        <div class="full">
          <button class="btn btn-primary" type="submit">إرسال الرسالة</button>
        </div>
      </form>
    </div>

    <div class="grid" style="align-content:start">
      <div class="card card-pad">
        <div class="muted small">الجوال والواتساب</div>
        <b dir="ltr"><?= e(brand('phone')) ?></b>
      </div>
      <div class="card card-pad">
        <div class="muted small">البريد الإلكتروني</div>
        <b dir="ltr"><?= e(brand('email')) ?></b>
      </div>
      <a class="card card-pad card-hover" href="<?= e(whatsapp_link(brand('whatsapp'), 'السلام عليكم، أود الاستفسار عن منصة ' . brand('name'))) ?>" target="_blank" rel="noopener">
        <b>💬 راسلنا على الواتساب</b>
        <div class="muted small">رد سريع خلال ساعات العمل</div>
      </a>
      <a class="card card-pad card-hover" href="<?= url('/student-login') ?>">
        <b>🎓 دخول الطالب</b>
        <div class="muted small">ادخل برمز المتابعة الخاص بك</div>
      </a>
    </div>
  </div>
</section>
