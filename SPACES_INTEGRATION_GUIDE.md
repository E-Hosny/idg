# 🚀 دليل دمج DigitalOcean Spaces في النظام

## 📋 نظرة عامة

تم دمج DigitalOcean Spaces بشكل كامل في النظام مع الحفاظ على الملفات القديمة المخزنة محلياً. النظام الآن يدعم:

- ✅ رفع جميع الملفات الجديدة إلى Spaces تلقائياً
- ✅ البحث الذكي عن الملفات (Spaces أولاً، ثم التخزين المحلي)
- ✅ عرض الملفات من أي مصدر بشكل شفاف
- ✅ نقل الملفات القديمة إلى Spaces عند الحاجة

---

## 🎯 المكونات الأساسية

### 1. FileService (`app/Services/FileService.php`)
الخدمة الرئيسية لإدارة جميع عمليات الملفات:

```php
// رفع ملف
$path = file_service()->upload($file, 'directory', 'filename.pdf');

// الحصول على URL
$url = file_service()->url($path);

// التحقق من وجود الملف
$exists = file_service()->exists($path);

// حذف ملف
file_service()->delete($path);

// نقل ملف من التخزين المحلي إلى Spaces
file_service()->migrateToSpaces($path);
```

### 2. Helper Functions (`app/Helpers/FileHelper.php`)
دوال مساعدة لتسهيل الاستخدام:

```php
// رفع ملف
$path = upload_file($file, 'directory', 'filename.pdf');

// الحصول على URL
$url = file_url($path);

// التحقق من وجود الملف
$exists = file_exists_anywhere($path);

// حذف ملف
delete_file_anywhere($path);

// تحميل ملف
$response = download_file_anywhere($path, 'download-name.pdf');

// عرض ملف في المتصفح
$response = serve_file($path);

// نقل ملف إلى Spaces
migrate_file_to_spaces($path);
```

### 3. Model Accessors
تم إضافة Accessors للـ Models للحصول على URLs تلقائياً:

**Certificate Model:**
```php
$certificate->uploaded_certificate_url; // URL من Spaces أو local
```

**TestRequest Model:**
```php
$testRequest->signed_document_url; // URL من Spaces أو local
```

---

## 📁 الملفات المحدثة

### Controllers
- ✅ `TestRequestController` - يرفع الملفات الموقعة إلى Spaces
- ✅ `CertificateController` - يرفع الشهادات إلى Spaces
- ✅ `PublicCertificateController` - يعرض الملفات من Spaces أو local

### Routes
- ✅ `/files/{filename}` - يعرض الملفات من Spaces أو local
- ✅ `/certificate-file/{filename}` - يعرض ملفات الشهادات

### Models
- ✅ `Certificate` - يحتوي على `uploaded_certificate_url` accessor
- ✅ `TestRequest` - يحتوي على `signed_document_url` accessor

---

## 🧪 الاختبار

### صفحة الاختبار
يمكنك الوصول لصفحة الاختبار الشاملة:

```
http://127.0.0.1:8000/test/files
```

الصفحة تتيح لك:
1. رفع ملفات تجريبية إلى Spaces
2. فحص حالة أي ملف (هل موجود في Spaces أم local)
3. اختبار FileService API

### API للاختبار
```bash
# اختبار شامل للنظام
GET http://127.0.0.1:8000/test/file-operations

# فحص حالة ملف معين
GET http://127.0.0.1:8000/test/check-file-status?path=certificates/certificate-123.pdf
```

---

## 🔄 نقل الملفات القديمة

### باستخدام Artisan Command
```bash
# نقل جميع الملفات (dry-run)
php artisan files:migrate-to-spaces --dry-run

# نقل جميع الملفات فعلياً
php artisan files:migrate-to-spaces

# نقل الشهادات فقط
php artisan files:migrate-to-spaces --type=certificates

# نقل ملفات Test Requests فقط
php artisan files:migrate-to-spaces --type=test-requests
```

### نقل يدوي
```php
// في Tinker أو Controller
$path = 'certificates/certificate-123.pdf';
$migrated = migrate_file_to_spaces($path);
```

---

## 📝 كيفية استخدام FileService في الكود

### في Controller
```php
use App\Services\FileService;

class MyController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('document');
        
        // رفع إلى Spaces
        $path = upload_file($file, 'documents', 'doc-' . time() . '.pdf');
        
        if (!$path) {
            return back()->withErrors(['error' => 'Failed to upload']);
        }
        
        // حفظ المسار في قاعدة البيانات
        MyModel::create(['file_path' => $path]);
        
        return back()->with('success', 'Uploaded successfully!');
    }
    
    public function download($id)
    {
        $model = MyModel::findOrFail($id);
        
        // تحميل من Spaces أو local
        $response = download_file_anywhere($model->file_path, 'document.pdf');
        
        if (!$response) {
            abort(404, 'File not found');
        }
        
        return $response;
    }
    
    public function view($id)
    {
        $model = MyModel::findOrFail($id);
        
        // عرض في المتصفح
        $response = serve_file($model->file_path);
        
        if (!$response) {
            abort(404, 'File not found');
        }
        
        return $response;
    }
}
```

### في Model
```php
class MyModel extends Model
{
    protected $appends = ['file_url'];
    
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return file_url($this->file_path);
        }
        return null;
    }
}
```

### في Blade View
```blade
@if($model->file_url)
    <a href="{{ $model->file_url }}" target="_blank">
        عرض الملف
    </a>
@endif
```

### في Vue/React Component
```javascript
// الـ Model accessor يرجع URL مباشرة
<a :href="model.file_url" target="_blank">
    View File
</a>
```

---

## ⚙️ الإعدادات المطلوبة

### ملف `.env`
```env
DO_SPACES_KEY=your_access_key
DO_SPACES_SECRET=your_secret_key
DO_SPACES_REGION=sgp1
DO_SPACES_BUCKET=idg-files
DO_SPACES_ENDPOINT=https://sgp1.digitaloceanspaces.com
```

### ملف `config/filesystems.php`
```php
'spaces' => [
    'driver' => 's3',
    'key' => env('DO_SPACES_KEY'),
    'secret' => env('DO_SPACES_SECRET'),
    'region' => env('DO_SPACES_REGION'),
    'bucket' => env('DO_SPACES_BUCKET'),
    'endpoint' => env('DO_SPACES_ENDPOINT'),
    'use_path_style_endpoint' => false,
    'visibility' => 'public',
    'throw' => true,
],
```

---

## 🔍 آلية البحث عن الملفات

عند طلب ملف، النظام يتبع الخطوات التالية:

1. **البحث في Spaces أولاً**: يتحقق من وجود الملف في DigitalOcean Spaces
2. **البحث في التخزين المحلي**: إذا لم يوجد في Spaces، يبحث في `storage/app/public`
3. **إرجاع 404**: إذا لم يوجد في أي مكان

هذا يضمن:
- ✅ عمل الملفات القديمة بدون مشاكل
- ✅ استخدام Spaces للملفات الجديدة
- ✅ إمكانية نقل الملفات تدريجياً

---

## 📊 معلومات الملف

للحصول على معلومات تفصيلية عن ملف:

```php
$info = file_service()->getFileInfo('certificates/cert-123.pdf');

// النتيجة:
[
    'path' => 'certificates/cert-123.pdf',
    'in_spaces' => true,
    'in_local' => false,
    'url' => 'https://idg-files.sgp1.digitaloceanspaces.com/certificates/cert-123.pdf',
    'size' => 245678,
]
```

---

## ⚠️ ملاحظات مهمة

1. **جميع الملفات الجديدة** تُرفع إلى Spaces تلقائياً
2. **الملفات القديمة** تبقى في التخزين المحلي حتى يتم نقلها يدوياً
3. **URLs** يتم إنشاؤها تلقائياً من Spaces أو local حسب موقع الملف
4. **الحذف** يحذف الملف من كلا الموقعين (Spaces + local) إن وُجد
5. **CORS** تأكد من إعدادات CORS في DigitalOcean Spaces للسماح بالوصول

---

## 🆘 استكشاف الأخطاء

### الملف لا يُرفع إلى Spaces
- تحقق من إعدادات `.env`
- تحقق من صلاحيات API Keys في DigitalOcean
- راجع logs: `storage/logs/laravel.log`

### الملف لا يظهر
- استخدم `/test/check-file-status` للتحقق من موقع الملف
- تأكد من أن المسار صحيح في قاعدة البيانات
- تحقق من CORS settings في Spaces

### خطأ 403 Forbidden
- تأكد من visibility الملف في Spaces
- تحقق من إعدادات Bucket permissions
- استخدم `serve_file()` بدلاً من الوصول المباشر

---

## 📞 الدعم

للمزيد من المعلومات أو المساعدة، راجع:
- [DigitalOcean Spaces Documentation](https://docs.digitalocean.com/products/spaces/)
- [Laravel File Storage](https://laravel.com/docs/filesystem)

---

تم التطوير بواسطة: **IDG System**  
التاريخ: **{{ now()->format('Y-m-d') }}**



