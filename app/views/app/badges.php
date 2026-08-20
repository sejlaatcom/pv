<div class="panel" style="margin-bottom:16px">
  <form method="post" action="<?= url('/badges') ?>" class="form-inline">
    <?= csrf_field() ?>
    <div class="field" style="margin:0">
      <label>اسم الوسام *</label>
      <input class="input" name="title" placeholder="نجم الأسبوع" required>
    </div>
    <div class="field" style="margin:0">
      <label>الوصف</label>
      <input class="input" name="description" placeholder="الأعلى نقاطاً خلال الأسبوع">
    </div>
    <div class="field" style="margin:0;max-width:110px">
      <label>الأيقونة</label>
      <input class="input" name="icon" value="🏅" style="text-align:center">
    </div>
    <div class="field" style="margin:0;max-width:170px">
      <label>حد المنح التلقائي</label>
      <input class="input" type="number" name="required_points" placeholder="اختياري">
    </div>
    <button class="btn btn-primary" type="submit">➕ إضافة وسام</button>
  </form>
  <p class="muted small mt-4">إذا حددت حد النقاط، سيُمنح الوسام تلقائياً لكل طالب يبلغ هذا الرصيد.</p>
</div>

<?php if (!$badges): ?>
  <div class="panel center"><p class="muted">لا توجد أوسمة بعد.</p></div>
<?php else: ?>
  <div class="grid grid-4">
    <?php foreach ($badges as $badge): $color = $badge['color'] ?: '#f59e0b'; ?>
      <div class="card badge-card card-hover">
        <?php if (is_admin()): ?>
          <form method="post" action="<?= url('/badges/' . (int) $badge['id'] . '/delete') ?>" class="corner-action"
                data-confirm="حذف الوسام؟">
            <?= csrf_field() ?>
            <button class="btn btn-danger btn-sm" type="submit">🗑️</button>
          </form>
        <?php endif; ?>

        <div class="badge-emoji" style="background:<?= e($color) ?>22"><?= e($badge['icon']) ?></div>
        <h3><?= e($badge['title']) ?></h3>
        <p class="muted small" style="min-height:36px"><?= e($badge['description']) ?></p>
        <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap">
          <span class="tag tag-brand">مُنح <?= (int) $badge['awarded_count'] ?> مرة</span>
          <?php if ($badge['required_points'] !== null): ?>
            <span class="tag tag-gold">تلقائي عند <?= num($badge['required_points']) ?></span>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
