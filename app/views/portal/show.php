<div class="portal-hero">
  <div class="who"><?= e($student['name']) ?></div>
  <div class="muted" style="color:rgba(255,255,255,.7);font-size:13px">
    <?= e($student['group_name'] ?: 'بدون مجموعة') ?>
  </div>
  <div class="big mt-4"><?= num($student['total_points']) ?></div>
  <div style="font-weight:800">نقطة</div>

  <div class="portal-stats">
    <div><b>#<?= (int) $profile['rank'] ?></b><small>الترتيب من <?= (int) $profile['total_students'] ?></small></div>
    <div><b><?= num($profile['points_week']) ?></b><small>نقاط الأسبوع</small></div>
    <div><b><?= count($profile['badges']) ?></b><small>الأوسمة</small></div>
    <div><b><?= (int) $profile['attendance']['rate'] ?>%</b><small>نسبة الحضور</small></div>
  </div>
</div>

<div class="grid" style="grid-template-columns:1.4fr .6fr">
  <div class="panel">
    <div class="panel-head"><h2>حركة نقاطي — آخر 14 يوماً</h2></div>
    <?= chart_area($profile['chart'], '#0d9488', 200) ?>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>أوسمتي</h2></div>
    <?php if (!$profile['badges']): ?>
      <p class="muted small">لم تحصل على أوسمة بعد — واصل التميز!</p>
    <?php else: ?>
      <div class="grid grid-2" style="gap:10px">
        <?php foreach ($profile['badges'] as $badge): ?>
          <div class="center" style="background:<?= e($badge['color']) ?>18;border-radius:14px;padding:12px 6px">
            <div style="font-size:26px"><?= e($badge['icon']) ?></div>
            <b class="small"><?= e($badge['title']) ?></b>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if ($quizzes): ?>
  <div class="panel mt-4">
    <div class="panel-head"><h2>المسابقات المتاحة</h2></div>
    <div class="grid grid-3">
      <?php foreach ($quizzes as $quiz): ?>
        <div class="card card-pad">
          <h3 style="font-size:16px"><?= e($quiz['title']) ?></h3>
          <p class="muted small"><?= e($quiz['description']) ?></p>
          <div style="display:flex;gap:6px;flex-wrap:wrap;margin:10px 0">
            <span class="tag tag-gray"><?= (int) $quiz['question_count'] ?> أسئلة</span>
            <span class="tag tag-gold"><?= (int) $quiz['points_per_correct'] ?> نقاط للإجابة</span>
          </div>
          <?php if ((int) $quiz['my_attempts'] > 0): ?>
            <span class="tag tag-green">شاركت فيها ✓</span>
          <?php else: ?>
            <a class="btn btn-primary btn-sm btn-block" href="<?= url('/p/' . $student['access_token'] . '/quiz/' . (int) $quiz['id']) ?>">
              ابدأ المشاركة
            </a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<div class="panel mt-4">
  <div class="panel-head">
    <h2>متجر الجوائز</h2>
    <span class="tag tag-gold">رصيدك <?= num($student['total_points']) ?> نقطة</span>
  </div>

  <div class="grid grid-4">
    <?php foreach ($rewards as $reward): $affordable = (int) $student['total_points'] >= (int) $reward['cost'] && (int) $reward['stock'] > 0; ?>
      <div class="card reward-card">
        <div class="reward-icon"><?= e($reward['icon']) ?></div>
        <h3 style="font-size:15px"><?= e($reward['title']) ?></h3>
        <p class="muted small" style="min-height:32px"><?= e($reward['description']) ?></p>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <span class="tag tag-gold"><?= num($reward['cost']) ?> نقطة</span>
          <span class="muted small">متبقٍ <?= (int) $reward['stock'] ?></span>
        </div>
        <form method="post" action="<?= url('/p/' . $student['access_token'] . '/redeem') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="reward_id" value="<?= (int) $reward['id'] ?>">
          <button class="btn <?= $affordable ? 'btn-primary' : 'btn-outline' ?> btn-sm btn-block" type="submit" <?= $affordable ? '' : 'disabled' ?>>
            <?= $affordable ? 'استبدال' : 'النقاط لا تكفي' ?>
          </button>
        </form>
      </div>
    <?php endforeach; ?>
    <?php if (!$rewards): ?>
      <p class="muted" style="grid-column:1/-1">لا توجد جوائز متاحة حالياً.</p>
    <?php endif; ?>
  </div>
</div>

<div class="grid grid-2 mt-4">
  <div class="panel">
    <div class="panel-head"><h2>سجل النقاط</h2></div>
    <div style="max-height:360px;overflow-y:auto">
      <?php foreach (array_slice($profile['transactions'], 0, 25) as $tx): ?>
        <div class="activity">
          <span class="points-pill <?= (int) $tx['points'] >= 0 ? 'points-plus' : 'points-minus' ?>">
            <?= (int) $tx['points'] > 0 ? '+' : '' ?><?= (int) $tx['points'] ?>
          </span>
          <div class="body">
            <b><?= e($tx['reason'] ?: $tx['item_title'] ?: 'رصد نقاط') ?></b>
            <small><?= e($tx['awarded_by_name'] ?: 'مشرف') ?></small>
          </div>
          <span class="muted small nowrap"><?= ar_date($tx['created_at']) ?></span>
        </div>
      <?php endforeach; ?>
      <?php if (!$profile['transactions']): ?>
        <p class="muted small">لا توجد حركات بعد.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>لوحة المتصدرين</h2></div>
    <?php foreach ($leaderboard as $i => $row): ?>
      <div class="rank-row<?= (int) $row['id'] === (int) $student['id'] ? ' top' : '' ?>">
        <span class="rank-num <?= ['g1', 'g2', 'g3'][$i] ?? '' ?>"><?= (int) $row['rank'] ?></span>
        <span class="rank-name"><?= e($row['name']) ?><?= (int) $row['id'] === (int) $student['id'] ? ' (أنت)' : '' ?></span>
        <span class="rank-points"><?= num($row['points']) ?></span>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="panel mt-4">
  <div class="panel-head"><h2>ملخص الحضور</h2></div>
  <div class="grid grid-4 center">
    <div><b style="font-size:22px;color:#15803d"><?= (int) $profile['attendance']['present'] ?></b><div class="muted small">حاضر</div></div>
    <div><b style="font-size:22px;color:var(--gold-dark)"><?= (int) $profile['attendance']['late'] ?></b><div class="muted small">متأخر</div></div>
    <div><b style="font-size:22px;color:#b91c1c"><?= (int) $profile['attendance']['absent'] ?></b><div class="muted small">غائب</div></div>
    <div><b style="font-size:22px;color:#075985"><?= (int) $profile['attendance']['excused'] ?></b><div class="muted small">بعذر</div></div>
  </div>
</div>
