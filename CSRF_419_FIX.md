# 🔧 إصلاح مشكلة 419 PAGE EXPIRED عند رفع الشهادات

## 🚨 المشكلة:
عند محاولة رفع شهادة (upload certificate) يظهر خطأ "419 PAGE EXPIRED"

## 🔍 الأسباب:
1. **انتهاء صلاحية CSRF token** - الجلسة انتهت
2. **مشكلة في إعدادات الجلسة** - خاصة مع `database` driver
3. **مشكلة في middleware** - HandleInertiaRequests

## ✅ الحلول المطبقة:

### 1. تغيير إعدادات الجلسة:
**الملف:** `config/session.php`
```php
// تغيير من database إلى file
'driver' => env('SESSION_DRIVER', 'file'),

// زيادة مدة الجلسة من 120 إلى 480 دقيقة
'lifetime' => (int) env('SESSION_LIFETIME', 480),

// تحسين تنظيف الجلسات
'lottery' => [1, 100],
```

### 2. تحسين كود JavaScript:
**الملف:** `resources/js/Pages/Dashboard/Artifacts/EvaluatedIndex.vue`
```javascript
// الحصول على CSRF token بطريقة آمنة
const csrfToken = this.$page.props.csrf_token || 
                 document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                 document.querySelector('input[name="_token"]')?.value

if (csrfToken) {
  formData.append('_token', csrfToken)
}

// معالجة خطأ 419
if (errors.status === 419 || errors.message?.includes('419')) {
  this.uploadError = 'انتهت صلاحية الجلسة. يرجى تحديث الصفحة والمحاولة مرة أخرى.'
  setTimeout(() => {
    this.$inertia.reload()
  }, 3000)
  return
}
```

### 3. إنشاء مجلد الجلسات:
```bash
mkdir -p storage/framework/sessions
chmod 755 storage/framework/sessions
```

### 4. مسح الكاش:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 📋 خطوات التطبيق:

### 1. إنشاء ملف `.env` (إذا لم يكن موجود):
```bash
cp .env.example .env
```

### 2. إضافة الإعدادات التالية في `.env`:
```env
SESSION_DRIVER=file
SESSION_LIFETIME=480
SESSION_DOMAIN=127.0.0.1
```

### 3. إنشاء مفتاح التطبيق:
```bash
php artisan key:generate
```

### 4. إعادة تشغيل الخادم:
```bash
# إيقاف الخادم الحالي (Ctrl+C)
# ثم تشغيله مرة أخرى
php artisan serve
```

## 🧪 الاختبار:

### بعد تطبيق الإصلاحات:
1. **اذهب إلى:** `http://127.0.0.1:8000/dashboard/evaluated-artifacts`
2. **اختر أي قطعة**
3. **اضغط "📄 Upload Certificate"**
4. **ارفع ملف PDF**
5. **النتيجة المتوقعة:** رفع ناجح بدون خطأ 419

## 🔍 إذا استمرت المشكلة:

### 1. تحقق من سجلات الخطأ:
```bash
tail -f storage/logs/laravel.log
```

### 2. تحقق من حالة الجلسات:
```bash
ls -la storage/framework/sessions/
```

### 3. تحقق من إعدادات PHP:
```bash
php -i | grep -i session
```

## 📊 النتائج المتوقعة:

| المشكلة | الحل | النتيجة |
|---------|------|---------|
| 419 PAGE EXPIRED | تغيير SESSION_DRIVER إلى file | ✅ تم إصلاحها |
| انتهاء الجلسة السريع | زيادة SESSION_LIFETIME إلى 480 | ✅ جلسة أطول |
| مشاكل CSRF | تحسين معالجة الأخطاء | ✅ معالجة أفضل |

## 🎯 ملاحظات مهمة:

1. **تأكد من وجود مجلد الجلسات** قبل تشغيل التطبيق
2. **أعد تشغيل الخادم** بعد تغيير الإعدادات
3. **امسح الكاش** إذا لم تعمل التغييرات
4. **تحقق من ملف `.env`** إذا لم يكن موجود 