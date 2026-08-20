<form method="post" action="<?= url('/quizzes') ?>">
  <?= csrf_field() ?>

  <div class="panel" style="margin-bottom:16px">
    <div class="panel-head"><h2>بيانات المسابقة</h2></div>
    <div class="form-grid">
      <div class="field full">
        <label>العنوان *</label>
        <input class="input" name="title" placeholder="مسابقة السيرة النبوية" required>
      </div>
      <div class="field full">
        <label>الوصف</label>
        <input class="input" name="description" placeholder="أسئلة مختارة في السيرة">
      </div>
      <div class="field">
        <label>التصنيف</label>
        <input class="input" name="category" placeholder="شرعي، علمي، ثقافي">
      </div>
      <div class="field">
        <label>النوع</label>
        <select class="select" name="type">
          <option value="quiz">مسابقة (لها إجابات صحيحة)</option>
          <option value="survey">استبانة رأي (بدون إجابات صحيحة)</option>
        </select>
      </div>
      <div class="field">
        <label>النقاط لكل إجابة صحيحة</label>
        <input class="input" type="number" name="points_per_correct" value="5">
      </div>
      <div class="field">
        <label>مدة المسابقة (دقائق)</label>
        <input class="input" type="number" name="time_limit_minutes" placeholder="اختياري">
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>الأسئلة</h2>
      <button type="button" class="btn btn-outline btn-sm" data-add-question>➕ إضافة سؤال</button>
    </div>

    <div id="questions-list">
      <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="quiz-question">
          <h4>السؤال <?= $i + 1 ?></h4>
          <div class="field">
            <input class="input" name="q[<?= $i ?>][question]" placeholder="نص السؤال">
          </div>
          <div class="grid grid-2" style="gap:8px">
            <?php for ($j = 0; $j < 4; $j++): ?>
              <label class="quiz-opt">
                <input type="radio" name="q[<?= $i ?>][correct]" value="<?= $j ?>" <?= $j === 0 ? 'checked' : '' ?> title="الإجابة الصحيحة">
                <input class="input" name="q[<?= $i ?>][options][]" placeholder="الخيار <?= $j + 1 ?>">
              </label>
            <?php endfor; ?>
          </div>
          <p class="muted small mt-4">حدّد الدائرة بجانب الإجابة الصحيحة (تُتجاهل في الاستبانات).</p>
        </div>
      <?php endfor; ?>
    </div>

    <div style="display:flex;gap:10px;margin-top:16px">
      <button class="btn btn-primary" type="submit">💾 حفظ المسابقة</button>
      <a class="btn btn-outline" href="<?= url('/quizzes') ?>">إلغاء</a>
    </div>
  </div>
</form>

<template id="question-template">
  <h4>السؤال __NUM__</h4>
  <div class="field">
    <input class="input" name="q[__INDEX__][question]" placeholder="نص السؤال">
  </div>
  <div class="grid grid-2" style="gap:8px">
    <label class="quiz-opt">
      <input type="radio" name="q[__INDEX__][correct]" value="0" checked>
      <input class="input" name="q[__INDEX__][options][]" placeholder="الخيار 1">
    </label>
    <label class="quiz-opt">
      <input type="radio" name="q[__INDEX__][correct]" value="1">
      <input class="input" name="q[__INDEX__][options][]" placeholder="الخيار 2">
    </label>
    <label class="quiz-opt">
      <input type="radio" name="q[__INDEX__][correct]" value="2">
      <input class="input" name="q[__INDEX__][options][]" placeholder="الخيار 3">
    </label>
    <label class="quiz-opt">
      <input type="radio" name="q[__INDEX__][correct]" value="3">
      <input class="input" name="q[__INDEX__][options][]" placeholder="الخيار 4">
    </label>
  </div>
</template>
