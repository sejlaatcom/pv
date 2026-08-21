<div class="grid" style="grid-template-columns:1.2fr .8fr">
  <div class="panel">
    <div class="panel-head"><h2>بيانات الجهة</h2></div>

    <?php if (!is_admin()): ?>
      <p class="muted">تعديل بيانات الجهة متاح لمدير الجهة فقط.</p>
    <?php endif; ?>

    <form method="post" action="<?= url('/settings/entity') ?>" class="form-grid">
      <?= csrf_field() ?>
      <div class="field full">
        <label>اسم الجهة</label>
        <input class="input" name="name" value="<?= e($entity['name']) ?>" <?= is_admin() ? '' : 'disabled' ?>>
      </div>
      <div class="field">
        <label>نوع الجهة</label>
        <select class="select" name="type" <?= is_admin() ? '' : 'disabled' ?>>
          <?php foreach (['school', 'quran', 'women', 'sports', 'club', 'other'] as $type): ?>
            <option value="<?= $type ?>" <?= $entity['type'] === $type ? 'selected' : '' ?>><?= e(label('entity_type', $type)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>المدينة</label>
        <input class="input" name="city" value="<?= e($entity['city']) ?>" <?= is_admin() ? '' : 'disabled' ?>>
      </div>
      <div class="field">
        <label>الجوال</label>
        <input class="input" type="tel" name="phone" value="<?= e($entity['phone']) ?>" <?= is_admin() ? '' : 'disabled' ?>>
      </div>
      <div class="field">
        <label>البريد الإلكتروني</label>
        <input class="input" type="email" name="email" value="<?= e($entity['email']) ?>" <?= is_admin() ? '' : 'disabled' ?>>
      </div>
      <?php if (is_admin()): ?>
        <div class="field full" style="display:flex;align-items:center;gap:10px">
          <input type="checkbox" id="allow_self_register" name="allow_self_register" value="1"
                 <?= $entity['allow_self_register'] ? 'checked' : '' ?>>
          <label for="allow_self_register" style="margin:0">تفعيل رابط التسجيل الذاتي للطلاب</label>
        </div>
        <div class="field full" style="display:flex;align-items:center;gap:10px">
          <input type="checkbox" id="public_board" name="public_board" value="1"
                 <?= $entity['public_board'] ? 'checked' : '' ?>>
          <label for="public_board" style="margin:0">إظهار لوحة الشرف العامة للجهة</label>
        </div>
        <div class="full"><button class="btn btn-primary" type="submit">حفظ البيانات</button></div>
      <?php endif; ?>
    </form>

    <?php
    $joinLink  = absolute_url('/join/' . $entity['slug']);
    $boardLink = absolute_url('/e/' . $entity['slug']);
    ?>
    <div class="mt-6">
      <h3 style="font-size:15px;margin-bottom:10px">روابط جهتك العامة</h3>

      <div class="card card-pad" style="background:#f9fafb;margin-bottom:10px">
        <b class="small">رابط التسجيل الذاتي <?= $entity['allow_self_register'] ? '' : '(معطّل حالياً)' ?></b>
        <div class="small" style="word-break:break-all;margin:6px 0" dir="ltr"><?= e($joinLink) ?></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <button type="button" class="btn btn-outline btn-sm" data-copy="<?= e($joinLink) ?>">نسخ</button>
          <a class="btn btn-outline btn-sm" href="<?= e($joinLink) ?>" target="_blank" rel="noopener">فتح</a>
        </div>
      </div>

      <div class="card card-pad" style="background:#f9fafb">
        <b class="small">رابط لوحة الشرف العامة <?= $entity['public_board'] ? '' : '(معطّل حالياً)' ?></b>
        <div class="small" style="word-break:break-all;margin:6px 0" dir="ltr"><?= e($boardLink) ?></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <button type="button" class="btn btn-outline btn-sm" data-copy="<?= e($boardLink) ?>">نسخ</button>
          <a class="btn btn-outline btn-sm" href="<?= e($boardLink) ?>" target="_blank" rel="noopener">فتح</a>
        </div>
      </div>
    </div>

    <div class="mt-6">
      <h3 style="font-size:15px;margin-bottom:10px">تغيير كلمة المرور</h3>
      <form method="post" action="<?= url('/settings/password') ?>" class="form-grid">
        <?= csrf_field() ?>
        <div class="field">
          <label>كلمة المرور الحالية</label>
          <input class="input" type="password" name="current_password" required>
        </div>
        <div class="field">
          <label>كلمة المرور الجديدة</label>
          <input class="input" type="password" name="new_password" required>
        </div>
        <div class="field">
          <label>تأكيد كلمة المرور</label>
          <input class="input" type="password" name="confirm_password" required>
        </div>
        <div class="field" style="padding-top:26px">
          <button class="btn btn-outline btn-block" type="submit">تغيير كلمة المرور</button>
        </div>
      </form>
    </div>

    <div class="mt-6">
      <h3 style="font-size:15px;margin-bottom:8px">حالة الاشتراك</h3>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <span class="tag <?= $entity['subscription_status'] === 'active' ? 'tag-green' : ($entity['subscription_status'] === 'trial' ? 'tag-gold' : 'tag-red') ?>">
          <?= $entity['subscription_status'] === 'active' ? 'اشتراك فعّال' : ($entity['subscription_status'] === 'trial' ? 'تجربة مجانية' : 'منتهي') ?>
        </span>
        <?php if ($entity['subscription_ends_at']): ?>
          <span class="tag tag-gray">ينتهي في <?= e(ar_date($entity['subscription_ends_at'])) ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>المشرفون والمستخدمون</h2></div>

    <?php foreach ($users as $row): ?>
      <div class="activity">
        <span class="avatar"><?= e(initials($row['name'])) ?></span>
        <div class="body">
          <b><?= e($row['name']) ?></b>
          <small dir="ltr" style="display:block"><?= e($row['email']) ?></small>
        </div>
        <span class="tag <?= $row['role'] === 'admin' ? 'tag-brand' : 'tag-gray' ?>"><?= e(label('role', $row['role'])) ?></span>
        <?php if (is_admin() && (int) $row['id'] !== (int) $user['id']): ?>
          <form method="post" action="<?= url('/settings/users/' . (int) $row['id'] . '/delete') ?>" data-confirm="حذف المستخدم؟">
            <?= csrf_field() ?>
            <button class="btn btn-danger btn-sm" type="submit">حذف</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <?php if (is_admin()): ?>
      <form method="post" action="<?= url('/settings/users') ?>" class="mt-6">
        <?= csrf_field() ?>
        <h3 style="font-size:15px;margin-bottom:10px">إضافة مشرف</h3>
        <div class="field">
          <label>الاسم</label>
          <input class="input" name="name" required>
        </div>
        <div class="field">
          <label>البريد الإلكتروني</label>
          <input class="input" type="email" name="email" required>
        </div>
        <div class="field">
          <label>كلمة المرور</label>
          <input class="input" type="password" name="password" required>
        </div>
        <div class="field">
          <label>الصلاحية</label>
          <select class="select" name="role">
            <option value="supervisor">مشرف</option>
            <option value="admin">مدير الجهة</option>
          </select>
        </div>
        <button class="btn btn-primary btn-block" type="submit">إضافة المستخدم</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php if (is_admin()): ?>
  <div class="panel mt-4">
    <div class="panel-head"><h2>روابط سريعة</h2></div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn btn-outline btn-sm" href="<?= url('/point-items') ?>">⚙️ بنود النقاط</a>
      <a class="btn btn-outline btn-sm" href="<?= url('/badges') ?>">🎖️ الأوسمة</a>
      <a class="btn btn-outline btn-sm" href="<?= url('/rewards') ?>">🎁 متجر الجوائز</a>
      <a class="btn btn-outline btn-sm" href="<?= url('/messages') ?>">✉️ رسائل التواصل</a>
      <a class="btn btn-outline btn-sm" href="<?= url('/notifications') ?>">🔔 التنبيهات</a>
      <a class="btn btn-outline btn-sm" href="<?= url('/export/students') ?>">⬇️ تصدير الطلاب</a>
    </div>
  </div>
<?php endif; ?>
