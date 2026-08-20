<div class="panel" style="margin-bottom:16px">
  <div class="panel-head">
    <div>
      <h2>المسابقات والاستبانات</h2>
      <p class="muted small">أنشئ مسابقة بأسئلة اختيار من متعدد، وسيصحح النظام إجابات الطلاب ويمنحهم النقاط تلقائياً.</p>
    </div>
    <a class="btn btn-primary btn-sm" href="<?= url('/quizzes/new') ?>">➕ مسابقة جديدة</a>
  </div>
</div>

<?php if (!$quizzes): ?>
  <div class="panel center"><p class="muted">لا توجد مسابقات بعد.</p></div>
<?php else: ?>
  <div class="grid grid-2">
    <?php foreach ($quizzes as $quiz): ?>
      <div class="card card-pad">
        <div style="display:flex;gap:14px;align-items:flex-start">
          <div class="feature-icon" style="background:<?= $quiz['type'] === 'survey' ? 'rgba(139,92,246,.14);color:var(--violet)' : 'rgba(13,148,136,.12);color:var(--brand-dark)' ?>">
            <?= $quiz['type'] === 'survey' ? '📊' : '❓' ?>
          </div>
          <div style="flex:1;min-width:0">
            <h3><?= e($quiz['title']) ?></h3>
            <p class="muted small"><?= e($quiz['description']) ?></p>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
              <span class="tag <?= $quiz['type'] === 'survey' ? 'tag-violet' : 'tag-brand' ?>">
                <?= $quiz['type'] === 'survey' ? 'استبانة' : 'مسابقة' ?>
              </span>
              <?php if ($quiz['category']): ?><span class="tag tag-gold"><?= e($quiz['category']) ?></span><?php endif; ?>
              <span class="tag tag-gray"><?= (int) $quiz['question_count'] ?> أسئلة</span>
              <span class="tag tag-gray">👥 <?= (int) $quiz['attempt_count'] ?></span>
              <span class="tag <?= $quiz['is_published'] ? 'tag-green' : 'tag-gray' ?>">
                <?= $quiz['is_published'] ? 'منشورة' : 'مسودة' ?>
              </span>
            </div>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:14px">
          <a class="btn btn-outline btn-sm" href="<?= url('/quizzes/' . (int) $quiz['id']) ?>">التفاصيل والنتائج</a>
          <form method="post" action="<?= url('/quizzes/' . (int) $quiz['id'] . '/publish') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="is_published" value="<?= $quiz['is_published'] ? '0' : '1' ?>">
            <button class="btn <?= $quiz['is_published'] ? 'btn-outline' : 'btn-primary' ?> btn-sm" type="submit">
              <?= $quiz['is_published'] ? 'إيقاف النشر' : 'نشر للطلاب' ?>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
