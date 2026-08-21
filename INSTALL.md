# دليل تشغيل منصة نقاطي على السيرفر المحلي (localhost)

هذا الدليل يشرح تنزيل النسخة كاملة وتشغيلها على جهازك خطوة بخطوة.
اختر الطريقة التي تناسبك:

- [الطريقة 1: XAMPP على ويندوز (الأشهر)](#الطريقة-1-xampp-على-ويندوز)
- [الطريقة 2: خادم PHP المدمج (الأسرع)](#الطريقة-2-خادم-php-المدمج)
- [الطريقة 3: Laragon أو MAMP](#الطريقة-3-laragon-أو-mamp)
- [بعد التنصيب](#بعد-التنصيب)
- [حل المشكلات الشائعة](#حل-المشكلات-الشائعة)

---

## أولاً: تنزيل النسخة

### أ) عبر Git

```bash
git clone -b claude/student-points-website-9n3cjn https://github.com/sejlaatcom/pv.git noqati
cd noqati
```

### ب) بدون Git

افتح صفحة المستودع على GitHub ← اختر الفرع `claude/student-points-website-9n3cjn`
← زر **Code** ← **Download ZIP**، ثم فُك الضغط وسمّ المجلد `noqati`.

---

## الطريقة 1: XAMPP على ويندوز

### 1) تثبيت XAMPP

نزّل XAMPP من <https://www.apachefriends.org> وثبّته (يفضّل إصدار PHP 8.1 فأعلى).

### 2) وضع المشروع في مجلد الويب

انسخ مجلد `noqati` كاملاً إلى:

```
C:\xampp\htdocs\noqati
```

بحيث يصبح المسار `C:\xampp\htdocs\noqati\public\index.php` موجوداً.

### 3) تشغيل الخدمات

افتح **XAMPP Control Panel** واضغط **Start** أمام:

- **Apache**
- **MySQL**

### 4) إنشاء قاعدة البيانات

افتح <http://localhost/phpmyadmin> ثم:

1. اضغط **New / جديد**.
2. اسم القاعدة: `noqati`
3. الترميز (Collation): **`utf8mb4_unicode_ci`** — مهم جداً لظهور العربية.
4. اضغط **Create**.

### 5) ملف الإعدادات

أنشئ ملفاً جديداً باسم `app/config.local.php` داخل مجلد المشروع، والصق فيه:

```php
<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'noqati',
        'user' => 'root',
        'pass' => '',          // في XAMPP كلمة مرور root فارغة افتراضياً
    ],
    'app' => [
        'base_url' => '/noqati',   // اسم المجلد داخل htdocs
        'timezone' => 'Asia/Riyadh',
    ],
];
```

> **مهم:** إذا وضعت المشروع في جذر `htdocs` مباشرة (أي `C:\xampp\htdocs\public`)،
> اجعل `'base_url' => ''` فارغاً. أما إذا كان داخل مجلد `noqati` فاكتب `'/noqati'`
> كما في المثال أعلاه.

### 6) إنشاء الجداول والبيانات التجريبية

افتح **موجّه الأوامر (CMD)** ونفّذ:

```cmd
cd C:\xampp\htdocs\noqati
C:\xampp\php\php.exe database\install.php --demo
```

ستظهر لك رسالة نجاح مع بيانات الدخول.

**بديل بدون سطر أوامر:** من phpMyAdmin، اختر قاعدة `noqati` ← تبويب **Import /
استيراد** ← اختر الملف `database/schema.sql` ← **Go**. (في هذه الحالة لن تتوفر
البيانات التجريبية، فأنشئ حساب جهتك من صفحة `/register`.)

### 7) افتح الموقع

```
http://localhost/noqati/
```

لوحة التحكم: <http://localhost/noqati/login>

---

## الطريقة 2: خادم PHP المدمج

أسرع طريقة إذا كان لديك PHP و MySQL مثبتين (بدون Apache):

```bash
cd noqati

# 1) الإعدادات
cat > app/config.local.php <<'PHP'
<?php
return [
    'db'  => ['host' => '127.0.0.1', 'name' => 'noqati', 'user' => 'root', 'pass' => ''],
    'app' => ['base_url' => '', 'timezone' => 'Asia/Riyadh'],
];
PHP

# 2) إنشاء القاعدة والجداول والبيانات التجريبية
php database/install.php --demo

# 3) التشغيل
php -S localhost:8000 -t public public/router.php
```

ثم افتح <http://localhost:8000>

> لاحظ استخدام `public/router.php` — بدونه لن تظهر ملفات التنسيق والصور.

---

## الطريقة 3: Laragon أو MAMP

**Laragon (ويندوز):**

1. ضع المشروع في `C:\laragon\www\noqati`
2. Laragon ينشئ نطاقاً تلقائياً: `http://noqati.test`
3. في الإعدادات اجعل `'base_url' => ''` (لأن النطاق يشير للمشروع مباشرة).
4. اضبط مجلد الجذر على `public` من: القائمة اليمنى ← Apache ← sites-enabled،
   أو اترك ملف `.htaccess` في الجذر يقوم بالتوجيه تلقائياً.
5. أنشئ القاعدة من HeidiSQL المرفق، ثم:
   `C:\laragon\bin\php\php-8.x\php.exe database\install.php --demo`

**MAMP (ماك):**

1. ضع المشروع في `/Applications/MAMP/htdocs/noqati`
2. المنفذ الافتراضي 8888، والمستخدم `root` وكلمة المرور `root`:
   ```php
   'db' => ['host' => '127.0.0.1', 'name' => 'noqati', 'user' => 'root', 'pass' => 'root'],
   'app' => ['base_url' => '/noqati'],
   ```
3. التنصيب: `/Applications/MAMP/bin/php/php8.x.x/bin/php database/install.php --demo`
4. الرابط: `http://localhost:8888/noqati/`

---

## بعد التنصيب

### بيانات الدخول التجريبية

| الدور | البريد | كلمة المرور |
|-------|--------|--------------|
| مدير الجهة | `admin@demo.local` | `123456` |
| مشرف | `supervisor@demo.local` | `123456` |

> غيّر كلمة المرور فوراً من صفحة **الإعدادات**، وإذا كنت ستستخدم المنصة فعلياً
> فابدأ بقاعدة نظيفة (`php database/install.php` بدون `--demo`) وأنشئ حسابك من
> صفحة `/register`.

### روابط مهمة

| الصفحة | الرابط |
|--------|--------|
| الموقع التعريفي | `/` |
| تسجيل الدخول | `/login` |
| إنشاء حساب جهة | `/register` |
| لوحة التحكم | `/dashboard` |
| لوحة الشرف العامة | `/e/andalus` |
| التسجيل الذاتي للطلاب | `/join/andalus` |
| بوابة الطالب | `/p/{رمز-المتابعة}` |
| دخول الطالب | `/student-login` |

(في XAMPP بمجلد فرعي، أضف `/noqati` قبل كل رابط.)

### التأكد أن كل شيء يعمل

```bash
php tests/run.php
```

يجب أن تظهر: **النتيجة: 40 ناجح · 0 فاشل**

### عند تحديث النسخة لاحقاً

```bash
git pull
php database/migrate.php      # يضيف أي أعمدة جديدة دون المساس ببياناتك
```

---

## حل المشكلات الشائعة

| المشكلة | السبب والحل |
|---------|--------------|
| **تعذّر الاتصال بقاعدة البيانات** | راجع `app/config.local.php`: اسم القاعدة والمستخدم وكلمة المرور. في XAMPP: `root` وكلمة مرور فارغة. تأكد أن MySQL يعمل من لوحة XAMPP. |
| **صفحة بيضاء أو خطأ 500** | فعّل عرض الأخطاء مؤقتاً بإضافة `'app' => ['debug' => true]` في `config.local.php`، ثم راجع الرسالة. |
| **كل الصفحات تعطي 404 عدا الرئيسية** | `mod_rewrite` غير مفعّل في Apache: افتح `C:\xampp\apache\conf\httpd.conf` وأزل `#` من سطر `LoadModule rewrite_module modules/mod_rewrite.so`، وتأكد أن `AllowOverride All` مضبوطة لمجلد `htdocs`، ثم أعد تشغيل Apache. |
| **الصفحة تظهر بدون تنسيق (CSS)** | قيمة `base_url` خاطئة. إذا كان الرابط `http://localhost/noqati/` فاجعلها `'/noqati'`، وإذا كان `http://localhost/` فاجعلها `''`. |
| **حروف عربية مشوّهة (؟؟؟)** | ترميز القاعدة ليس `utf8mb4`. أعد إنشاء القاعدة بترميز `utf8mb4_unicode_ci` ثم استورد `schema.sql` من جديد. |
| **`php` غير معروف في CMD** | استخدم المسار الكامل: `C:\xampp\php\php.exe database\install.php --demo` |
| **`Access denied for user`** | كلمة مرور MySQL غير صحيحة في ملف الإعدادات، أو المستخدم لا يملك صلاحية على القاعدة. |
| **التاريخ أو «نقاط اليوم» غير صحيحة** | اضبط `'timezone' => 'Asia/Riyadh'` في `config.local.php` (المنصة توحّد المنطقة الزمنية بين PHP وMySQL تلقائياً بعد ذلك). |
| **رسالة «لم يتم تهيئة قاعدة البيانات»** | لم تُنشأ الجداول بعد: نفّذ `php database/install.php --demo` أو استورد `database/schema.sql`. |
| **المنفذ 80 مشغول** | غيّر منفذ Apache من XAMPP (Config ← httpd.conf ← `Listen 8080`)، ويصبح الرابط `http://localhost:8080/noqati/`. |

---

## ملاحظة أمنية

مجلد `public` فقط هو الذي يجب أن يكون متاحاً للمتصفح. عند النقل لاستضافة حقيقية،
اجعل **Document Root** للنطاق هو مجلد `public`، ولا ترفع ملف `app/config.local.php`
إلى أي مستودع عام لأنه يحتوي كلمة مرور قاعدة البيانات.
