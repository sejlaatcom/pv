<section class="hero">
  <div class="container center" style="padding:48px 20px">
    <span class="hero-badge">📝 تسجيل جديد</span>
    <h1 style="font-size:34px;color:#fff"><?= e($entity['name']) ?></h1>
    <p class="lead" style="margin:10px auto 0">سجّل بياناتك للانضمام ومتابعة نقاطك وأوسمتك.</p>
  </div>
</section>

<section class="section">
  <div class="container narrow">
    <div class="card card-pad">
      <?php if (!empty($error)): ?>
        <div class="flash flash-error" style="position:static;margin-bottom:16px"><span><?= e($error) ?></span></div>
      <?php endif; ?>

      <form method="post" action="<?= url('/join/' . $entity['slug']) ?>" class="form-grid">
        <?= csrf_field() ?>
        <div class="field full">
          <label>الاسم الثلاثي *</label>
          <input class="input" name="name" value="<?= e($old['name'] ?? '') ?>" required>
        </div>
        <div class="field">
          <label>المجموعة</label>
          <select class="select" name="group_id">
            <option value="">— اختر —</option>
            <?php foreach ($groups as $group): ?>
              <option value="<?= (int) $group['id'] ?>" <?= (int) ($old['group_id'] ?? 0) === (int) $group['id'] ? 'selected' : '' ?>>
                <?= e($group['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>رقم الطالب (إن وجد)</label>
          <input class="input" name="code" value="<?= e($old['code'] ?? '') ?>">
        </div>
        <div class="field">
          <label>جوال ولي الأمر</label>
          <input class="input" type="tel" name="guardian_phone" value="<?= e($old['guardian_phone'] ?? '') ?>" placeholder="05xxxxxxxx">
        </div>
        <div class="field">
          <label>الجنس</label>
          <select class="select" name="gender">
            <option value="male">طالب</option>
            <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>طالبة</option>
          </select>
        </div>
        <div class="full">
          <button class="btn btn-primary btn-block" type="submit">تسجيل</button>
        </div>
      </form>
    </div>
  </div>
</section>
