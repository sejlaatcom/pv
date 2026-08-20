<?php $old = $old ?? []; $errors = $errors ?? []; ?>
<div class="auth-card wide">
  <a href="<?= url('/') ?>" class="logo" style="justify-content:center;margin-bottom:22px">
    <span class="logo-mark">⭐</span>
    <span><b><?= e(brand('name')) ?></b><small><?= e(brand('tagline')) ?></small></span>
  </a>

  <h1>أنشئ حساب جهتك</h1>
  <p class="sub">تجربة مجانية 14 يوماً، وسنجهّز لك بنود النقاط والأوسمة والجوائز تلقائياً.</p>

  <form method="post" action="<?= url('/register') ?>" class="form-grid">
    <?= csrf_field() ?>

    <div class="field">
      <label for="entity_name">اسم الجهة *</label>
      <input class="input" id="entity_name" name="entity_name" value="<?= e($old['entity_name'] ?? '') ?>" placeholder="مدرسة الأندلس" required>
      <?php if (!empty($errors['entity_name'])): ?><div class="field-error"><?= e($errors['entity_name']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label for="entity_type">نوع الجهة</label>
      <select class="select" id="entity_type" name="entity_type">
        <?php foreach (['school', 'quran', 'women', 'sports', 'club', 'other'] as $type): ?>
          <option value="<?= $type ?>" <?= ($old['entity_type'] ?? '') === $type ? 'selected' : '' ?>><?= e(label('entity_type', $type)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="field">
      <label for="city">المدينة</label>
      <input class="input" id="city" name="city" value="<?= e($old['city'] ?? '') ?>">
    </div>

    <div class="field">
      <label for="phone">رقم الجوال</label>
      <input class="input" type="tel" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>">
    </div>

    <div class="field">
      <label for="name">اسمك (مدير الجهة) *</label>
      <input class="input" id="name" name="name" value="<?= e($old['name'] ?? '') ?>" required>
      <?php if (!empty($errors['name'])): ?><div class="field-error"><?= e($errors['name']) ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label for="email">البريد الإلكتروني *</label>
      <input class="input" type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
      <?php if (!empty($errors['email'])): ?><div class="field-error"><?= e($errors['email']) ?></div><?php endif; ?>
    </div>

    <div class="field full">
      <label for="password">كلمة المرور * <span class="muted small">(6 أحرف فأكثر)</span></label>
      <input class="input" type="password" id="password" name="password" required>
      <?php if (!empty($errors['password'])): ?><div class="field-error"><?= e($errors['password']) ?></div><?php endif; ?>
    </div>

    <div class="full">
      <button class="btn btn-primary btn-block" type="submit">إنشاء الحساب</button>
    </div>
  </form>

  <div class="auth-foot">
    لديك حساب؟ <a href="<?= url('/login') ?>">سجّل الدخول</a>
  </div>
</div>
