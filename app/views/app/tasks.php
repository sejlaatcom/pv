<div class="panel" style="margin-bottom:16px">
  <form method="post" action="<?= url('/tasks') ?>" class="form-inline">
    <?= csrf_field() ?>
    <div class="field" style="margin:0">
      <label>عنوان المهمة *</label>
      <input class="input" name="title" placeholder="حفظ سورة الملك" required>
    </div>
    <div class="field" style="margin:0">
      <label>المجموعة</label>
      <select class="select" name="group_id">
        <option value="">جميع الطلاب</option>
        <?php foreach ($groups as $group): ?>
          <option value="<?= (int) $group['id'] ?>"><?= e($group['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field" style="margin:0;max-width:170px">
      <label>تاريخ التسليم</label>
      <input class="input" type="date" name="due_date">
    </div>
    <div class="field" style="margin:0;max-width:120px">
      <label>الدرجة</label>
      <input class="input" type="number" name="max_score" value="10">
    </div>
    <div class="field" style="margin:0;max-width:130px">
      <label>النقاط</label>
      <input class="input" type="number" name="points" value="10">
    </div>
    <button class="btn btn-primary" type="submit">➕ إضافة مهمة</button>
  </form>
</div>

<?php if (!$tasks): ?>
  <div class="panel center"><p class="muted">لا توجد مهام بعد.</p></div>
<?php else: ?>
  <div class="grid">
    <?php foreach ($tasks as $task): ?>
      <div class="card card-pad">
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:16px">
          <div class="feature-icon" style="background:rgba(13,148,136,.12);color:var(--brand-dark)">📝</div>
          <div style="flex:1;min-width:200px">
            <h3><?= e($task['title']) ?></h3>
            <p class="muted small"><?= e($task['description']) ?></p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
              <span class="tag tag-brand"><?= e($task['group_name'] ?: 'جميع الطلاب') ?></span>
              <span class="tag tag-gold">⭐ <?= (int) $task['points'] ?> نقطة</span>
              <?php if ($task['due_date']): ?>
                <span class="tag tag-gray">التسليم: <?= e(ar_date($task['due_date'])) ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div style="width:200px">
            <div class="small" style="display:flex;justify-content:space-between;margin-bottom:6px">
              <span class="muted">التصحيح</span>
              <b><?= (int) $task['graded'] ?> / <?= (int) $task['total'] ?></b>
            </div>
            <?= progress_bar((int) $task['graded'], max(1, (int) $task['total'])) ?>
          </div>
          <a class="btn btn-outline btn-sm" href="<?= url('/tasks/' . (int) $task['id']) ?>">رصد الدرجات</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
