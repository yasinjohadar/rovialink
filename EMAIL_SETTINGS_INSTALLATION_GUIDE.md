# دليل تثبيت إعدادات البريد الإلكتروني (SMTP) - المشروع الثاني

## 📋 نظرة عامة

هذا الدليل يوضح كيفية نقل وتثبيت نظام إعدادات البريد الإلكتروني (SMTP) من المشروع الأول إلى المشروع الثاني.

---

## ✅ قائمة الملفات المطلوبة

### 1. Models (النماذج)
- [ ] `app/Models/EmailSetting.php`

### 2. Controllers (المتحكمات)
- [ ] `app/Http/Controllers/Admin/EmailSettingController.php`

### 3. Migrations (قاعدة البيانات)
- [ ] `database/migrations/2025_11_27_150636_create_email_settings_table.php`
- [ ] `database/migrations/2025_11_27_152643_add_email_preferences_to_users_table.php` (اختياري - فقط إذا كنت تستخدم تفضيلات البريد)

### 4. Views (العروض)
- [ ] `resources/views/admin/pages/settings/email/index.blade.php`
- [ ] `resources/views/admin/pages/settings/email/create.blade.php`
- [ ] `resources/views/admin/pages/settings/email/edit.blade.php`

### 5. Service Providers
- [ ] `app/Providers/MailConfigServiceProvider.php`

### 6. Config Files
- [ ] `config/mail.php` (مراجعة فقط - موجود افتراضياً في Laravel)

---

## 📝 خطوات التثبيت التفصيلية

### الخطوة 1: نسخ الملفات الأساسية

#### 1.1 نسخ Model
```bash
# من المشروع الأول
app/Models/EmailSetting.php

# إلى المشروع الثاني
app/Models/EmailSetting.php
```

**التحقق:**
- [ ] تأكد من وجود namespace: `namespace App\Models;`
- [ ] تأكد من وجود use statements: `use Illuminate\Database\Eloquent\Model;` و `use Illuminate\Support\Facades\Crypt;`

#### 1.2 نسخ Controller
```bash
# من المشروع الأول
app/Http/Controllers/Admin/EmailSettingController.php

# إلى المشروع الثاني
app/Http/Controllers/Admin/EmailSettingController.php
```

**التحقق:**
- [ ] تأكد من وجود namespace: `namespace App\Http\Controllers\Admin;`
- [ ] تأكد من وجود use statements:
  - `use App\Http\Controllers\Controller;`
  - `use App\Models\EmailSetting;`
  - `use Illuminate\Http\Request;`
  - `use Illuminate\Support\Facades\Mail;`
  - `use Illuminate\Support\Facades\Log;`

#### 1.3 نسخ Migrations
```bash
# من المشروع الأول
database/migrations/2025_11_27_150636_create_email_settings_table.php
database/migrations/2025_11_27_152643_add_email_preferences_to_users_table.php

# إلى المشروع الثاني
database/migrations/2025_11_27_150636_create_email_settings_table.php
database/migrations/2025_11_27_152643_add_email_preferences_to_users_table.php
```

**ملاحظة مهمة:** قد تحتاج لتغيير التاريخ في اسم الملف إذا كان هناك migrations أخرى بنفس التاريخ.

**التحقق:**
- [ ] تأكد من أن أسماء الجداول صحيحة: `email_settings` و `users`
- [ ] تأكد من أن Foreign Keys صحيحة (إن وجدت)

#### 1.4 نسخ Views
```bash
# إنشاء المجلد إذا لم يكن موجوداً
mkdir -p resources/views/admin/pages/settings/email

# نسخ الملفات
resources/views/admin/pages/settings/email/index.blade.php
resources/views/admin/pages/settings/email/create.blade.php
resources/views/admin/pages/settings/email/edit.blade.php
```

**التحقق:**
- [ ] تأكد من أن جميع الملفات تستخدم `@extends('admin.layouts.master')`
- [ ] تأكد من أن Routes المستخدمة موجودة (سيتم إضافتها لاحقاً)

#### 1.5 نسخ Service Provider
```bash
# من المشروع الأول
app/Providers/MailConfigServiceProvider.php

# إلى المشروع الثاني
app/Providers/MailConfigServiceProvider.php
```

**التحقق:**
- [ ] تأكد من وجود namespace: `namespace App\Providers;`
- [ ] تأكد من وجود use statements:
  - `use App\Models\EmailSetting;`
  - `use Illuminate\Support\ServiceProvider;`
  - `use Illuminate\Support\Facades\Config;`

---

### الخطوة 2: تسجيل Service Provider

#### 2.1 فتح ملف `bootstrap/providers.php`

#### 2.2 إضافة Service Provider
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\MailConfigServiceProvider::class,  // ← أضف هذا السطر
];
```

**التحقق:**
- [ ] تأكد من وجود السطر في الملف
- [ ] تأكد من عدم وجود أخطاء syntax

---

### الخطوة 3: إضافة Routes

#### 3.1 فتح ملف `routes/admin.php`

#### 3.2 البحث عن قسم Settings أو إنشاء قسم جديد

#### 3.3 إضافة Routes التالية:
```php
// ========== Email Settings Routes ==========
Route::prefix('settings/email')->name('admin.settings.email.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\EmailSettingController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\EmailSettingController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\EmailSettingController::class, 'store'])->name('store');
    Route::post('/test-temp', [\App\Http\Controllers\Admin\EmailSettingController::class, 'testTemp'])->name('test-temp');
    Route::get('/{emailSetting}/edit', [\App\Http\Controllers\Admin\EmailSettingController::class, 'edit'])->name('edit');
    Route::put('/{emailSetting}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'update'])->name('update');
    Route::delete('/{emailSetting}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'destroy'])->name('destroy');
    Route::post('/{emailSetting}/activate', [\App\Http\Controllers\Admin\EmailSettingController::class, 'activate'])->name('activate');
    Route::post('/{emailSetting}/test', [\App\Http\Controllers\Admin\EmailSettingController::class, 'test'])->name('test');
    Route::get('/provider/{provider}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'getProviderPreset'])->name('provider.preset');
});
```

**التحقق:**
- [ ] تأكد من وجود use statement في أعلى الملف:
  ```php
  use App\Http\Controllers\Admin\EmailSettingController;
  ```
- [ ] أو استخدم الاسم الكامل في Routes كما هو موضح أعلاه

---

### الخطوة 4: إضافة رابط في Sidebar

#### 4.1 فتح ملف `resources/views/admin/layouts/main-sidebar.blade.php`

#### 4.2 البحث عن قسم Settings أو قسم مناسب آخر

#### 4.3 إضافة الكود التالي:
```php
<!-- إعدادات البريد -->
<li class="slide has-sub {{ request()->routeIs('admin.settings.email.*') ? 'open active' : '' }}">
    <a href="javascript:void(0);" class="side-menu__item">
        <i class="ri-mail-settings-line side-menu__icon"></i>
        <span class="side-menu__label">إعدادات البريد</span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide {{ request()->routeIs('admin.settings.email.index') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.email.index') }}" class="side-menu__item {{ request()->routeIs('admin.settings.email.index') ? 'active' : '' }}">جميع الإعدادات</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.settings.email.create') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.email.create') }}" class="side-menu__item {{ request()->routeIs('admin.settings.email.create') ? 'active' : '' }}">إضافة إعدادات</a>
        </li>
    </ul>
</li>
```

**التحقق:**
- [ ] تأكد من أن الأيقونة `ri-mail-settings-line` موجودة في المشروع (RemixIcon)
- [ ] إذا لم تكن موجودة، استبدلها بأيقونة أخرى مثل `fas fa-envelope` أو `ri-mail-line`

---

### الخطوة 5: تشغيل Migrations

#### 5.1 التأكد من اتصال قاعدة البيانات
```bash
php artisan migrate:status
```

#### 5.2 تشغيل Migration الأولى (إلزامي)
```bash
php artisan migrate --path=database/migrations/2025_11_27_150636_create_email_settings_table.php
```

#### 5.3 تشغيل Migration الثانية (اختياري - فقط إذا كنت تستخدم تفضيلات البريد)
```bash
php artisan migrate --path=database/migrations/2025_11_27_152643_add_email_preferences_to_users_table.php
```

**التحقق:**
- [ ] تأكد من إنشاء جدول `email_settings` بنجاح
- [ ] تأكد من عدم وجود أخطاء في Migration

---

### الخطوة 6: التحقق من Dependencies

#### 6.1 التحقق من Facades المستخدمة
تأكد من أن المشروع يحتوي على:
- ✅ `Illuminate\Support\Facades\Crypt` (موجود افتراضياً)
- ✅ `Illuminate\Support\Facades\Mail` (موجود افتراضياً)
- ✅ `Illuminate\Support\Facades\Log` (موجود افتراضياً)
- ✅ `Illuminate\Support\Facades\Config` (موجود افتراضياً)

#### 6.2 التحقق من APP_KEY
```bash
# تأكد من وجود APP_KEY في .env
php artisan key:generate  # إذا لم يكن موجوداً
```

**مهم جداً:** APP_KEY ضروري لتشفير كلمات مرور SMTP.

---

### الخطوة 7: التحقق من Config

#### 7.1 فتح `config/mail.php`

#### 7.2 التأكد من وجود إعدادات SMTP الأساسية:
```php
'mailers' => [
    'smtp' => [
        'transport' => 'smtp',
        'host' => env('MAIL_HOST', '127.0.0.1'),
        'port' => env('MAIL_PORT', 2525),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        // ...
    ],
],
```

**ملاحظة:** هذا الملف موجود افتراضياً في Laravel، لكن تأكد من وجوده.

---

## 🧪 الاختبار

### اختبار 1: التحقق من Routes
```bash
php artisan route:list | grep email
```

**النتيجة المتوقعة:** يجب أن ترى جميع routes التالية:
- `admin.settings.email.index`
- `admin.settings.email.create`
- `admin.settings.email.store`
- `admin.settings.email.edit`
- `admin.settings.email.update`
- `admin.settings.email.destroy`
- `admin.settings.email.activate`
- `admin.settings.email.test`
- `admin.settings.email.test-temp`
- `admin.settings.email.provider.preset`

### اختبار 2: الوصول للصفحة
1. سجل دخول كـ Admin
2. اذهب إلى: `/admin/settings/email`
3. يجب أن ترى صفحة "إعدادات البريد الإلكتروني"

### اختبار 3: إنشاء إعداد SMTP
1. اضغط على "إضافة إعدادات جديدة"
2. املأ الحقول:
   - Provider: Gmail (أو أي provider آخر)
   - Host: smtp.gmail.com
   - Port: 587
   - Username: your-email@gmail.com
   - Password: your-app-password
   - Encryption: TLS
   - From Address: your-email@gmail.com
   - From Name: Your Name
3. احفظ الإعدادات

### اختبار 4: اختبار الإعدادات
1. من صفحة قائمة الإعدادات، اضغط على "اختبار"
2. أدخل بريد إلكتروني للاختبار
3. اضغط "إرسال بريد اختباري"
4. تحقق من وصول البريد

### اختبار 5: تفعيل الإعدادات
1. اضغط على "تفعيل" بجانب الإعداد
2. يجب أن يصبح الإعداد نشطاً
3. يجب أن يتم تطبيق الإعدادات تلقائياً على `config/mail`

---

## ⚠️ المشاكل الشائعة وحلولها

### مشكلة 1: خطأ "Class not found"
**السبب:** Service Provider غير مسجل
**الحل:** تأكد من إضافة `MailConfigServiceProvider` في `bootstrap/providers.php`

### مشكلة 2: خطأ "Route not found"
**السبب:** Routes غير مضافة
**الحل:** تأكد من إضافة جميع Routes في `routes/admin.php`

### مشكلة 3: خطأ "Table doesn't exist"
**السبب:** Migration لم يتم تشغيلها
**الحل:** شغل `php artisan migrate`

### مشكلة 4: خطأ في فك التشفير
**السبب:** APP_KEY مختلف بين المشروعين
**الحل:** 
- إذا كنت تنقل بيانات موجودة: استخدم نفس APP_KEY
- إذا كنت تبدأ من جديد: أنشئ إعدادات جديدة

### مشكلة 5: الأيقونة لا تظهر
**السبب:** RemixIcon غير موجود
**الحل:** استبدل `ri-mail-settings-line` بأيقونة موجودة مثل `fas fa-envelope`

### مشكلة 6: خطأ 500 عند الوصول للصفحة
**السبب:** قد يكون خطأ في View أو Controller
**الحل:**
1. تحقق من `storage/logs/laravel.log`
2. تأكد من أن جميع use statements صحيحة
3. تأكد من أن Routes موجودة

---

## 📋 Checklist النهائي

### قبل البدء:
- [ ] المشروع الثاني يعمل بشكل صحيح
- [ ] قاعدة البيانات متصلة
- [ ] APP_KEY موجود في .env

### بعد النسخ:
- [ ] جميع الملفات موجودة في المسارات الصحيحة
- [ ] Service Provider مسجل في `bootstrap/providers.php`
- [ ] Routes مضافة في `routes/admin.php`
- [ ] Sidebar link مضافة في `main-sidebar.blade.php`

### بعد Migrations:
- [ ] جدول `email_settings` موجود
- [ ] Migration الثانية تم تشغيلها (إن كنت تحتاجها)

### بعد الاختبار:
- [ ] يمكن الوصول لصفحة الإعدادات
- [ ] يمكن إنشاء إعداد جديد
- [ ] يمكن اختبار الإعدادات
- [ ] يمكن تفعيل الإعدادات
- [ ] البريد يصل بنجاح

---

## 🔐 ملاحظات الأمان

1. **APP_KEY:** يجب أن يكون موجوداً وفريداً لكل مشروع
2. **كلمات المرور:** مشفرة في قاعدة البيانات باستخدام Laravel Crypt
3. **البيانات المنقولة:** إذا كنت تنقل بيانات موجودة، استخدم نفس APP_KEY

---

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من `storage/logs/laravel.log`
2. تحقق من أن جميع الملفات موجودة
3. تحقق من أن Routes مسجلة: `php artisan route:list | grep email`
4. تحقق من أن Service Provider مسجل: `php artisan config:clear`

---

## ✅ النجاح!

إذا أكملت جميع الخطوات بنجاح، يجب أن تتمكن من:
- ✅ الوصول لصفحة إعدادات البريد
- ✅ إنشاء إعدادات SMTP جديدة
- ✅ اختبار الإعدادات
- ✅ تفعيل الإعدادات
- ✅ إرسال بريد إلكتروني بنجاح
