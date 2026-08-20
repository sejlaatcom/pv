<?php
/**
 * رسوم بيانية بصيغة SVG مولّدة من PHP — بدون أي مكتبات خارجية،
 * لتعمل على أي استضافة وبدون اتصال بالإنترنت.
 *
 * كل دالة تستقبل مصفوفة عناصر بالشكل: ['label' => '...', 'value' => 12]
 */

/** رسم خطي/مساحي */
function chart_area(array $data, string $color = '#0d9488', int $height = 220): string
{
    if (!$data) {
        return '<p class="muted">لا توجد بيانات بعد.</p>';
    }

    $width = 720;
    $padX = 34;
    $padY = 18;
    $max = max(1, max(array_map(fn($d) => (int) $d['value'], $data)));
    $count = count($data);
    $stepX = $count > 1 ? ($width - $padX * 2) / ($count - 1) : 0;
    $usable = $height - $padY * 2 - 18;

    $points = [];
    foreach (array_values($data) as $i => $row) {
        $x = $padX + $stepX * $i;
        $y = $padY + $usable - ($usable * ((int) $row['value'] / $max));
        $points[] = [$x, $y, $row];
    }

    $line = implode(' ', array_map(fn($p) => round($p[0], 1) . ',' . round($p[1], 1), $points));
    $areaPath = 'M ' . round($points[0][0], 1) . ',' . round($padY + $usable, 1) . ' L ' . str_replace(' ', ' L ', $line)
        . ' L ' . round(end($points)[0], 1) . ',' . round($padY + $usable, 1) . ' Z';

    $gridLines = '';
    for ($g = 0; $g <= 3; $g++) {
        $y = $padY + $usable - ($usable * $g / 3);
        $value = (int) round($max * $g / 3);
        $gridLines .= sprintf(
            '<line x1="%d" y1="%.1f" x2="%d" y2="%.1f" stroke="#e5e7eb" stroke-width="1"/>' .
            '<text x="%d" y="%.1f" font-size="10" fill="#9ca3af" text-anchor="start">%s</text>',
            $padX, $y, $width - $padX, $y, $width - $padX + 4, $y + 3, $value
        );
    }

    $dots = '';
    $labels = '';
    $labelEvery = max(1, (int) ceil($count / 8));
    foreach ($points as $i => $p) {
        $dots .= sprintf(
            '<circle cx="%.1f" cy="%.1f" r="3.5" fill="#fff" stroke="%s" stroke-width="2"><title>%s: %s</title></circle>',
            $p[0], $p[1], $color, e($p[2]['label']), num($p[2]['value'])
        );
        if ($i % $labelEvery === 0) {
            $labels .= sprintf(
                '<text x="%.1f" y="%d" font-size="10" fill="#9ca3af" text-anchor="middle">%s</text>',
                $p[0], $height - 4, e($p[2]['label'])
            );
        }
    }

    $id = 'grad' . substr(md5($color . $height . $count), 0, 6);

    return <<<SVG
<svg viewBox="0 0 {$width} {$height}" class="chart" preserveAspectRatio="none" role="img">
  <defs>
    <linearGradient id="{$id}" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="{$color}" stop-opacity="0.35"/>
      <stop offset="100%" stop-color="{$color}" stop-opacity="0.02"/>
    </linearGradient>
  </defs>
  {$gridLines}
  <path d="{$areaPath}" fill="url(#{$id})"/>
  <polyline points="{$line}" fill="none" stroke="{$color}" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
  {$dots}
  {$labels}
</svg>
SVG;
}

/** رسم أعمدة */
function chart_bars(array $data, array $colors = [], int $height = 230): string
{
    if (!$data) {
        return '<p class="muted">لا توجد بيانات بعد.</p>';
    }

    $palette = $colors ?: ['#0d9488', '#8b5cf6', '#f59e0b', '#0ea5e9', '#ec4899', '#22c55e'];
    $width = 720;
    $padX = 30;
    $padY = 16;
    $usable = $height - $padY - 34;
    $max = max(1, max(array_map(fn($d) => (int) $d['value'], $data)));
    $count = count($data);
    $slot = ($width - $padX * 2) / max(1, $count);
    $barWidth = min(70, $slot * 0.55);

    $bars = '';
    foreach (array_values($data) as $i => $row) {
        $value = (int) $row['value'];
        $barHeight = max(2, $usable * ($value / $max));
        $x = $padX + $slot * $i + ($slot - $barWidth) / 2;
        $y = $padY + $usable - $barHeight;
        $color = $palette[$i % count($palette)];
        $bars .= sprintf(
            '<rect x="%.1f" y="%.1f" width="%.1f" height="%.1f" rx="8" fill="%s"><title>%s: %s</title></rect>' .
            '<text x="%.1f" y="%.1f" font-size="10" fill="#6b7280" text-anchor="middle">%s</text>' .
            '<text x="%.1f" y="%d" font-size="11" fill="#374151" text-anchor="middle">%s</text>',
            $x, $y, $barWidth, $barHeight, $color, e($row['label']), num($value),
            $x + $barWidth / 2, $y - 5, num($value),
            $x + $barWidth / 2, $height - 8, e(mb_strimwidth($row['label'], 0, 18, '…', 'UTF-8'))
        );
    }

    return '<svg viewBox="0 0 ' . $width . ' ' . $height . '" class="chart" role="img">' . $bars . '</svg>';
}

/** رسم دائري مفرغ */
function chart_donut(array $data, int $size = 200): string
{
    $data = array_values(array_filter($data, fn($d) => (int) $d['value'] > 0));
    if (!$data) {
        return '<p class="muted">لا توجد بيانات بعد.</p>';
    }

    $palette = ['#0d9488', '#f59e0b', '#8b5cf6', '#0ea5e9', '#ec4899', '#22c55e', '#ef4444'];
    $total = array_sum(array_map(fn($d) => (int) $d['value'], $data));
    $radius = $size / 2 - 10;
    $inner = $radius * 0.62;
    $cx = $cy = $size / 2;
    $angle = -M_PI / 2;
    $paths = '';

    foreach ($data as $i => $row) {
        $share = (int) $row['value'] / $total;
        $sweep = $share * 2 * M_PI;
        $end = $angle + $sweep;
        $large = $sweep > M_PI ? 1 : 0;

        $x1 = $cx + $radius * cos($angle);
        $y1 = $cy + $radius * sin($angle);
        $x2 = $cx + $radius * cos($end);
        $y2 = $cy + $radius * sin($end);
        $x3 = $cx + $inner * cos($end);
        $y3 = $cy + $inner * sin($end);
        $x4 = $cx + $inner * cos($angle);
        $y4 = $cy + $inner * sin($angle);

        // القطاع الكامل يُرسم كحلقة كاملة لتفادي مسار بزاوية صفرية
        if ($share >= 0.999) {
            $paths .= sprintf(
                '<circle cx="%.1f" cy="%.1f" r="%.1f" fill="none" stroke="%s" stroke-width="%.1f"><title>%s: %s</title></circle>',
                $cx, $cy, ($radius + $inner) / 2, $palette[$i % count($palette)], $radius - $inner,
                e($row['label']), num($row['value'])
            );
        } else {
            $paths .= sprintf(
                '<path d="M %.2f %.2f A %.2f %.2f 0 %d 1 %.2f %.2f L %.2f %.2f A %.2f %.2f 0 %d 0 %.2f %.2f Z" fill="%s"><title>%s: %s</title></path>',
                $x1, $y1, $radius, $radius, $large, $x2, $y2,
                $x3, $y3, $inner, $inner, $large, $x4, $y4,
                $palette[$i % count($palette)], e($row['label']), num($row['value'])
            );
        }
        $angle = $end;
    }

    $legend = '<ul class="chart-legend">';
    foreach ($data as $i => $row) {
        $percent = round((int) $row['value'] / $total * 100);
        $legend .= sprintf(
            '<li><span class="dot" style="background:%s"></span>%s <b>%s%%</b></li>',
            $palette[$i % count($palette)],
            e($row['label']),
            $percent
        );
    }
    $legend .= '</ul>';

    return '<div class="donut-wrap"><svg viewBox="0 0 ' . $size . ' ' . $size . '" class="donut" role="img">'
        . $paths
        . sprintf('<text x="%d" y="%d" text-anchor="middle" font-size="18" font-weight="700" fill="#111827">%s</text>', $cx, $cy + 6, num($total))
        . '</svg>' . $legend . '</div>';
}

/** شريط تقدم بسيط */
function progress_bar(int $value, int $max, string $color = '#0d9488'): string
{
    $percent = $max > 0 ? min(100, (int) round($value / $max * 100)) : 0;
    return '<div class="progress"><span style="width:' . $percent . '%;background:' . $color . '"></span></div>';
}
