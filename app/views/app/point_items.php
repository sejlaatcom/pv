<div class="panel" style="margin-bottom:16px">
  <form method="post" action="<?= url('/point-items') ?>" class="form-inline">
    <?= csrf_field() ?>
    <div class="field" style="margin:0">
      <label>اسم البند *</label>
      <input class="input" name="title" placeholder="تسميع متقن" required>
    </div>
    <div class="field" style="margin:0;max-width:140px">
      <label>النقاط *</label>
      <input class="input" type="number" name="points" placeholder="10 أو 5-" required>
    </div>
    <div class="field" style="margin:0;max-width:200px">
      <label>التصنيف</label>
      <select class="select" name="category">
        <?php foreach (['behavior', 'attendance', 'memorization', 'homework', 'participation', 'penalty', 'other'] as $category): ?>
          <option value="<?= $category ?>"><?= e(label('point_category', $category)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary" type="submit">➕ إضافة بند</button>
  </form>
  <p class="muted small mt-4">القيم السالبة تُستخدم للمخالفات مثل التأخر أو عدم حل الواجب.</p>
</div>

<div class="table-wrap">
  <table class="data">
    <thead><tr><th>البند</th><th>التصنيف</th><th>النقاط</th><th>إجراء</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><b><?= e($item['title']) ?></b></td>
          <td><span class="tag tag-gray"><?= e(label('point_category', $item['category'])) ?></span></td>
          <td>
            <span class="points-pill <?= (int) $item['points'] >= 0 ? 'points-plus' : 'points-minus' ?>">
              <?= (int) $item['points'] > 0 ? '+' : '' ?><?= (int) $item['points'] ?>
            </span>
          </td>
          <td>
            <form method="post" action="<?= url('/point-items/' . (int) $item['id'] . '/delete') ?>" data-confirm="حذف البند؟">
              <?= csrf_field() ?>
              <button class="btn btn-danger btn-sm" type="submit">حذف</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$items): ?>
        <tr><td colspan="4" class="center muted">لا توجد بنود بعد.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
