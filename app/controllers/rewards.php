<?php
/** الأوسمة ومتجر الجوائز وطلبات الاستبدال. */

function badges_page(): void
{
    require_login();
    view('app/badges', [
        'title'  => 'الأوسمة',
        'badges' => badges_list(current_entity_id()),
    ], 'app');
}

function badges_create(): void
{
    require_login();

    $title = input('title', '');
    if (mb_strlen($title) < 2) {
        flash('error', 'أدخل اسم الوسام');
        redirect('/badges');
    }

    $required = input_int('required_points');
    db_insert('badges', [
        'entity_id'       => current_entity_id(),
        'title'           => $title,
        'description'     => input('description', '') ?: null,
        'icon'            => input('icon', '🏅') ?: '🏅',
        'color'           => input('color', '#f59e0b') ?: '#f59e0b',
        'required_points' => $required ?: null,
    ]);

    flash('success', 'تمت إضافة الوسام');
    redirect('/badges');
}

function badges_delete(int $id): void
{
    require_admin();
    guard_entity(db_one('SELECT * FROM badges WHERE id = ?', [$id]));
    db_delete('badges', $id);

    flash('success', 'تم حذف الوسام');
    redirect('/badges');
}

function rewards_page(): void
{
    require_login();
    $entityId = current_entity_id();

    view('app/rewards', [
        'title'       => 'متجر الجوائز',
        'rewards'     => rewards_list($entityId),
        'redemptions' => redemptions_list($entityId),
    ], 'app');
}

function rewards_create(): void
{
    require_login();

    $title = input('title', '');
    $cost = input_int('cost');
    if (mb_strlen($title) < 2 || !$cost || $cost <= 0) {
        flash('error', 'أدخل اسم الجائزة وتكلفتها بالنقاط');
        redirect('/rewards');
    }

    db_insert('rewards', [
        'entity_id'   => current_entity_id(),
        'title'       => $title,
        'description' => input('description', '') ?: null,
        'cost'        => $cost,
        'stock'       => max(0, (int) input_int('stock', 0)),
        'icon'        => input('icon', '🎁') ?: '🎁',
    ]);

    flash('success', 'تمت إضافة الجائزة');
    redirect('/rewards');
}

function rewards_delete(int $id): void
{
    require_admin();
    guard_entity(db_one('SELECT * FROM rewards WHERE id = ?', [$id]));
    db_delete('rewards', $id);

    flash('success', 'تم حذف الجائزة');
    redirect('/rewards');
}

function redemption_update(int $id): void
{
    require_login();
    $status = input('status', '');
    if (!in_array($status, ['pending', 'approved', 'delivered', 'rejected'], true)) {
        flash('error', 'حالة غير صحيحة');
        redirect('/rewards');
    }

    try {
        redemption_set_status(current_entity_id(), $id, $status);
        flash('success', 'تم تحديث حالة الطلب');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }

    redirect('/rewards');
}
