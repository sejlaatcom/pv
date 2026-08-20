<?php
/** المسابقات والاستبانات مع التصحيح الآلي. */

function quizzes_page(): void
{
    require_login();
    view('app/quizzes', [
        'title'   => 'المسابقات والاستبانات',
        'quizzes' => quizzes_list(current_entity_id()),
    ], 'app');
}

function quiz_new_page(): void
{
    require_login();
    view('app/quiz_new', ['title' => 'مسابقة جديدة'], 'app');
}

function quiz_create_action(): void
{
    require_login();

    $questions = [];
    foreach ((array) ($_POST['q'] ?? []) as $row) {
        $questions[] = [
            'question' => $row['question'] ?? '',
            'options'  => (array) ($row['options'] ?? []),
            'correct'  => $row['correct'] ?? 0,
        ];
    }

    try {
        $quizId = quiz_create(current_entity_id(), [
            'title'              => input('title', ''),
            'description'        => input('description', ''),
            'type'               => input('type', 'quiz') === 'survey' ? 'survey' : 'quiz',
            'category'           => input('category', ''),
            'points_per_correct' => input_int('points_per_correct', 5),
            'time_limit_minutes' => input_int('time_limit_minutes'),
        ], $questions, (int) current_user()['id']);

        flash('success', 'تم إنشاء المسابقة، انشرها ليشارك بها الطلاب');
        redirect('/quizzes/' . $quizId);
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
        redirect('/quizzes/new');
    }
}

function quiz_page(int $id): void
{
    require_login();
    $quiz = guard_entity(quiz_get($id));

    view('app/quiz', [
        'title'     => $quiz['title'],
        'quiz'      => $quiz,
        'questions' => quiz_questions($id, true),
        'attempts'  => quiz_attempts($id),
    ], 'app');
}

function quiz_publish_action(int $id): void
{
    require_login();
    $quiz = guard_entity(quiz_get($id));

    $publish = input('is_published') === '1' ? 1 : 0;
    db_update('quizzes', $id, ['is_published' => $publish]);

    flash('success', $publish ? 'تم نشر المسابقة للطلاب' : 'تم إيقاف نشر المسابقة');
    redirect('/quizzes/' . $id);
}

function quiz_delete_action(int $id): void
{
    require_login();
    guard_entity(quiz_get($id));
    db_delete('quizzes', $id);

    flash('success', 'تم حذف المسابقة');
    redirect('/quizzes');
}
