<?php
/** لوحة القيادة والتقارير. */

function dashboard_page(): void
{
    $user = require_login();
    $entityId = current_entity_id();

    view('app/dashboard', [
        'title' => 'لوحة القيادة',
        'stats' => dashboard_stats($entityId),
        'user'  => $user,
    ], 'app');
}

function reports_page(): void
{
    require_login();
    $period = input('period', 'month');
    if (!in_array($period, ['week', 'month', 'term', 'all'], true)) {
        $period = 'month';
    }

    view('app/reports', [
        'title'   => 'التقارير والإحصاءات',
        'report'  => reports_data(current_entity_id(), $period),
        'period'  => $period,
    ], 'app');
}

function messages_page(): void
{
    require_admin();
    view('app/messages', [
        'title'    => 'رسائل التواصل',
        'messages' => db_all('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 100'),
    ], 'app');
}
