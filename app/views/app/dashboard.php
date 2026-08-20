<div class="grid grid-4">
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(13,148,136,.12)">👥</div>
    <div>
      <div class="stat-label">الطلاب</div>
      <div class="stat-value" style="color:var(--brand-dark)"><?= num($stats['students']) ?></div>
      <div class="stat-hint"><?= num($stats['groups']) ?> مجموعات</div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(245,158,11,.16)">🪙</div>
    <div>
      <div class="stat-label">مجموع النقاط</div>
      <div class="stat-value" style="color:var(--gold-dark)"><?= num($stats['total_points']) ?></div>
      <div class="stat-hint"><?= num($stats['points_week']) ?> هذا الأسبوع</div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(139,92,246,.14)">⚡</div>
    <div>
      <div class="stat-label">نقاط اليوم</div>
      <div class="stat-value" style="color:var(--violet)"><?= num($stats['points_today']) ?></div>
      <div class="stat-hint">المرصود منذ بداية اليوم</div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(34,197,94,.14)">📅</div>
    <div>
      <div class="stat-label">نسبة الحضور اليوم</div>
      <div class="stat-value" style="color:#15803d"><?= (int) $stats['attendance']['rate'] ?>%</div>
      <div class="stat-hint">حاضر <?= (int) $stats['attendance']['present'] ?> · غائب <?= (int) $stats['attendance']['absent'] ?></div>
    </div>
  </div>
</div>

<div class="grid grid-3 mt-4">
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(236,72,153,.14)">🎁</div>
    <div>
      <div class="stat-label">طلبات استبدال معلّقة</div>
      <div class="stat-value" style="color:var(--rose)"><?= num($stats['pending_redemptions']) ?></div>
      <div class="stat-hint"><a href="<?= url('/rewards') ?>">مراجعة الطلبات ←</a></div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(245,158,11,.16)">🎖️</div>
    <div>
      <div class="stat-label">أوسمة ممنوحة</div>
      <div class="stat-value" style="color:var(--gold-dark)"><?= num($stats['badges_awarded']) ?></div>
      <div class="stat-hint"><a href="<?= url('/badges') ?>">إدارة الأوسمة ←</a></div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(14,165,233,.14)">❓</div>
    <div>
      <div class="stat-label">مسابقات منشورة</div>
      <div class="stat-value" style="color:var(--sky)"><?= num($stats['active_quizzes']) ?></div>
      <div class="stat-hint"><a href="<?= url('/quizzes') ?>">المسابقات ←</a></div>
    </div>
  </div>
</div>

<div class="grid mt-4" style="grid-template-columns:2fr 1fr">
  <div class="panel">
    <div class="panel-head">
      <h2>النقاط المرصودة خلال أسبوعين</h2>
      <span class="tag tag-brand">آخر 14 يوماً</span>
    </div>
    <?= chart_area($stats['trend']) ?>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>توزيع النقاط حسب النوع</h2></div>
    <?= chart_donut($stats['categories']) ?>
  </div>
</div>

<div class="grid mt-4" style="grid-template-columns:1fr 2fr">
  <div class="panel">
    <div class="panel-head">
      <h2>المتصدرون</h2>
      <a class="small" href="<?= url('/leaderboard') ?>">الكل ←</a>
    </div>
    <?php foreach ($stats['top_students'] as $i => $row): ?>
      <a class="rank-row<?= $i === 0 ? ' top' : '' ?>" href="<?= url('/students/' . $row['id']) ?>">
        <span class="rank-num <?= ['g1', 'g2', 'g3'][$i] ?? '' ?>"><?= (int) $row['rank'] ?></span>
        <span class="rank-name"><?= e($row['name']) ?></span>
        <span class="rank-points"><?= num($row['points']) ?></span>
      </a>
    <?php endforeach; ?>
    <?php if (!$stats['top_students']): ?>
      <p class="muted">لا يوجد طلاب بعد. <a href="<?= url('/students') ?>">أضف طلابك</a></p>
    <?php endif; ?>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>أداء المجموعات</h2></div>
    <?= chart_bars($stats['groups_chart']) ?>
  </div>
</div>

<div class="panel mt-4">
  <div class="panel-head">
    <h2>أحدث الحركات</h2>
    <a class="btn btn-primary btn-sm" href="<?= url('/award') ?>">⚡ رصد نقاط</a>
  </div>

  <?php if (!$stats['recent']): ?>
    <p class="muted">لم تُرصد أي نقاط بعد.</p>
  <?php endif; ?>

  <?php foreach ($stats['recent'] as $tx): ?>
    <div class="activity">
      <span class="points-pill <?= (int) $tx['points'] >= 0 ? 'points-plus' : 'points-minus' ?>">
        <?= (int) $tx['points'] > 0 ? '+' : '' ?><?= (int) $tx['points'] ?>
      </span>
      <div class="body">
        <b><?= e($tx['student_name']) ?></b>
        <small><?= e($tx['reason'] ?: 'رصد نقاط') ?> · <?= e($tx['awarded_by_name'] ?: 'مشرف') ?></small>
      </div>
      <span class="muted small nowrap"><?= ar_date($tx['created_at']) ?></span>
    </div>
  <?php endforeach; ?>
</div>
