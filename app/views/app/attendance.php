<div class="grid grid-4" style="margin-bottom:16px">
  <div class="card stat">
    <div class="stat-icon" style="background:#dcfce7">✅</div>
    <div><div class="stat-label">حاضر</div><div class="stat-value" style="color:#15803d"><?= (int) $stats['present'] ?></div></div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:#fef3c7">⏰</div>
    <div><div class="stat-label">متأخر</div><div class="stat-value" style="color:var(--gold-dark)"><?= (int) $stats['late'] ?></div></div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:#fee2e2">🚫</div>
    <div><div class="stat-label">غائب</div><div class="stat-value" style="color:#b91c1c"><?= (int) $stats['absent'] ?></div></div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:#e0f2fe">📄</div>
    <div><div class="stat-label">بعذر</div><div class="stat-value" style="color:#075985"><?= (int) $stats['excused'] ?></div></div>
  </div>
</div>

<div class="panel" style="margin-bottom:16px">
  <form method="get" action="<?= url('/attendance') ?>" class="form-inline">
    <div class="field" style="margin:0">
      <label>التاريخ</label>
      <input class="input" type="date" name="day" value="<?= e($day) ?>" onchange="this.form.submit()">
    </div>
    <div class="field" style="margin:0">
      <label>المجموعة</label>
      <select class="select" name="group_id" onchange="this.form.submit()">
        <option value="">جميع المجموعات</option>
        <?php foreach ($groups as $group): ?>
          <option value="<?= (int) $group['id'] ?>" <?= (int) $groupId === (int) $group['id'] ? 'selected' : '' ?>>
            <?= e($group['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-outline btn-sm" type="submit">عرض</button>
    <span class="muted small"><?= e(ar_day_name($day)) ?> · <?= e(ar_date($day)) ?></span>
  </form>
</div>

<?php if (!$rows): ?>
  <div class="panel center"><p class="muted">لا يوجد طلاب لعرض كشف الحضور.</p></div>
<?php else: ?>
  <form method="post" action="<?= url('/attendance') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="day" value="<?= e($day) ?>">
    <input type="hidden" name="group_id" value="<?= (int) $groupId ?>">

    <div class="panel" style="margin-bottom:12px">
      <div class="form-inline">
        <select class="select" style="max-width:240px" data-mark-all>
          <option value="">تحديد حالة للجميع...</option>
          <option value="present">الجميع حاضر</option>
          <option value="late">الجميع متأخر</option>
          <option value="absent">الجميع غائب</option>
          <option value="excused">الجميع بعذر</option>
        </select>
        <button class="btn btn-primary" type="submit">💾 حفظ الكشف</button>
      </div>
    </div>

    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>الطالب</th><th>المجموعة</th><th>الحالة</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td>
                <a class="cell-user" href="<?= url('/students/' . (int) $row['student_id']) ?>">
                  <span class="avatar"><?= e(initials($row['name'])) ?></span>
                  <span><?= e($row['name']) ?></span>
                </a>
              </td>
              <td class="muted"><?= e($row['group_name'] ?: '—') ?></td>
              <td>
                <div class="att-options">
                  <?php foreach (['present', 'late', 'absent', 'excused'] as $status): ?>
                    <label class="att-opt">
                      <input type="radio" name="status[<?= (int) $row['student_id'] ?>]" value="<?= $status ?>"
                             <?= $row['status'] === $status ? 'checked' : '' ?>>
                      <span class="<?= $status ?>"><?= e(label('attendance', $status)) ?></span>
                    </label>
                  <?php endforeach; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </form>
<?php endif; ?>
