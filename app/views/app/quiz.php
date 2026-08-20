<div class="grid" style="grid-template-columns:1.2fr .8fr">
  <div class="panel">
    <div class="panel-head">
      <div>
        <h2><?= e($quiz['title']) ?></h2>
        <p class="muted small"><?= e($quiz['description']) ?></p>
      </div>
      <span class="tag <?= $quiz['is_published'] ? 'tag-green' : 'tag-gray' ?>">
        <?= $quiz['is_published'] ? 'منشورة' : 'مسودة' ?>
      </span>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
      <span class="tag tag-brand"><?= $quiz['type'] === 'survey' ? 'استبانة' : 'مسابقة' ?></span>
      <?php if ($quiz['category']): ?><span class="tag tag-gold"><?= e($quiz['category']) ?></span><?php endif; ?>
      <span class="tag tag-gray"><?= (int) $quiz['points_per_correct'] ?> نقطة لكل إجابة صحيحة</span>
      <?php if ($quiz['time_limit_minutes']): ?>
        <span class="tag tag-sky">⏱️ <?= (int) $quiz['time_limit_minutes'] ?> دقيقة</span>
      <?php endif; ?>
    </div>

    <?php foreach ($questions as $i => $question): ?>
      <div class="quiz-question">
        <h4><?= $i + 1 ?>. <?= e($question['question']) ?></h4>
        <div class="quiz-options">
          <?php foreach ($question['options'] as $option): ?>
            <div class="quiz-opt" style="<?= $option === $question['correct_answer'] ? 'border-color:var(--green);background:#f0fdf4' : '' ?>">
              <?= e($option) ?>
              <?php if ($option === $question['correct_answer']): ?>
                <span class="tag tag-green" style="margin-inline-start:auto">الإجابة الصحيحة</span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div style="display:flex;gap:10px;margin-top:16px">
      <form method="post" action="<?= url('/quizzes/' . (int) $quiz['id'] . '/publish') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="is_published" value="<?= $quiz['is_published'] ? '0' : '1' ?>">
        <button class="btn <?= $quiz['is_published'] ? 'btn-outline' : 'btn-primary' ?>" type="submit">
          <?= $quiz['is_published'] ? 'إيقاف النشر' : 'نشر للطلاب' ?>
        </button>
      </form>
      <form method="post" action="<?= url('/quizzes/' . (int) $quiz['id'] . '/delete') ?>" data-confirm="حذف المسابقة ونتائجها؟">
        <?= csrf_field() ?>
        <button class="btn btn-danger" type="submit">🗑️ حذف</button>
      </form>
      <a class="btn btn-outline" href="<?= url('/quizzes') ?>">رجوع</a>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>النتائج</h2>
      <span class="tag tag-brand"><?= count($attempts) ?> مشاركة</span>
    </div>

    <?php if (!$attempts): ?>
      <p class="muted small">لم يشارك أحد بعد. انشر المسابقة وشارك رابط الطالب معه.</p>
    <?php endif; ?>

    <?php foreach ($attempts as $i => $attempt): ?>
      <div class="activity">
        <span class="avatar"><?= $i + 1 ?></span>
        <div class="body">
          <b><?= e($attempt['student_name']) ?></b>
          <small><?= (int) $attempt['correct_count'] ?> من <?= (int) $attempt['total_questions'] ?> · <?= ar_date($attempt['completed_at']) ?></small>
        </div>
        <span class="points-pill points-plus">+<?= (int) $attempt['points_awarded'] ?></span>
      </div>
    <?php endforeach; ?>
  </div>
</div>
