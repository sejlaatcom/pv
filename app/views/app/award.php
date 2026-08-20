<form method="post" action="<?= url('/award') ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="group_id" value="<?= (int) ($groupId ?? 0) ?>">

  <div class="award-layout">
    <div>
      <div class="panel" style="margin-bottom:16px">
        <div class="chips-row" style="margin-bottom:14px">
          <a class="chip-btn<?= !$groupId ? ' active' : '' ?>" href="<?= url('/award') ?>">جميع المجموعات</a>
          <?php foreach ($groups as $group): ?>
            <a class="chip-btn<?= (int) $groupId === (int) $group['id'] ? ' active' : '' ?>"
               href="<?= url('/award?group_id=' . (int) $group['id']) ?>">
              <?= e($group['name']) ?> (<?= (int) $group['student_count'] ?>)
            </a>
          <?php endforeach; ?>
        </div>

        <div class="form-inline">
          <input class="input" style="max-width:260px" placeholder="بحث سريع بالاسم..." data-filter-target="#student-list">
          <button type="button" class="btn btn-outline btn-sm" data-select-all>تحديد الكل</button>
          <span class="muted small">المحددون: <b data-selected-count>0</b></span>
        </div>
      </div>

      <?php if (!$students): ?>
        <div class="panel center">
          <p class="muted">لا يوجد طلاب في هذه المجموعة.</p>
          <a class="btn btn-primary btn-sm mt-4" href="<?= url('/students') ?>">إضافة طلاب</a>
        </div>
      <?php else: ?>
        <div class="student-grid" id="student-list">
          <?php foreach ($students as $student): ?>
            <label class="student-pick" data-name="<?= e($student['name']) ?>">
              <input type="checkbox" name="student_ids[]" value="<?= (int) $student['id'] ?>">
              <span class="box">
                <span class="who"><?= e(initials($student['name'])) ?></span>
                <span class="nm"><?= e($student['name']) ?></span>
                <span class="gp"><?= e($student['group_name'] ?: 'بدون مجموعة') ?></span>
                <span class="pt"><?= num($student['total_points']) ?></span>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="sticky-side">
      <div class="panel" style="margin-bottom:14px">
        <div class="panel-head"><h2>البنود الإيجابية</h2><span>➕</span></div>
        <div class="item-grid">
          <?php foreach ($items as $item): if ((int) $item['points'] < 0) continue; ?>
            <label class="item-pick">
              <input type="radio" name="item_id" value="<?= (int) $item['id'] ?>">
              <span class="box">
                <span><?= e($item['title']) ?></span>
                <span class="val val-plus">+<?= (int) $item['points'] ?></span>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="panel" style="margin-bottom:14px">
        <div class="panel-head"><h2>البنود السلبية</h2><span>➖</span></div>
        <div class="item-grid">
          <?php foreach ($items as $item): if ((int) $item['points'] >= 0) continue; ?>
            <label class="item-pick">
              <input type="radio" name="item_id" value="<?= (int) $item['id'] ?>">
              <span class="box">
                <span><?= e($item['title']) ?></span>
                <span class="val val-minus"><?= (int) $item['points'] ?></span>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
        <?php if (!$items): ?>
          <p class="muted small">لا توجد بنود. <a href="<?= url('/point-items') ?>">أضف بنوداً</a></p>
        <?php endif; ?>
      </div>

      <div class="panel">
        <div class="field">
          <label for="points">أو أدخل قيمة مخصصة</label>
          <input class="input" type="number" id="points" name="points" placeholder="مثال: 15 أو 5-">
        </div>
        <div class="field">
          <label for="reason">السبب / الملاحظة</label>
          <input class="input" id="reason" name="reason" placeholder="اختياري">
        </div>
        <button class="btn btn-primary btn-block" type="submit">⚡ رصد النقاط</button>
      </div>
    </div>
  </div>
</form>
