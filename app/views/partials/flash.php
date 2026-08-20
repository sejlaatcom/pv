<?php $messages = flash_pull(); ?>
<?php if ($messages): ?>
  <div class="flash-wrap">
    <?php foreach ($messages as $message): ?>
      <div class="flash flash-<?= e($message['type']) ?>">
        <span><?= e($message['message']) ?></span>
        <button type="button" class="flash-close" onclick="this.parentElement.remove()">×</button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
