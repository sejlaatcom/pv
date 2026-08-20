<div class="panel" style="margin-bottom:16px">
  <form method="post" action="<?= url('/groups') ?>" class="form-inline">
    <?= csrf_field() ?>
    <div class="field" style="margin:0">
      <label>اسم المجموعة *</label>
      <input class="input" name="name" placeholder="أول متوسط - أ" required>
    </div>
    <div class="field" style="margin:0">
      <label>المرحلة / النوع</label>
      <input class="input" name="level" placeholder="متوسط، تحفيظ، نشاط">
    </div>
    <div class="field" style="margin:0">
      <label>المشرف</label>
      <select class="select" name="supervisor_id">
        <option value="">بدون</option>
        <?php foreach ($supervisors as $supervisor): ?>
          <option value="<?= (int) $supervisor['id'] ?>"><?= e($supervisor['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary" type="submit">➕ إضافة مجموعة</button>
  </form>
</div>

<?php if (!$groups): ?>
  <div class="panel center"><p class="muted">لا توجد مجموعات بعد — أضف أول مجموعة من الأعلى.</p></div>
<?php else: ?>
  <div class="grid grid-3">
    <?php foreach ($groups as $group): $color = $group['color'] ?: '#0d9488'; ?>
      <div class="card card-pad card-hover">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
          <div class="feature-icon" style="background:<?= e($color) ?>22;color:<?= e($color) ?>">🗂️</div>
          <?php if (is_admin()): ?>
            <form method="post" action="<?= url('/groups/' . (int) $group['id'] . '/delete') ?>"
                  data-confirm="حذف المجموعة؟ سيبقى الطلاب بدون مجموعة.">
              <?= csrf_field() ?>
              <button class="btn btn-danger btn-sm" type="submit">🗑️</button>
            </form>
          <?php endif; ?>
        </div>
        <h3><?= e($group['name']) ?></h3>
        <p class="muted small"><?= e($group['description'] ?: $group['level'] ?: '—') ?></p>
        <?php if ($group['supervisor_name']): ?>
          <p class="small mt-4">👤 المشرف: <b><?= e($group['supervisor_name']) ?></b></p>
        <?php endif; ?>

        <div class="grid grid-3 mt-4" style="gap:8px;text-align:center">
          <div style="background:#f3f4f6;border-radius:12px;padding:8px">
            <b><?= (int) $group['student_count'] ?></b>
            <div class="muted" style="font-size:11px">طالب</div>
          </div>
          <div style="background:#f3f4f6;border-radius:12px;padding:8px">
            <b><?= num($group['total_points']) ?></b>
            <div class="muted" style="font-size:11px">نقطة</div>
          </div>
          <div style="background:#f3f4f6;border-radius:12px;padding:8px">
            <b><?= (int) $group['student_count'] > 0 ? num(round((int) $group['total_points'] / (int) $group['student_count'])) : 0 ?></b>
            <div class="muted" style="font-size:11px">المعدل</div>
          </div>
        </div>

        <div style="display:flex;gap:8px;margin-top:14px">
          <a class="btn btn-outline btn-sm" href="<?= url('/students?group_id=' . (int) $group['id']) ?>">الطلاب</a>
          <a class="btn btn-outline btn-sm" href="<?= url('/award?group_id=' . (int) $group['id']) ?>">⚡ رصد</a>
          <a class="btn btn-outline btn-sm" href="<?= url('/attendance?group_id=' . (int) $group['id']) ?>">📅 حضور</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
