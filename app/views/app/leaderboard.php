<?php $periods = ['week' => 'هذا الأسبوع', 'month' => 'هذا الشهر', 'term' => 'الفصل الدراسي', 'all' => 'الإجمالي']; ?>

<div class="panel" style="margin-bottom:16px">
  <div class="form-inline">
    <div class="chips-row">
      <?php foreach ($periods as $key => $labelText): ?>
        <a class="chip-btn<?= $period === $key ? ' active' : '' ?>"
           href="<?= url('/leaderboard?period=' . $key . ($groupId ? '&group_id=' . (int) $groupId : '')) ?>">
          <?= e($labelText) ?>
        </a>
      <?php endforeach; ?>
    </div>
    <a class="btn btn-outline btn-sm" style="margin-inline-start:auto"
       href="<?= url('/export/leaderboard?period=' . e($period) . ($groupId ? '&group_id=' . (int) $groupId : '')) ?>">⬇️ تصدير Excel</a>
    <form method="get" action="<?= url('/leaderboard') ?>">
      <input type="hidden" name="period" value="<?= e($period) ?>">
      <select class="select" name="group_id" onchange="this.form.submit()">
        <option value="">جميع المجموعات</option>
        <?php foreach ($groups as $group): ?>
          <option value="<?= (int) $group['id'] ?>" <?= (int) $groupId === (int) $group['id'] ? 'selected' : '' ?>>
            <?= e($group['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
</div>

<?php if (!$rows): ?>
  <div class="panel center"><p class="muted">لا توجد نتائج — ابدأ برصد النقاط.</p></div>
<?php else: ?>
  <?php $podium = array_slice($rows, 0, 3); $colors = ['#f59e0b', '#9ca3af', '#b45309']; $heights = [120, 92, 74]; ?>
  <div class="panel">
    <div class="podium">
      <?php foreach ([1, 0, 2] as $index): if (empty($podium[$index])) continue; $row = $podium[$index]; ?>
        <a class="podium-col" href="<?= url('/students/' . (int) $row['id']) ?>">
          <div class="podium-medal"><?= ['🥇', '🥈', '🥉'][$index] ?></div>
          <div class="podium-face" style="background:<?= $colors[$index] ?>"><?= e(initials($row['name'])) ?></div>
          <div class="podium-name"><?= e($row['name']) ?></div>
          <div class="podium-group"><?= e($row['group_name'] ?: '—') ?></div>
          <div class="podium-bar" style="height:<?= $heights[$index] ?>px;background:linear-gradient(180deg,<?= $colors[$index] ?>,<?= $colors[$index] ?>99)">
            <?= num($row['points']) ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="table-wrap mt-4">
    <table class="data">
      <thead><tr><th>الترتيب</th><th>الطالب</th><th>المجموعة</th><th>الأوسمة</th><th>النقاط</th></tr></thead>
      <tbody>
        <?php foreach (array_slice($rows, 3) as $row): ?>
          <tr>
            <td><span class="avatar" style="background:#f3f4f6;color:#4b5563"><?= (int) $row['rank'] ?></span></td>
            <td>
              <a class="cell-user" href="<?= url('/students/' . (int) $row['id']) ?>">
                <span class="avatar"><?= e(initials($row['name'])) ?></span>
                <span><?= e($row['name']) ?></span>
              </a>
            </td>
            <td class="muted"><?= e($row['group_name'] ?: '—') ?></td>
            <td class="muted">🎖️ <?= (int) $row['badge_count'] ?></td>
            <td><b style="color:var(--brand-dark)"><?= num($row['points']) ?></b></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
