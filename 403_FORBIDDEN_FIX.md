# 🚫 إصلاح خطأ 403 FORBIDDEN عند فتح ملفات الشهادات

## 🚨 المشكلة:
عند محاولة فتح ملف PDF تم رفعه على السيرفر، يظهر خطأ 403 FORBIDDEN.

## 🔍 سبب المشكلة:

### 1. **Storage Link غير موجود**:
```bash
# يجب إنشاء storage link
php artisan storage:link
```

### 2. **Permissions غير صحيحة**:
```bash
# على السيرفر
chmod -R 755 storage/
chmod -R 755 public/storage/
```

### 3. **ملفات Storage محمية** من الوصول المباشر

## ✅ الحلول المطبقة:

### 1. **تحديث .htaccess الرئيسي**:
```apache
# Allow direct access to storage files
RewriteCond %{REQUEST_URI} ^/storage/
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^(.*)$ $1 [L]
```

### 2. **إنشاء .htaccess في مجلد storage**:
```apache
# Allow access to PDF files in storage
<FilesMatch "\.(pdf|png|jpg|jpeg|gif|svg)$">
    Require all granted
    Order allow,deny
    Allow from all
</FilesMatch>
```

### 3. **تحديث URL generation**:
```php
// من
$certificateFileUrl = asset('storage/' . $certificate->uploaded_certificate_path);

// إلى
$certificateFileUrl = url('storage/' . $certificate->uploaded_certificate_path);
```

## 🛠️ خطوات الإصلاح على السيرفر:

### الخطوة 1: إنشاء Storage Link
```bash
# على السيرفر
php artisan storage:link
```

### الخطوة 2: تحديث Permissions
```bash
# على السيرفر
chmod -R 755 storage/
chmod -R 755 public/storage/
chown -R www-data:www-data storage/
chown -R www-data:www-data public/storage/
```

### الخطوة 3: مسح Cache
```bash
# على السيرفر
php artisan optimize:clear
php artisan config:clear
```

### الخطوة 4: اختبار الوصول
```bash
# اختبار الوصول للملف مباشرة
curl -I "https://yourdomain.com/storage/certificates/certificate-IDG-2025-009-1755088881.pdf"
```

## 🔧 حلول إضافية:

### 1. **إنشاء Route مخصص للوصول للملفات**:
```php
// في routes/web.php
Route::get('/files/{filename}', function($filename) {
    $path = storage_path('app/public/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->name('files.show');
```

### 2. **استخدام Controller method بدلاً من الوصول المباشر**:
```php
// في PublicCertificateController
public function showCertificate($token)
{
    $artifact = Artifact::where('qr_token', $token)->first();
    $certificate = $artifact->certificates()->where('status', 'uploaded')->latest()->first();
    
    if ($certificate && $certificate->uploaded_certificate_path) {
        $filePath = storage_path('app/public/' . $certificate->uploaded_certificate_path);
        return response()->file($filePath);
    }
    
    abort(404);
}
```

### 3. **تحديث URL في Frontend**:
```javascript
// بدلاً من asset('storage/...')
// استخدم route مخصص
const certificateUrl = route('files.show', { filename: certificate.uploaded_certificate_path });
```

## 🧪 اختبار الحل:

### 1. **اختبار Storage Link**:
```bash
# على السيرفر
ls -la public/storage
# يجب أن يظهر كـ symbolic link
```

### 2. **اختبار Permissions**:
```bash
# على السيرفر
ls -la storage/app/public/
ls -la public/storage/
```

### 3. **اختبار الوصول للملف**:
```bash
# على السيرفر
curl -I "https://yourdomain.com/storage/certificates/certificate-IDG-2025-009-1755088881.pdf"
```

## 📋 قائمة التحقق:

- [ ] Storage Link موجود
- [ ] Permissions صحيحة
- [ ] .htaccess محدث
- [ ] Cache مُمسح
- [ ] الملف موجود في المسار الصحيح
- [ ] URL generation صحيح

## 🚀 الأوامر السريعة:

```bash
# إنشاء Storage Link
php artisan storage:link

# تحديث Permissions
chmod -R 755 storage/ public/storage/

# مسح Cache
php artisan optimize:clear

# اختبار Route
php artisan route:list | grep storage
```

## ✅ النتيجة المتوقعة:

بعد تطبيق الحلول:
- ✅ ملفات PDF تفتح بدون خطأ 403
- ✅ QR codes تعمل وتوجه للملفات
- ✅ الوصول للملفات يعمل بشكل طبيعي
- ✅ لا توجد مشاكل في permissions

## 📞 إذا استمرت المشكلة:

1. **فحص Server Logs** للحصول على تفاصيل الخطأ
2. **مقارنة Server Environment** مع البيئة المحلية
3. **اختبار Route مخصص** بدلاً من الوصول المباشر
4. **فحص Server Configuration** و Apache/Nginx settings

**المشكلة يجب أن تُحل بهذه الطرق! 🎉** 