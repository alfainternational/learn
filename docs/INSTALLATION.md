<div dir="rtl">

# دليل التثبيت

## المتطلبات الأساسية

### البرمجيات المطلوبة

| البرنامج | الإصدار المطلوب | التحقق |
|----------|-----------------|--------|
| PHP | >= 8.2 | `php -v` |
| Composer | >= 2.0 | `composer -V` |
| Node.js | >= 18 | `node -v` |
| npm | >= 9 | `npm -v` |

### إضافات PHP المطلوبة
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- SQLite3 أو MySQL
- Tokenizer
- XML

للتحقق:
```bash
php -m | grep -E "(bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|sqlite3|tokenizer|xml)"
```

---

## خطوات التثبيت

### 1. استنساخ المشروع

```bash
git clone <repository-url>
cd learn
```

### 2. تثبيت مكتبات PHP

```bash
composer install
```

### 3. تثبيت مكتبات JavaScript

```bash
npm install
```

### 4. إعداد ملف البيئة

```bash
cp .env.example .env
php artisan key:generate
```

---

## إعداد قاعدة البيانات

### الخيار 1: SQLite (للتطوير)

```bash
# إنشاء ملف قاعدة البيانات
touch database/database.sqlite
```

تأكد من الإعدادات في `.env`:
```env
DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
```

### الخيار 2: MySQL (للإنتاج)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khattit
DB_USERNAME=root
DB_PASSWORD=your_password
```

### تشغيل التهجيرات

```bash
php artisan migrate
```

### تشغيل البذور (Seeders)

```bash
# جميع البذور
php artisan db:seed

# أو بشكل منفصل
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=PlanTemplateSeeder
php artisan db:seed --class=MarketingCourseSeeder
```

---

## إعداد Gemini API

### 1. الحصول على مفتاح API

1. انتقل إلى [Google AI Studio](https://aistudio.google.com/)
2. سجّل الدخول بحساب Google
3. اذهب إلى **Get API key**
4. اضغط على **Create API key**
5. انسخ المفتاح

### 2. إضافة المفتاح للمشروع

في ملف `.env`:
```env
GEMINI_API_KEY=AIzaSy...your-key-here
```

### 3. التحقق من الإعداد

```bash
php artisan tinker
>>> app(\App\Services\AI\GeminiService::class)->test()
```

أو من لوحة الإدارة:
```
POST /api/v1/admin/settings/test-gemini
```

### إعدادات AI إضافية

في `config/ai.php`:
```php
return [
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => 'gemini-pro',
        'max_tokens' => 2048,
    ],
    'credits' => [
        'default' => 50,
        'premium' => 200,
    ],
];
```

---

## تشغيل المشروع

### بيئة التطوير

```bash
# تشغيل الخادم
php artisan serve

# في نافذة أخرى - تشغيل Vite
npm run dev
```

الوصول: `http://localhost:8000`

### بيئة الإنتاج

```bash
# بناء الملفات
npm run build

# تحسين Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## إعداد الصلاحيات (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## حسابات الاختبار

بعد تشغيل `AdminUserSeeder`:

| الدور | البريد | كلمة المرور |
|-------|--------|-------------|
| Admin | admin@khattit.com | password |
| User | user@example.com | password |

---

## حل المشاكل الشائعة

### خطأ: Class not found
```bash
composer dump-autoload
php artisan clear-compiled
```

### خطأ: Permission denied
```bash
chmod -R 775 storage bootstrap/cache
```

### خطأ: SQLite database not found
```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### خطأ: Vite manifest not found
```bash
npm run build
# أو في التطوير
npm run dev
```

### خطأ: Gemini API
- تأكد من صحة المفتاح
- تأكد من عدم تجاوز الحد اليومي
- تحقق من الاتصال بالإنترنت

---

## متغيرات البيئة الكاملة

```env
APP_NAME=خطط
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack

DB_CONNECTION=sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

GEMINI_API_KEY=your-api-key

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@khattit.com"
MAIL_FROM_NAME="${APP_NAME}"
```

</div>
