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
        <div class="full"><button class="btn btn-primary" type="submit">حفظ البيانات</button></div>
      <?php endif; ?>
    </form>

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
    </div>
  </div>
<?php endif; ?>
