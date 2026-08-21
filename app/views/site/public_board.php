<?php $periods = ['week' => 'هذا الأسبوع', 'month' => 'هذا الشهر', 'term' => 'الفصل الدراسي', 'all' => 'الإجمالي']; ?>

<section class="hero">
  <div class="container center" style="padding:48px 20px">
    <span class="hero-badge">🏆 لوحة الشرف</span>
    <h1 style="font-size:34px;color:#fff"><?= e($entity['name']) ?></h1>
    <div class="hero-stats" style="max-width:760px;margin:26px auto 0">
      <div><b><?= num($stats['students']) ?></b><span>طالب</span></div>
      <div><b><?= num($stats['points']) ?></b><span>نقطة</span></div>
      <div><b><?= num($stats['badges']) ?></b><span>وسام</span></div>
      <div><b><?= num($stats['groups']) ?></b><span>مجموعة</span></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="chips-row center" style="justify-content:center;margin-bottom:22px">
      <?php foreach ($periods as $key => $labelText): ?>
        <a class="chip-btn<?= $period === $key ? ' active' : '' ?>" href="<?= url('/e/' . $entity['slug'] . '?period=' . $key) ?>">
          <?= e($labelText) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if (!$rows): ?>
      <div class="card card-pad center"><p class="muted">لا توجد نتائج خلال هذه الفترة.</p></div>
    <?php else: ?>
      <?php $podium = array_slice($rows, 0, 3); $colors = ['#f59e0b', '#9ca3af', '#b45309']; $heights = [120, 92, 74]; ?>
      <div class="card card-pad">
        <div class="podium">
          <?php foreach ([1, 0, 2] as $index): if (empty($podium[$index])) continue; $row = $podium[$index]; ?>
            <div class="podium-col">
              <div class="podium-medal"><?= ['🥇', '🥈', '🥉'][$index] ?></div>
              <div class="podium-face" style="background:<?= $colors[$index] ?>"><?= e(initials($row['name'])) ?></div>
              <div class="podium-name"><?= e($row['name']) ?></div>
              <div class="podium-group"><?= e($row['group_name'] ?: '—') ?></div>
              <div class="podium-bar" style="height:<?= $heights[$index] ?>px;background:linear-gradient(180deg,<?= $colors[$index] ?>,<?= $colors[$index] ?>99)">
                <?= num($row['points']) ?>
              </div>
            </div>
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
                <td><b><?= e($row['name']) ?></b></td>
                <td class="muted"><?= e($row['group_name'] ?: '—') ?></td>
                <td class="muted">🎖️ <?= (int) $row['badge_count'] ?></td>
                <td><b style="color:var(--brand-dark)"><?= num($row['points']) ?></b></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <p class="center muted small mt-6">
      هذه الصفحة عامة ويمكن مشاركتها مع أولياء الأمور — بيانات التواصل والملاحظات لا تظهر فيها.
    </p>
  </div>
</section>
