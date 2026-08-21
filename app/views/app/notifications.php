<div class="grid" style="grid-template-columns:1fr 1.4fr">
  <div class="panel">
    <div class="panel-head"><h2>إرسال تنبيه</h2></div>
    <form method="post" action="<?= url('/notifications') ?>">
      <?= csrf_field() ?>
      <div class="field">
        <label>الطالب</label>
        <select class="select" name="student_id">
          <option value="">تنبيه عام (لكل الجهة)</option>
          <?php foreach ($students as $student): ?>
            <option value="<?= (int) $student['id'] ?>">
              <?= e($student['name']) ?><?= $student['group_name'] ? ' — ' . e($student['group_name']) : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>العنوان *</label>
        <input class="input" name="title" placeholder="ملاحظة عن الواجب" required>
      </div>
      <div class="field">
        <label>نص الرسالة</label>
        <textarea class="textarea" name="body" rows="4" placeholder="نص التنبيه الذي سيُرسل لولي الأمر"></textarea>
      </div>
      <div class="field">
        <label>القناة</label>
        <select class="select" name="channel">
          <option value="whatsapp">واتساب</option>
          <option value="app">داخل المنصة</option>
        </select>
      </div>
      <button class="btn btn-primary btn-block" type="submit">تسجيل التنبيه</button>
    </form>
    <p class="muted small mt-4">
      بعد التسجيل يظهر زر «إرسال واتساب» بجانب التنبيه، ويفتح المحادثة برسالة جاهزة تتضمن
      رابط متابعة الطالب.
    </p>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>سجل التنبيهات</h2>
      <span class="tag tag-brand"><?= count($notifications) ?> تنبيه</span>
    </div>

    <?php if (!$notifications): ?>
      <p class="muted">لم تُرسل أي تنبيهات بعد.</p>
    <?php endif; ?>

    <?php foreach ($notifications as $notification): ?>
      <?php
      $message = $notification['title']
          . ($notification['body'] ? "\n" . $notification['body'] : '')
          . ($notification['access_token'] ? "\n" . absolute_url('/p/' . $notification['access_token']) : '');
      ?>
      <div class="activity">
        <span class="avatar" style="background:<?= $notification['channel'] === 'whatsapp' ? '#dcfce7;color:#15803d' : '#e0f2fe;color:#075985' ?>">
          <?= $notification['channel'] === 'whatsapp' ? '💬' : '🔔' ?>
        </span>
        <div class="body">
          <b><?= e($notification['title']) ?></b>
          <small>
            <?= e($notification['student_name'] ?: 'تنبيه عام') ?>
            · <?= e($notification['sender'] ?: 'النظام') ?>
            · <?= ar_date($notification['created_at'], true) ?>
          </small>
          <?php if ($notification['body']): ?>
            <div class="muted small" style="margin-top:4px"><?= nl2br(e($notification['body'])) ?></div>
          <?php endif; ?>
        </div>
        <?php if ($notification['guardian_phone']): ?>
          <a class="btn btn-outline btn-sm" target="_blank" rel="noopener"
             href="<?= e(whatsapp_link($notification['guardian_phone'], $message)) ?>">إرسال واتساب</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
