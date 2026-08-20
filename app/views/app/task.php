<div class="panel" style="margin-bottom:16px">
  <div class="panel-head">
    <div>
      <h2><?= e($task['title']) ?></h2>
      <p class="muted small"><?= e($task['description']) ?></p>
    </div>
    <div style="display:flex;gap:8px">
      <span class="tag tag-brand"><?= e($task['group_name'] ?: 'جميع الطلاب') ?></span>
      <span class="tag tag-gold">الدرجة الكاملة <?= (int) $task['max_score'] ?></span>
      <span class="tag tag-sky"><?= (int) $task['points'] ?> نقطة عند الإتقان</span>
    </div>
  </div>
  <p class="muted small">
    تُمنح نقاط المهمة تلقائياً لمن يحصل على نصف الدرجة فأكثر، بنسبة درجته من الدرجة الكاملة.
  </p>
</div>

<form method="post" action="<?= url('/tasks/' . (int) $task['id'] . '/grade') ?>">
  <?= csrf_field() ?>
  <div class="table-wrap">
    <table class="data">
      <thead><tr><th>الطالب</th><th>الحالة</th><th>الدرجة الحالية</th><th>رصد الدرجة</th></tr></thead>
      <tbody>
        <?php foreach ($submissions as $submission): ?>
          <tr>
            <td>
              <a class="cell-user" href="<?= url('/students/' . (int) $submission['student_id']) ?>">
                <span class="avatar"><?= e(initials($submission['student_name'])) ?></span>
                <span><?= e($submission['student_name']) ?></span>
              </a>
            </td>
            <td><span class="tag tag-gray"><?= e(label('submission', $submission['status'])) ?></span></td>
            <td class="muted"><?= $submission['score'] === null ? '—' : (int) $submission['score'] . ' / ' . (int) $task['max_score'] ?></td>
            <td>
              <input class="input" style="max-width:110px" type="number" min="0" max="<?= (int) $task['max_score'] ?>"
                     name="score[<?= (int) $submission['id'] ?>]" placeholder="—">
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$submissions): ?>
          <tr><td colspan="4" class="center muted">لا يوجد طلاب في هذه المهمة.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-4" style="display:flex;gap:10px">
    <button class="btn btn-primary" type="submit">💾 حفظ الدرجات ومنح النقاط</button>
    <a class="btn btn-outline" href="<?= url('/tasks') ?>">رجوع</a>
  </div>
</form>
