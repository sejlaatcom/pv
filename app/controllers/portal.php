<?php
/** بوابة الطالب وولي الأمر — تُفتح برابط خاص بدون تسجيل دخول. */

function portal_find(string $token): array
{
    $student = student_by_token($token);
    if (!$student) {
        http_response_code(404);
        view('errors/404', ['message' => 'الرابط غير صحيح أو تم تغييره'], 'site');
        exit;
    }
    return $student;
}

function portal_page(string $token): void
{
    $student = portal_find($token);
    $entityId = (int) $student['entity_id'];

    view('portal/show', [
        'title'       => $student['name'],
        'student'     => $student,
        'profile'     => student_profile($student),
        'rewards'     => rewards_list($entityId, true),
        'leaderboard' => leaderboard($entityId, 'all', null, 10),
        'quizzes'     => db_all(
            'SELECT q.*, (SELECT COUNT(*) FROM quiz_questions x WHERE x.quiz_id = q.id) AS question_count,
                    (SELECT COUNT(*) FROM quiz_attempts a WHERE a.quiz_id = q.id AND a.student_id = ?) AS my_attempts
             FROM quizzes q WHERE q.entity_id = ? AND q.is_published = 1 ORDER BY q.created_at DESC',
            [$student['id'], $entityId]
        ),
    ], 'portal');
}

function portal_redeem(string $token): void
{
    $student = portal_find($token);

    try {
        redemption_request($student, (int) input_int('reward_id'));
        flash('success', 'تم إرسال طلب الاستبدال، بانتظار اعتماد المشرف');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }

    redirect('/p/' . $token);
}

function portal_quiz_page(string $token, int $id): void
{
    $student = portal_find($token);
    $quiz = db_one('SELECT * FROM quizzes WHERE id = ? AND entity_id = ?', [$id, $student['entity_id']]);

    if (!$quiz || !$quiz['is_published']) {
        flash('error', 'المسابقة غير متاحة');
        redirect('/p/' . $token);
    }

    $attempt = db_one('SELECT * FROM quiz_attempts WHERE quiz_id = ? AND student_id = ?', [$id, $student['id']]);

    view('portal/quiz', [
        'title'     => $quiz['title'],
        'student'   => $student,
        'quiz'      => $quiz,
        'questions' => quiz_questions($id),
        'attempt'   => $attempt,
    ], 'portal');
}

function portal_quiz_submit(string $token, int $id): void
{
    $student = portal_find($token);
    $answers = (array) ($_POST['answer'] ?? []);

    try {
        $result = quiz_submit($student, $id, $answers);
        flash(
            'success',
            sprintf(
                'أحسنت! إجاباتك الصحيحة %d من %d، وحصلت على %d نقطة.',
                $result['correct'],
                $result['total'],
                $result['points_awarded']
            )
        );
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }

    redirect('/p/' . $token . '/quiz/' . $id);
}

function portal_login_page(?string $error = null): void
{
    view('portal/login', [
        'title' => 'دخول الطالب',
        'error' => $error,
    ], 'blank');
}

function portal_login_submit(): void
{
    $code = trim((string) input('token', ''));
    $student = $code !== '' ? student_by_token($code) : null;

    if (!$student) {
        portal_login_page('رمز الدخول غير صحيح، اطلب الرابط من مشرفك');
        return;
    }

    redirect('/p/' . $student['access_token']);
}
