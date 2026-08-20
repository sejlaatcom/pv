<div class="table-wrap">
  <table class="data">
    <thead><tr><th>المرسل</th><th>الجهة</th><th>الموضوع</th><th>الرسالة</th><th>التاريخ</th></tr></thead>
    <tbody>
      <?php foreach ($messages as $message): ?>
        <tr>
          <td>
            <b><?= e($message['name']) ?></b>
            <small class="muted" style="display:block" dir="ltr"><?= e($message['email']) ?></small>
            <?php if ($message['phone']): ?><small class="muted" dir="ltr"><?= e($message['phone']) ?></small><?php endif; ?>
          </td>
          <td class="muted"><?= e($message['entity_type'] ?: '—') ?></td>
          <td><?= e($message['subject'] ?: '—') ?></td>
          <td style="max-width:380px"><?= nl2br(e($message['message'])) ?></td>
          <td class="muted small nowrap"><?= ar_date($message['created_at'], true) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$messages): ?>
        <tr><td colspan="5" class="center muted">لا توجد رسائل.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
