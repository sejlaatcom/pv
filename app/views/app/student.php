<?php $link = absolute_url('/p/' . $student['access_token']); ?>

<div class="grid grid-4">
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(245,158,11,.16)">🪙</div>
    <div>
      <div class="stat-label">الرصيد الكلي</div>
      <div class="stat-value" style="color:var(--gold-dark)"><?= num($student['total_points']) ?></div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(13,148,136,.12)">⚡</div>
    <div>
      <div class="stat-label">نقاط هذا الأسبوع</div>
      <div class="stat-value" style="color:var(--brand-dark)"><?= num($profile['points_week']) ?></div>
      <div class="stat-hint">هذا الشهر: <?= num($profile['points_month']) ?></div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(139,92,246,.14)">🏆</div>
    <div>
      <div class="stat-label">الترتيب</div>
      <div class="stat-value" style="color:var(--violet)"><?= (int) $profile['rank'] ?> / <?= (int) $profile['total_students'] ?></div>
    </div>
  </div>
  <div class="card stat">
    <div class="stat-icon" style="background:rgba(34,197,94,.14)">📅</div>
    <div>
      <div class="stat-label">نسبة الحضور</div>
      <div class="stat-value" style="color:#15803d"><?= (int) $profile['attendance']['rate'] ?>%</div>
      <div class="stat-hint">غياب <?= (int) $profile['attendance']['absent'] ?> · تأخر <?= (int) $profile['attendance']['late'] ?></div>
    </div>
  </div>
</div>

<div class="grid mt-4" style="grid-template-columns:2fr 1fr">
  <div class="panel">
    <div class="panel-head">
      <h2>حركة النقاط — آخر 14 يوماً</h2>
      <span class="tag tag-brand"><?= e($student['group_name'] ?: 'بدون مجموعة') ?></span>
    </div>
    <?= chart_area($profile['chart'], '#8b5cf6') ?>
  </div>

  <div class="grid" style="align-content:start">
    <div class="panel">
      <div class="panel-head"><h2>رصد سريع</h2></div>
      <form method="post" action="<?= url('/award') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="student_ids[]" value="<?= (int) $student['id'] ?>">
        <div class="field">
          <label>البند</label>
          <select class="select" name="item_id">
            <option value="">— اختر بنداً —</option>
            <?php foreach ($items as $item): ?>
              <option value="<?= (int) $item['id'] ?>"><?= e($item['title']) ?> (<?= (int) $item['points'] > 0 ? '+' : '' ?><?= (int) $item['points'] ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>أو قيمة مخصصة</label>
          <input class="input" type="number" name="points" placeholder="15 أو 5-">
        </div>
        <div class="field">
          <label>السبب</label>
          <input class="input" name="reason" placeholder="اختياري">
        </div>
        <button class="btn btn-primary btn-block" type="submit">⚡ رصد</button>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head"><h2>رابط ولي الأمر</h2></div>
      <div class="small" style="background:#f3f4f6;border-radius:12px;padding:10px;word-break:break-all" dir="ltr"><?= e($link) ?></div>
      <div style="display:flex;gap:8px;margin-top:10px">
        <button type="button" class="btn btn-outline btn-sm" data-copy="<?= e($link) ?>">نسخ الرابط</button>
        <?php if ($student['guardian_phone']): ?>
          <a class="btn btn-primary btn-sm" target="_blank" rel="noopener"
             href="<?= e(whatsapp_link($student['guardian_phone'], 'متابعة ' . $student['name'] . ' — الرصيد الحالي ' . $student['total_points'] . ' نقطة: ' . $link)) ?>">💬 واتساب</a>
        <?php endif; ?>
      </div>
      <p class="muted small mt-4">رمز الدخول للطالب: <b dir="ltr"><?= e($student['access_token']) ?></b></p>
    </div>

    <div class="panel">
      <div class="panel-head"><h2>تنبيه لولي الأمر</h2></div>
      <form method="post" action="<?= url('/students/' . (int) $student['id'] . '/notify') ?>">
        <?= csrf_field() ?>
        <div class="field">
          <label class="small">العنوان</label>
          <input class="input" name="title" placeholder="ملاحظة عن الواجب" required>
        </div>
        <div class="field">
          <label class="small">النص</label>
          <textarea class="textarea" name="body" rows="3" style="min-height:80px"></textarea>
        </div>
        <button class="btn btn-outline btn-block" type="submit">🔔 تسجيل التنبيه</button>
      </form>
      <p class="muted small mt-4">
        يُحفظ في <a href="<?= url('/notifications') ?>">سجل التنبيهات</a> مع زر إرسال عبر الواتساب.
      </p>
    </div>

    <div class="panel">
      <div class="panel-head"><h2>تصدير</h2></div>
      <a class="btn btn-outline btn-block" href="<?= url('/students/' . (int) $student['id'] . '/export') ?>">
        ⬇️ تصدير سجل النقاط (Excel)
      </a>
    </div>
  </div>
</div>

<div class="grid grid-2 mt-4">
  <div class="panel">
    <div class="panel-head"><h2>سجل النقاط</h2></div>
    <?php if (!$profile['transactions']): ?>
      <p class="muted">لا توجد حركات بعد.</p>
    <?php endif; ?>
    <div style="max-height:420px;overflow-y:auto">
      <?php foreach ($profile['transactions'] as $tx): ?>
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
    </div>
  </div>

  <div class="grid" style="align-content:start">
    <div class="panel">
      <div class="panel-head"><h2>الأوسمة</h2></div>
      <?php if (!$profile['badges']): ?>
        <p class="muted small">لم يحصل على أوسمة بعد.</p>
      <?php else: ?>
        <div class="grid grid-3">
          <?php foreach ($profile['badges'] as $badge): ?>
            <div class="center" style="background:<?= e($badge['color']) ?>18;border-radius:14px;padding:12px 6px">
              <div style="font-size:26px"><?= e($badge['icon']) ?></div>
              <b class="small"><?= e($badge['title']) ?></b>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= url('/students/' . (int) $student['id'] . '/badge') ?>" class="form-inline mt-4">
        <?= csrf_field() ?>
        <select class="select" name="badge_id" style="max-width:200px">
          <?php foreach ($badges as $badge): ?>
            <option value="<?= (int) $badge['id'] ?>"><?= e($badge['icon']) ?> <?= e($badge['title']) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-outline btn-sm" type="submit">منح وسام</button>
      </form>
    </div>

    <div class="panel">
      <div class="panel-head"><h2>طلبات الجوائز</h2></div>
      <?php if (!$profile['redemptions']): ?>
        <p class="muted small">لا توجد طلبات استبدال.</p>
      <?php endif; ?>
      <?php foreach ($profile['redemptions'] as $redemption): ?>
        <div class="activity">
          <span>🎁</span>
          <div class="body">
            <b><?= e($redemption['reward_title']) ?></b>
            <small><?= e(label('redemption', $redemption['status'])) ?></small>
          </div>
          <span class="points-pill points-minus">-<?= (int) $redemption['cost'] ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="panel mt-4">
  <div class="panel-head"><h2>بيانات الطالب</h2></div>
  <form method="post" action="<?= url('/students/' . (int) $student['id'] . '/update') ?>" class="form-grid">
    <?= csrf_field() ?>
    <div class="field">
      <label>الاسم</label>
      <input class="input" name="name" value="<?= e($student['name']) ?>">
    </div>
    <div class="field">
      <label>المجموعة</label>
      <select class="select" name="group_id">
        <option value="">بدون مجموعة</option>
        <?php foreach ($groups as $group): ?>
          <option value="<?= (int) $group['id'] ?>" <?= (int) $student['group_id'] === (int) $group['id'] ? 'selected' : '' ?>>
            <?= e($group['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label>رقم الطالب</label>
      <input class="input" name="code" value="<?= e($student['code']) ?>">
    </div>
    <div class="field">
      <label>اسم ولي الأمر</label>
      <input class="input" name="guardian_name" value="<?= e($student['guardian_name']) ?>">
    </div>
    <div class="field">
      <label>جوال ولي الأمر</label>
      <input class="input" type="tel" name="guardian_phone" value="<?= e($student['guardian_phone']) ?>">
    </div>
    <div class="field" style="display:flex;align-items:center;gap:8px;padding-top:26px">
      <input type="checkbox" id="is_active" name="is_active" value="1" <?= $student['is_active'] ? 'checked' : '' ?>>
      <label for="is_active" style="margin:0">الطالب نشط</label>
    </div>
    <div class="full" style="display:flex;gap:10px">
      <button class="btn btn-primary" type="submit">حفظ التعديلات</button>
    </div>
  </form>

  <?php if (is_admin()): ?>
    <form method="post" action="<?= url('/students/' . (int) $student['id'] . '/delete') ?>" class="mt-4"
          data-confirm="سيتم حذف الطالب وسجل نقاطه نهائياً. هل أنت متأكد؟">
      <?= csrf_field() ?>
      <button class="btn btn-danger btn-sm" type="submit">🗑️ حذف الطالب</button>
    </form>
  <?php endif; ?>
</div>
