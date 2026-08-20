<?php
/** صفحات الموقع التعريفي. */

function page_home(): void
{
    view('site/home', [
        'title' => brand('full_name') . ' — ' . brand('tagline'),
    ], 'site');
}

function page_features(): void
{
    view('site/features', ['title' => 'المميزات — ' . brand('name')], 'site');
}

function page_pricing(): void
{
    view('site/pricing', ['title' => 'الاشتراك — ' . brand('name')], 'site');
}

function page_faq(): void
{
    view('site/faq', ['title' => 'الأسئلة الشائعة — ' . brand('name')], 'site');
}

function page_contact(array $old = [], array $errors = []): void
{
    view('site/contact', [
        'title'  => 'تواصل معنا — ' . brand('name'),
        'old'    => $old,
        'errors' => $errors,
    ], 'site');
}

function page_contact_submit(): void
{
    $data = [
        'name'        => input('name', ''),
        'email'       => input('email', ''),
        'phone'       => input('phone', ''),
        'entity_type' => input('entity_type', ''),
        'subject'     => input('subject', ''),
        'message'     => input('message', ''),
    ];

    $errors = [];
    if (mb_strlen($data['name']) < 2) {
        $errors['name'] = 'الرجاء إدخال الاسم';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'الرجاء إدخال بريد إلكتروني صحيح';
    }
    if (mb_strlen($data['message']) < 5) {
        $errors['message'] = 'الرجاء كتابة رسالتك';
    }

    if ($errors) {
        page_contact($data, $errors);
        return;
    }

    db_insert('contact_messages', [
        'name'        => $data['name'],
        'email'       => $data['email'],
        'phone'       => $data['phone'] ?: null,
        'entity_type' => $data['entity_type'] ?: null,
        'subject'     => $data['subject'] ?: null,
        'message'     => $data['message'],
    ]);

    flash('success', 'تم إرسال رسالتك بنجاح، وسنتواصل معك قريباً بإذن الله.');
    redirect('/contact');
}
