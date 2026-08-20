<?php $statusColors = ['pending' => 'tag-gold', 'approved' => 'tag-sky', 'delivered' => 'tag-green', 'rejected' => 'tag-red']; ?>

<div class="panel" style="margin-bottom:16px">
  <form method="post" action="<?= url('/rewards') ?>" class="form-inline">
    <?= csrf_field() ?>
    <div class="field" style="margin:0">
      <label>اسم الجائزة *</label>
      <input class="input" name="title" placeholder="بطاقة ألعاب" required>
    </div>
    <div class="field" style="margin:0">
      <label>الوصف</label>
      <input class="input" name="description">
    </div>
    <div class="field" style="margin:0;max-width:120px">
      <label>التكلفة *</label>
      <input class="input" type="number" name="cost" required>
    </div>
    <div class="field" style="margin:0;max-width:110px">
      <label>الكمية</label>
      <input class="input" type="number" name="stock" value="10">
    </div>
    <div class="field" style="margin:0;max-width:100px">
      <label>الأيقونة</label>
      <input class="input" name="icon" value="🎁" style="text-align:center">
    </div>
    <button class="btn btn-primary" type="submit">➕ إضافة</button>
  </form>
</div>

<div class="grid grid-4">
  <?php foreach ($rewards as $reward): ?>
    <div class="card reward-card card-hover">
      <?php if (is_admin()): ?>
        <form method="post" action="<?= url('/rewards/' . (int) $reward['id'] . '/delete') ?>" class="corner-action"
              data-confirm="حذف الجائزة؟">
          <?= csrf_field() ?>
          <button class="btn btn-danger btn-sm" type="submit">🗑️</button>
        </form>
      <?php endif; ?>
      <div class="reward-icon"><?= e($reward['icon']) ?></div>
      <h3><?= e($reward['title']) ?></h3>
      <p class="muted small" style="min-height:34px"><?= e($reward['description']) ?></p>
      <div style="display:flex;justify-content:space-between;align-items:center">
        <span class="tag tag-gold"><?= num($reward['cost']) ?> نقطة</span>
        <span class="muted small">متبقٍ <?= (int) $reward['stock'] ?></span>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$rewards): ?>
    <div class="panel center" style="grid-column:1/-1"><p class="muted">لا توجد جوائز بعد.</p></div>
  <?php endif; ?>
</div>

<div class="panel mt-6">
  <div class="panel-head">
    <h2>طلبات الاستبدال</h2>
    <span class="tag tag-gold"><?= count(array_filter($redemptions, fn($r) => $r['status'] === 'pending')) ?> بانتظار الاعتماد</span>
  </div>

  <?php if (!$redemptions): ?>
    <p class="muted">لا توجد طلبات — تظهر هنا طلبات الطلاب من بوابتهم.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>الطالب</th><th>الجائزة</th><th>النقاط</th><th>التاريخ</th><th>الحالة</th><th>إجراء</th></tr></thead>
        <tbody>
          <?php foreach ($redemptions as $redemption): ?>
            <tr>
              <td><b><?= e($redemption['student_name']) ?></b></td>
              <td><?= e($redemption['reward_title']) ?></td>
              <td><span class="points-pill points-minus">-<?= (int) $redemption['cost'] ?></span></td>
              <td class="muted small"><?= ar_date($redemption['created_at']) ?></td>
              <td><span class="tag <?= $statusColors[$redemption['status']] ?>"><?= e(label('redemption', $redemption['status'])) ?></span></td>
              <td>
                <div style="display:flex;gap:6px">
                  <?php if ($redemption['status'] === 'pending'): ?>
                    <form method="post" action="<?= url('/redemptions/' . (int) $redemption['id']) ?>">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="approved">
                      <button class="btn btn-primary btn-sm" type="submit">اعتماد</button>
                    </form>
                    <form method="post" action="<?= url('/redemptions/' . (int) $redemption['id']) ?>">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="rejected">
                      <button class="btn btn-danger btn-sm" type="submit">رفض</button>
                    </form>
                  <?php elseif ($redemption['status'] === 'approved'): ?>
                    <form method="post" action="<?= url('/redemptions/' . (int) $redemption['id']) ?>">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="delivered">
                      <button class="btn btn-outline btn-sm" type="submit">تم التسليم</button>
                    </form>
                  <?php else: ?>
                    <span class="muted small">—</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
