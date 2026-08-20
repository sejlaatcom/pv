<?php
/** رصد النقاط وبنودها ولوحة المتصدرين. */

function award_page(): void
{
    require_login();
    $entityId = current_entity_id();
    $groupId = input_int('group_id');

    view('app/award', [
        'title'    => 'رصد النقاط',
        'groups'   => groups_list($entityId),
        'students' => students_list($entityId, ['group_id' => $groupId, 'only_active' => true]),
        'items'    => point_items_list($entityId, true),
        'groupId'  => $groupId,
    ], 'app');
}

function award_submit(): void
{
    require_login();
    $entityId = current_entity_id();

    $studentIds = (array) ($_POST['student_ids'] ?? []);
    try {
        $result = points_award($entityId, $studentIds, [
            'item_id' => input_int('item_id'),
            'points'  => input('points', ''),
            'reason'  => input('reason', ''),
        ]);
        $sign = $result['points'] > 0 ? '+' : '';
        flash('success', "تم رصد {$sign}{$result['points']} نقطة لعدد {$result['count']} طالب");
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }

    redirect('/award' . (input_int('group_id') ? '?group_id=' . input_int('group_id') : ''));
}

function point_items_page(): void
{
    require_login();
    view('app/point_items', [
        'title' => 'بنود النقاط',
        'items' => point_items_list(current_entity_id()),
    ], 'app');
}

function point_items_create(): void
{
    require_login();

    $title = input('title', '');
    $points = input_int('points');

    if (mb_strlen($title) < 2 || !$points) {
        flash('error', 'أدخل اسم البند وقيمة النقاط (يمكن أن تكون سالبة)');
        redirect('/point-items');
    }

    db_insert('point_items', [
        'entity_id' => current_entity_id(),
        'title'     => $title,
        'points'    => $points,
        'category'  => input('category', 'behavior'),
        'color'     => $points >= 0 ? '#10b981' : '#ef4444',
    ]);

    flash('success', 'تمت إضافة البند');
    redirect('/point-items');
}

function point_items_delete(int $id): void
{
    require_login();
    guard_entity(db_one('SELECT * FROM point_items WHERE id = ?', [$id]));
    db_delete('point_items', $id);

    flash('success', 'تم حذف البند');
    redirect('/point-items');
}

function leaderboard_page(): void
{
    require_login();
    $entityId = current_entity_id();
    $period = input('period', 'month');
    if (!in_array($period, ['week', 'month', 'term', 'all'], true)) {
        $period = 'month';
    }
    $groupId = input_int('group_id');

    view('app/leaderboard', [
        'title'   => 'لوحة المتصدرين',
        'rows'    => leaderboard($entityId, $period, $groupId, 100),
        'groups'  => groups_list($entityId),
        'period'  => $period,
        'groupId' => $groupId,
    ], 'app');
}
