<?php
$features = [
    ['01', '👥', 'جلب الأسماء والبيانات', 'استيراد المشاركين من ملفات Excel أو عبر رابط تسجيل مباشر خلال دقائق.', '#0d9488'],
    ['02', '📋', 'المسابقات والاستبانات', 'نظام متكامل للمسابقات والاستبانات مع التصحيح الآلي واحتساب النقاط.', '#22c55e'],
    ['03', '🏆', 'بنك المسابقات الجاهزة', 'مسابقات جاهزة قابلة للنسخ والتعديل حسب احتياج جهتك.', '#f59e0b'],
    ['04', '🎖️', 'نقاط وأوسمة وجوائز', 'نظام نقاط وأوسمة إلكترونية ومتجر جوائز لاستبدال النقاط.', '#8b5cf6'],
    ['05', '📅', 'الحضور والمهام والدرجات', 'متابعة الحضور والانصراف وتسليم المهام ورصد الدرجات يومياً.', '#0ea5e9'],
    ['06', '📱', 'واجهة للمشرف والطالب', 'واجهات مخصصة تعمل على الجوال والحاسب بسلاسة تامة.', '#14b8a6'],
    ['07', '💬', 'التنبيهات عبر الواتساب', 'إرسال التنبيهات والملاحظات لأولياء الأمور برسالة واحدة.', '#ec4899'],
    ['08', '📊', 'تقارير وإحصاءات لحظية', 'تقارير تفاعلية ومؤشرات أداء لحظية قابلة للطباعة.', '#0d9488'],
    ['09', '🗂️', 'إدارة المجموعات والفصول', 'إدارة الفصول والحلقات وتوزيع المشرفين بسهولة.', '#d97706'],
    ['10', '🌐', 'موقع خاص بالجهة', 'صفحة خاصة بجهتك ورابط مخصص لولي الأمر لمتابعة ابنه لحظياً.', '#8b5cf6'],
];

$sectors = [
    ['🏫', 'المدارس'],
    ['📗', 'حلقات تحفيظ القرآن'],
    ['🏛️', 'الدور النسائية'],
    ['⚽', 'الأكاديميات الرياضية'],
    ['🏖️', 'النوادي الصيفية'],
];
?>

<section class="hero">
  <div class="container hero-inner">
    <div>
      <span class="hero-badge">⭐ منصة سعودية لإدارة التحفيز</span>
      <h1>حوّل التحفيز إلى <span class="accent">نقاط وأوسمة</span> يتنافس عليها الطلاب</h1>
      <p class="lead"><?= e(brand('subline')) ?></p>
      <div class="hero-actions">
        <a class="btn btn-gold" href="<?= url('/register') ?>">ابدأ تجربتك المجانية</a>
        <a class="btn btn-ghost" href="<?= url('/pricing') ?>">الاشتراك السنوي <?= (int) brand('price') ?> <?= e(brand('currency')) ?></a>
      </div>

      <div class="hero-stats">
        <div><b>+50</b><span>جهة تعليمية</span></div>
        <div><b>+12,000</b><span>مشارك ومشاركة</span></div>
        <div><b>+400</b><span>مسابقة جاهزة</span></div>
        <div><b>99.9%</b><span>جاهزية المنصة</span></div>
      </div>
    </div>

    <div class="hero-cards">
      <div class="float-card">
        <div class="float-head"><span>لوحة المتصدرين</span><span class="tag tag-gold">هذا الأسبوع</span></div>
        <?php
        $preview = [['عبدالله الحربي', 1250, 'g1'], ['محمد العتيبي', 980, 'g2'], ['سلطان القحطاني', 870, 'g3'], ['فيصل الشمري', 760, ''], ['أنس الزهراني', 650, '']];
        foreach ($preview as $i => [$name, $points, $medal]): ?>
          <div class="rank-row<?= $i === 0 ? ' top' : '' ?>">
            <span class="rank-num <?= $medal ?>"><?= $i + 1 ?></span>
            <span class="rank-name"><?= e($name) ?></span>
            <span class="rank-points"><?= num($points) ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="float-card delayed">
        <small class="muted">مرحباً بك</small>
        <div style="font-weight:900;margin-bottom:12px">أنس الزهراني</div>
        <div class="points-badge">
          <small>المجموع الكلي</small>
          <b>1,250</b>
          <small>نقطة</small>
        </div>
        <div class="mini-grid">
          <div><span>🏆</span>المسابقات</div>
          <div><span>🎖️</span>الأوسمة</div>
          <div><span>🎁</span>الجوائز</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="chip">مميزات المنصة</span>
      <h2>كل ما تحتاجه لإدارة التحفيز في منصة واحدة</h2>
      <p>من استيراد الأسماء حتى تسليم الجوائز — أدوات جاهزة تختصر على المشرف ساعات العمل اليومية.</p>
    </div>

    <div class="grid grid-3">
      <?php foreach ($features as [$num, $icon, $title, $desc, $color]): ?>
        <div class="card feature-card card-hover">
          <div class="feature-icon" style="background: <?= $color ?>22; color: <?= $color ?>"><?= $icon ?></div>
          <div>
            <span class="feature-num" style="color: <?= $color ?>"><?= $num ?></span>
            <h3><?= e($title) ?></h3>
            <p><?= e($desc) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section-sm">
  <div class="container">
    <div class="section-head">
      <span class="chip chip-gold">القطاعات</span>
      <h2>يخدم العديد من الجهات</h2>
    </div>
    <div class="grid grid-5">
      <?php foreach ($sectors as [$emoji, $label]): ?>
        <div class="card sector-card card-hover">
          <span class="emoji"><?= $emoji ?></span>
          <b><?= e($label) ?></b>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="chip">خطوات البدء</span>
      <h2>ابدأ خلال ثلاث خطوات</h2>
    </div>
    <div class="grid grid-3">
      <div class="card step-card">
        <div class="step-num">1</div>
        <h3>أنشئ جهتك ومجموعاتك</h3>
        <p class="muted">أضف الفصول أو الحلقات، واستورد أسماء الطلاب من Excel أو بالنسخ واللصق.</p>
      </div>
      <div class="card step-card">
        <div class="step-num">2</div>
        <h3>اضبط بنود النقاط والجوائز</h3>
        <p class="muted">حدد السلوكيات الإيجابية والسلبية وقيمة كل بند، وأضف الأوسمة وجوائز المتجر.</p>
      </div>
      <div class="card step-card">
        <div class="step-num">3</div>
        <h3>ارصد وتابع لحظياً</h3>
        <p class="muted">ارصد النقاط بضغطة واحدة، وتابع المتصدرين والتقارير، وشارك ولي الأمر رابط ابنه.</p>
      </div>
    </div>
  </div>
</section>

<section class="section-sm">
  <div class="container">
    <div class="card price-split">
      <div class="card-pad" style="padding:46px">
        <span class="chip chip-gold">اشتراك سنوي</span>
        <h2 style="font-size:30px;margin-bottom:10px">اشتراك واحد لكل مميزات المنصة</h2>
        <p class="muted" style="margin-bottom:22px">بدون رسوم إضافية على عدد الطلاب أو المجموعات، ويشمل التحديثات والدعم الفني.</p>
        <ul class="check-list">
          <?php foreach ([
              'عدد غير محدود من الطلاب والمجموعات',
              'بنك مسابقات جاهزة مع التصحيح الآلي',
              'متجر جوائز وأوسمة إلكترونية',
              'تقارير لحظية قابلة للطباعة',
              'رابط خاص لكل ولي أمر',
              'دعم فني ونسخ احتياطي يومي',
          ] as $item): ?>
            <li><span class="tick">✓</span><?= e($item) ?></li>
          <?php endforeach; ?>
        </ul>
        <a class="btn btn-primary mt-4" href="<?= url('/register') ?>">ابدأ الآن</a>
      </div>
      <div class="price-side">
        <small style="opacity:.75;font-weight:700">الاشتراك السنوي</small>
        <div class="price-value"><?= (int) brand('price') ?></div>
        <div style="font-size:20px;font-weight:900;margin-top:6px"><?= e(brand('currency')) ?> فقط</div>
        <p style="opacity:.75;font-size:14px;margin-top:18px">تجربة مجانية قبل الاشتراك، وإمكانية الإلغاء في أي وقت.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="card card-pad center" style="background:linear-gradient(140deg,#0f766e,#134e4a);color:#fff;padding:52px 24px">
      <h2 style="font-size:32px;margin-bottom:12px">جاهز لتحفيز طلابك؟</h2>
      <p style="opacity:.8;max-width:560px;margin:0 auto 24px">
        أنشئ حساب جهتك مجاناً، وستجد بنود نقاط وأوسمة وجوائز جاهزة تبدأ بها فوراً.
      </p>
      <div class="hero-actions" style="justify-content:center">
        <a class="btn btn-white" href="<?= url('/register') ?>">إنشاء حساب مجاني</a>
        <a class="btn btn-ghost" href="<?= url('/contact') ?>">اطلب عرضاً تقديمياً</a>
      </div>
    </div>
  </div>
</section>
