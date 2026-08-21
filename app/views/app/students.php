<div class="panel" style="margin-bottom:16px">
  <form method="get" action="<?= url('/students') ?>" class="form-inline">
    <input class="input" style="max-width:280px" name="q" value="<?= e($filters['search'] ?? '') ?>" placeholder="ابحث بالاسم أو الرقم...">
    <select class="select" style="max-width:220px" name="group_id" onchange="this.form.submit()">
      <option value="">جميع المجموعات</option>
      <?php foreach ($groups as $group): ?>
        <option value="<?= (int) $group['id'] ?>" <?= (int) ($filters['group_id'] ?? 0) === (int) $group['id'] ? 'selected' : '' ?>>
          <?= e($group['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-outline btn-sm" type="submit">بحث</button>
    <div style="margin-inline-start:auto;display:flex;gap:8px">
      <a class="btn btn-outline btn-sm"
         href="<?= url('/export/students' . (!empty($filters['group_id']) ? '?group_id=' . (int) $filters['group_id'] : '')) ?>">⬇️ تصدير Excel</a>
      <button type="button" class="btn btn-outline btn-sm" data-open="#import-modal">⬆️ استيراد قائمة</button>
      <button type="button" class="btn btn-primary btn-sm" data-open="#add-modal">➕ إضافة طالب</button>
    </div>
  </form>
</div>

<?php if (!$students): ?>
  <div class="panel center">
    <p class="muted">لا يوجد طلاب مطابقون.</p>
    <button type="button" class="btn btn-primary btn-sm mt-4" data-open="#add-modal">إضافة أول طالب</button>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <table class="data" id="students-table">
      <thead>
        <tr>
          <th>#</th><th>الطالب</th><th>المجموعة</th><th>ولي الأمر</th><th>النقاط</th><th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $i => $student): ?>
          <tr>
            <td class="muted"><?= $i + 1 ?></td>
            <td>
              <a class="cell-user" href="<?= url('/students/' . (int) $student['id']) ?>">
                <span class="avatar"><?= e(initials($student['name'])) ?></span>
                <span>
                  <?= e($student['name']) ?>
                  <?php if ($student['code']): ?><small class="muted" style="display:block"><?= e($student['code']) ?></small><?php endif; ?>
                </span>
              </a>
            </td>
            <td class="muted"><?= e($student['group_name'] ?: '—') ?></td>
            <td class="muted" dir="ltr" style="text-align:right"><?= e($student['guardian_phone'] ?: '—') ?></td>
            <td><b style="color:var(--brand-dark)"><?= num($student['total_points']) ?></b></td>
            <td>
              <div style="display:flex;gap:6px;align-items:center">
                <button type="button" class="btn btn-outline btn-sm"
                        data-copy="<?= e(absolute_url('/p/' . $student['access_token'])) ?>">🔗 رابط ولي الأمر</button>
                <?php if ($student['guardian_phone']): ?>
                  <a class="btn btn-outline btn-sm" target="_blank" rel="noopener"
                     href="<?= e(whatsapp_link($student['guardian_phone'], 'متابعة ' . $student['name'] . ' — الرصيد الحالي ' . $student['total_points'] . ' نقطة: ' . absolute_url('/p/' . $student['access_token']))) ?>">💬</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<dialog class="modal" id="add-modal">
  <form method="post" action="<?= url('/students') ?>">
    <?= csrf_field() ?>
    <div class="modal-head">
      <h3>إضافة طالب جديد</h3>
      <button type="button" class="modal-close" data-close>×</button>
    </div>
    <div class="modal-body form-grid">
      <div class="field full">
        <label>الاسم *</label>
        <input class="input" name="name" required>
      </div>
      <div class="field">
        <label>المجموعة</label>
        <select class="select" name="group_id">
          <option value="">بدون مجموعة</option>
          <?php foreach ($groups as $group): ?>
            <option value="<?= (int) $group['id'] ?>"><?= e($group['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>رقم الطالب</label>
        <input class="input" name="code">
      </div>
      <div class="field">
        <label>الجنس</label>
        <select class="select" name="gender">
          <option value="male">طالب</option>
          <option value="female">طالبة</option>
        </select>
      </div>
      <div class="field">
        <label>اسم ولي الأمر</label>
        <input class="input" name="guardian_name">
      </div>
      <div class="field full">
        <label>جوال ولي الأمر</label>
        <input class="input" type="tel" name="guardian_phone" placeholder="05xxxxxxxx">
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-primary" type="submit">إضافة</button>
      <button class="btn btn-outline" type="button" data-close>إلغاء</button>
    </div>
  </form>
</dialog>

<dialog class="modal" id="import-modal">
  <form method="post" action="<?= url('/students/import') ?>">
    <?= csrf_field() ?>
    <div class="modal-head">
      <h3>استيراد قائمة الطلاب</h3>
      <button type="button" class="modal-close" data-close>×</button>
    </div>
    <div class="modal-body">
      <p class="muted small">
        الصق الأسماء سطراً لكل طالب. يمكنك إضافة رقم الطالب وجوال ولي الأمر مفصولة بفاصلة:
        <br><code dir="rtl">عبدالله الحربي، ST-1001، 0551234567</code>
      </p>
      <div class="field">
        <label>المجموعة</label>
        <select class="select" name="group_id">
          <option value="">بدون مجموعة</option>
          <?php foreach ($groups as $group): ?>
            <option value="<?= (int) $group['id'] ?>"><?= e($group['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>الأسماء</label>
        <textarea class="textarea" name="rows" rows="10" placeholder="عبدالله الحربي&#10;محمد العتيبي&#10;سلطان القحطاني"></textarea>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-primary" type="submit">استيراد</button>
      <button class="btn btn-outline" type="button" data-close>إلغاء</button>
    </div>
  </form>
</dialog>
