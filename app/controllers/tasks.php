<?php
/** المهام والدرجات. */

function tasks_page(): void
{
    require_login();
    $entityId = current_entity_id();

    view('app/tasks', [
        'title'  => 'المهام والدرجات',
        'tasks'  => tasks_list($entityId),
        'groups' => groups_list($entityId),
    ], 'app');
}

function tasks_create(): void
{
    require_login();

    $title = input('title', '');
    if (mb_strlen($title) < 2) {
        flash('error', 'أدخل عنوان المهمة');
        redirect('/tasks');
    }

    task_create(current_entity_id(), [
        'title'       => $title,
        'description' => input('description', ''),
        'group_id'    => input_int('group_id'),
        'due_date'    => input('due_date', ''),
        'max_score'   => input_int('max_score', 10),
        'points'      => input_int('points', 10),
    ], (int) current_user()['id']);

    flash('success', 'تمت إضافة المهمة وتوزيعها على الطلاب');
    redirect('/tasks');
}

function task_page(int $id): void
{
    require_login();
    $task = guard_entity(task_get($id));

    view('app/task', [
        'title'       => $task['title'],
        'task'        => $task,
        'submissions' => task_submissions($id),
    ], 'app');
}

function task_grade_action(int $id): void
{
    require_login();
    guard_entity(task_get($id));

    $scores = (array) ($_POST['score'] ?? []);
    $graded = 0;

    foreach ($scores as $submissionId => $score) {
        if ($score === '' || !is_numeric($score)) {
            continue;
        }
        try {
            task_grade(current_entity_id(), (int) $submissionId, (int) $score);
            $graded++;
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
    }

    flash($graded ? 'success' : 'info', $graded ? "تم رصد درجات $graded طالباً ومنح النقاط" : 'لم يتم رصد أي درجة');
    redirect('/tasks/' . $id);
}
