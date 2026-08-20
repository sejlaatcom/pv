<div class="panel">
  <div class="panel-head">
    <div>
      <h2><?= e($quiz['title']) ?></h2>
      <p class="muted small"><?= e($quiz['description']) ?></p>
    </div>
    <span class="tag tag-gold"><?= (int) $quiz['points_per_correct'] ?> نقاط لكل إجابة صحيحة</span>
  </div>

  <?php if ($attempt): ?>
    <div class="center" style="padding:26px 0">
      <div style="font-size:48px">🎉</div>
      <h3>سبق أن شاركت في هذه المسابقة</h3>
      <p class="muted">
        نتيجتك: <b><?= (int) $attempt['correct_count'] ?></b> من <b><?= (int) $attempt['total_questions'] ?></b>
        · حصلت على <b><?= (int) $attempt['points_awarded'] ?></b> نقطة
      </p>
      <a class="btn btn-primary mt-4" href="<?= url('/p/' . $student['access_token']) ?>">العودة لصفحتي</a>
    </div>
  <?php else: ?>
    <form method="post" action="<?= url('/p/' . $student['access_token'] . '/quiz/' . (int) $quiz['id']) ?>">
      <?= csrf_field() ?>
      <?php foreach ($questions as $i => $question): ?>
        <div class="quiz-question">
          <h4><?= $i + 1 ?>. <?= e($question['question']) ?></h4>
          <div class="quiz-options">
            <?php foreach ($question['options'] as $option): ?>
              <label class="quiz-opt">
                <input type="radio" name="answer[<?= (int) $question['id'] ?>]" value="<?= e($option) ?>" required>
                <span><?= e($option) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <div style="display:flex;gap:10px;margin-top:16px">
        <button class="btn btn-primary" type="submit">إرسال الإجابات</button>
        <a class="btn btn-outline" href="<?= url('/p/' . $student['access_token']) ?>">رجوع</a>
      </div>
    </form>
  <?php endif; ?>
</div>
