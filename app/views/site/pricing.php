<?php
$plans = [
    [
        'name' => 'التجربة المجانية', 'price' => '0', 'period' => 'لمدة 14 يوماً', 'featured' => false,
        'cta' => 'ابدأ التجربة', 'href' => '/register',
        'features' => [['حتى 30 طالباً', true], ['مجموعتان', true], ['رصد النقاط والأوسمة', true], ['متجر الجوائز', true], ['المسابقات والتصحيح الآلي', false], ['التقارير المتقدمة', false]],
    ],
    [
        'name' => 'الاشتراك السنوي', 'price' => (string) (int) brand('price'), 'period' => brand('currency') . ' / سنوياً', 'featured' => true,
        'cta' => 'اشترك الآن', 'href' => '/contact',
        'features' => [['طلاب ومجموعات بلا حدود', true], ['رصد النقاط والأوسمة', true], ['متجر الجوائز والاستبدال', true], ['المسابقات مع التصحيح الآلي', true], ['الحضور والمهام والدرجات', true], ['تقارير لحظية ورابط ولي الأمر', true]],
    ],
    [
        'name' => 'باقة المؤسسات', 'price' => 'حسب الطلب', 'period' => 'لأكثر من جهة', 'featured' => false,
        'cta' => 'تواصل معنا', 'href' => '/contact',
        'features' => [['كل مميزات الاشتراك السنوي', true], ['إدارة عدة جهات', true], ['هوية بصرية خاصة', true], ['تدريب المشرفين', true], ['مدير حساب مخصص', true], ['تكامل مع أنظمة الجهة', true]],
    ],
];
?>
<section class="hero">
  <div class="container center" style="padding:56px 20px">
    <span class="hero-badge">💳 الاشتراك</span>
    <h1 style="font-size:40px;color:#fff">أسعار واضحة بلا مفاجآت</h1>
    <p class="lead" style="margin:12px auto 0">اشتراك سنوي واحد يشمل جميع المميزات والتحديثات والدعم الفني.</p>
  </div>
</section>

<section class="section">
  <div class="container grid grid-3">
    <?php foreach ($plans as $plan): ?>
      <div class="card plan<?= $plan['featured'] ? ' featured' : '' ?>">
        <?php if ($plan['featured']): ?><span class="tag tag-gold plan-tag">الأكثر طلباً</span><?php endif; ?>
        <h3><?= e($plan['name']) ?></h3>
        <div class="plan-price"><?= e($plan['price']) ?></div>
        <div class="muted small" style="margin-bottom:20px"><?= e($plan['period']) ?></div>
        <ul class="check-list">
          <?php foreach ($plan['features'] as [$label, $included]): ?>
            <li class="<?= $included ? '' : 'off' ?>">
              <span class="tick" style="<?= $included ? '' : 'background:#f3f4f6;color:#9ca3af' ?>"><?= $included ? '✓' : '×' ?></span>
              <?= e($label) ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <a class="btn <?= $plan['featured'] ? 'btn-primary' : 'btn-outline' ?> btn-block mt-4" href="<?= url($plan['href']) ?>"><?= e($plan['cta']) ?></a>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="container mt-6">
    <div class="card card-pad center">
      <h3>هل لديك سؤال حول الاشتراك؟</h3>
      <p class="muted" style="margin:8px 0 16px">فريقنا جاهز لمساعدتك في اختيار الباقة المناسبة لجهتك.</p>
      <a class="btn btn-outline" href="<?= url('/contact') ?>">تواصل معنا</a>
    </div>
  </div>
</section>
