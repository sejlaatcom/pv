<?php $periods = ['week' => 'أسبوع', 'month' => 'شهر', 'term' => 'فصل دراسي', 'all' => 'الكل']; ?>

<div class="panel" style="margin-bottom:16px">
  <div class="form-inline">
    <div class="chips-row">
      <?php foreach ($periods as $key => $labelText): ?>
        <a class="chip-btn<?= $period === $key ? ' active' : '' ?>" href="<?= url('/reports?period=' . $key) ?>"><?= e($labelText) ?></a>
      <?php endforeach; ?>
    </div>
    <div style="margin-inline-start:auto;display:flex;gap:8px">
      <a class="btn btn-outline btn-sm" href="<?= url('/export/report?period=' . e($period)) ?>">⬇️ تصدير Excel</a>
      <button type="button" class="btn btn-outline btn-sm" onclick="window.print()">🖨️ طباعة التقرير</button>
    </div>
  </div>
</div>

<div class="grid grid-2" style="margin-bottom:16px">
  <div class="card stat">
    <div class="stat-icon" style="background:#dcfce7">📈</div>
    <div>
      <div class="stat-label">النقاط الإيجابية خلال الفترة</div>
      <div class="stat-value" style="color:#15803d"><?= num($report['behavior']['positive']) ?></div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:#fee2e2">📉</div>
    <div>
      <div class="stat-label">النقاط المخصومة خلال الفترة</div>
      <div class="stat-value" style="color:#b91c1c"><?= num($report['behavior']['negative']) ?></div>
    </div>
  </div>
</div>

<div class="grid" style="grid-template-columns:2fr 1fr">
  <div class="panel">
    <div class="panel-head"><h2>النقاط حسب المجموعة</h2></div>
    <?= chart_bars($report['by_group']) ?>
  </div>
  <div class="panel">
    <div class="panel-head"><h2>توزيع النقاط حسب النوع</h2></div>
    <?= chart_donut($report['categories']) ?>
  </div>
</div>

<div class="grid grid-2 mt-4">
  <div class="panel">
    <div class="panel-head"><h2>نسبة الحضور — آخر 14 يوماً</h2></div>
    <?= chart_area($report['attendance_trend'], '#22c55e') ?>
  </div>
  <div class="panel">
    <div class="panel-head"><h2>النقاط الشهرية</h2></div>
    <?= chart_bars($report['monthly'], ['#0d9488']) ?>
  </div>
</div>

<div class="grid grid-2 mt-4">
  <div class="panel">
    <div class="panel-head"><h2>الأعلى نقاطاً خلال الفترة</h2></div>
    <div class="table-wrap" style="border:none">
      <table class="data">
        <thead><tr><th>#</th><th>الطالب</th><th>المجموعة</th><th>النقاط</th></tr></thead>
        <tbody>
          <?php foreach ($report['top_students'] as $i => $row): ?>
            <tr>
              <td class="muted"><?= $i + 1 ?></td>
              <td><b><?= e($row['name']) ?></b></td>
              <td class="muted"><?= e($row['group_name'] ?: '—') ?></td>
              <td><b style="color:var(--brand-dark)"><?= num($row['value']) ?></b></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$report['top_students']): ?>
            <tr><td colspan="4" class="center muted">لا توجد بيانات خلال هذه الفترة.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>متوسط النقاط لكل مجموعة</h2></div>
    <div class="table-wrap" style="border:none">
      <table class="data">
        <thead><tr><th>المجموعة</th><th>الطلاب</th><th>الإجمالي</th><th>المتوسط</th></tr></thead>
        <tbody>
          <?php foreach ($report['by_group'] as $row): ?>
            <tr>
              <td><b><?= e($row['label']) ?></b></td>
              <td class="muted"><?= (int) $row['students'] ?></td>
              <td><?= num($row['value']) ?></td>
              <td><b style="color:var(--brand-dark)"><?= num($row['average']) ?></b></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
