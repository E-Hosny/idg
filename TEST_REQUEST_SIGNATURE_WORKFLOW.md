# آلية توقيع طلبات الاختبار (Test Request Signature Workflow)

## نظرة عامة

تم تنفيذ آلية متكاملة لتمكين توقيع طلبات الاختبار إلكترونيًا من خلال:
1. تحميل طلب الاختبار كملف PDF
2. توقيع الملف باستخدام جهاز التوقيع الإلكتروني
3. رفع الملف الموقع مرة أخرى إلى النظام
4. عرض قائمة الطلبات الموقعة

---

## التغييرات التي تم إجراؤها

### 1. قاعدة البيانات

#### Migration: `add_signed_document_to_test_requests_table.php`
- **الموقع**: `database/migrations/2025_10_18_065537_add_signed_document_to_test_requests_table.php`
- **الهدف**: إضافة عمود جديد لتخزين مسار الملف الموقع
- **التغيير**: إضافة عمود `signed_document_path` من نوع `string` nullable

```php
$table->string('signed_document_path')->nullable()->after('notes');
```

#### Model: `TestRequest.php`
- **الموقع**: `app/Models/TestRequest.php`
- **التغيير**: إضافة `signed_document_path` إلى قائمة `$fillable`

---

### 2. الروتات (Routes)

#### الموقع: `routes/web.php`

تم إضافة 3 روتات جديدة:

1. **تحميل PDF**
   ```php
   Route::get('/dashboard/customers/{customer}/test-request/download-pdf', 
       [TestRequestController::class, 'downloadPdf'])
       ->name('dashboard.customers.test-request.download-pdf');
   ```

2. **رفع الملف الموقع**
   ```php
   Route::post('/dashboard/customers/{customer}/test-request/upload-signed', 
       [TestRequestController::class, 'uploadSignedDocument'])
       ->name('dashboard.customers.test-request.upload-signed');
   ```

3. **عرض قائمة الطلبات الموقعة**
   ```php
   Route::get('/dashboard/customers/{customer}/test-requests/signed', 
       [TestRequestController::class, 'listSignedRequests'])
       ->name('dashboard.customers.test-requests.signed');
   ```

---

### 3. Controller

#### الموقع: `app/Http/Controllers/TestRequestController.php`

تم إضافة 3 وظائف رئيسية:

#### أ) `downloadPdf($customerId)`
**الوظيفة**: تحميل طلب الاختبار كملف PDF

**الخطوات**:
1. جلب بيانات العميل من Qoyod
2. جلب بيانات طلب الاختبار
3. جلب القطع (Artifacts) المرتبطة
4. إنشاء PDF باستخدام DomPDF
5. تحميل الملف تلقائيًا

**المميزات**:
- حجم ورق A4 landscape
- دقة 150 DPI
- تصميم احترافي يطابق الصفحة المطبوعة

#### ب) `uploadSignedDocument(Request $request, $customerId)`
**الوظيفة**: رفع الملف الموقع

**التحقق**:
- نوع الملف: PDF فقط
- الحجم الأقصى: 10 ميجابايت

**الخطوات**:
1. التحقق من صحة الملف
2. حذف الملف الموقع القديم إن وجد
3. تخزين الملف الجديد في `storage/app/public/test-requests/signed/`
4. تحديث حالة الطلب إلى `signed`
5. حفظ مسار الملف في قاعدة البيانات

**اسم الملف**: `signed-test-request-{receiving_record_no}-{timestamp}.pdf`

#### ج) `listSignedRequests($customerId)`
**الوظيفة**: عرض قائمة جميع طلبات الاختبار الموقعة للعميل

**الخطوات**:
1. جلب بيانات العميل من Qoyod
2. جلب جميع الطلبات التي لديها `signed_document_path`
3. عرض الصفحة مع البيانات

---

### 4. View للـ PDF

#### الموقع: `resources/views/test-request-pdf.blade.php`

**الوصف**: قالب Blade لتوليد PDF من طلب الاختبار

**المميزات**:
- تصميم احترافي يطابق النموذج الأصلي
- جداول منظمة لعرض البيانات
- معلومات العميل كاملة
- قائمة القطع (Artifacts)
- قسم توثيق التسليم مع مساحات للتوقيع
- تنسيق A4 Landscape
- خطوط واضحة ومقروءة

**الأقسام**:
1. Header Table (معلومات المستند)
2. Customer Information (معلومات العميل)
3. Items Table (جدول القطع)
4. Delivery Documentation (توثيق التسليم)

---

### 5. صفحة طلب الاختبار

#### الموقع: `resources/js/Pages/Dashboard/Customers/TestRequest.vue`

#### التغييرات:

**أ) الأزرار الجديدة**:
1. **زر Download PDF** (أحمر)
   - يحمل الصفحة كملف PDF
   - أيقونة: `fa-file-pdf`
   
2. **زر Upload Signed Document** (بنفسجي)
   - يفتح نافذة اختيار ملف
   - يرفع الملف الموقع
   - أيقونة: `fa-upload`

**ب) الوظائف الجديدة**:

```javascript
downloadPdf() {
  // تحميل PDF مباشرة
  window.location.href = `/dashboard/customers/${this.customer.qoyod_customer_id}/test-request/download-pdf`;
}

triggerFileUpload() {
  // فتح نافذة اختيار الملف
  this.$refs.fileInput.click();
}

handleFileUpload(event) {
  // معالجة رفع الملف
  // التحقق من النوع والحجم
  // رفع باستخدام Inertia
}
```

**ج) حقل الملف المخفي**:
```html
<input 
  ref="fileInput" 
  type="file" 
  accept=".pdf" 
  @change="handleFileUpload" 
  style="display: none;"
/>
```

---

### 6. صفحة القطع (Artifacts)

#### الموقع: `resources/js/Pages/Dashboard/Customers/Artifacts.vue`

#### التغييرات:

**أ) تعديل الأزرار**:
1. **زر "Test Request"** → **"New Test Request"** (طلب اختبار جديد)
2. **زر جديد "Signed Test Requests"** (طلبات الاختبار) - أصفر
   - يعرض قائمة الطلبات الموقعة
   - أيقونة: `fa-file-signature`

**ب) الوظيفة الجديدة**:
```javascript
viewSignedTestRequests() {
  if (this.customer?.id) {
    this.$inertia.visit(`/dashboard/customers/${this.customer.id}/test-requests/signed`)
  }
}
```

**ج) الترجمات**:
- EN: "New Test Request" / "Signed Test Requests"
- AR: "طلب اختبار جديد" / "طلبات الاختبار"

---

### 7. صفحة الطلبات الموقعة

#### الموقع: `resources/js/Pages/Dashboard/Customers/SignedTestRequests.vue`

**الوصف**: صفحة جديدة لعرض قائمة جميع طلبات الاختبار الموقعة للعميل

#### المكونات:

**أ) معلومات العميل**:
- الرقم المرجعي
- اسم العميل
- المنشأة

**ب) جدول الطلبات الموقعة**:
الأعمدة:
1. Receiving Record No (رقم سجل الاستلام)
2. Received Date (تاريخ الاستلام)
3. Delivery Date (تاريخ التسليم)
4. Received By (تم الاستلام بواسطة)
5. Status (الحالة)
6. Uploaded Date (تاريخ الرفع)
7. Actions (الإجراءات)

**ج) الإجراءات المتاحة**:
1. **Download** (تحميل) - تحميل الملف الموقع
2. **View** (عرض) - عرض الطلب

**د) الحالات**:
- Pending (قيد الانتظار)
- Under Evaluation (قيد التقييم)
- Evaluated (تم التقييم)
- Certified (معتمد)
- **Signed (موقع)** - جديد
- Rejected (مرفوض)

**هـ) الرسائل**:
- رسالة عند عدم وجود طلبات موقعة
- رسائل النجاح والخطأ

---

## سير العمل (Workflow)

### 1. إنشاء طلب اختبار جديد
```
Customer Artifacts Page → "New Test Request" Button → Test Request Page
```

### 2. تحميل PDF
```
Test Request Page → "Download PDF" Button → PDF File Downloaded
```

### 3. التوقيع
```
PDF File → Digital Signature Device → Signed PDF File
```

### 4. رفع الملف الموقع
```
Test Request Page → "Upload Signed Document" Button → 
Select Signed PDF → Upload → Success Message
```

### 5. عرض الطلبات الموقعة
```
Customer Artifacts Page → "Signed Test Requests" Button → 
Signed Requests List → Download or View
```

---

## المسارات والصلاحيات

### المجلدات المستخدمة:
- **تخزين الملفات الموقعة**: `storage/app/public/test-requests/signed/`
- **الوصول العام**: `/storage/test-requests/signed/`

### الصلاحيات المطلوبة:
```bash
# تأكد من أن المجلد قابل للكتابة
chmod -R 775 storage/app/public/test-requests
chown -R www-data:www-data storage/app/public/test-requests
```

### Symbolic Link:
```bash
php artisan storage:link
```

---

## الترجمة

تم إضافة ترجمات باللغتين الإنجليزية والعربية لجميع العناصر الجديدة:

### الإنجليزية:
- New Test Request
- Signed Test Requests
- Download PDF
- Upload Signed Document
- Signed
- No Signed Requests Found

### العربية:
- طلب اختبار جديد
- طلبات الاختبار
- تحميل PDF
- رفع المستند الموقع
- موقع
- لا توجد طلبات موقعة

---

## التحقق من الأمان

### 1. التحقق من نوع الملف
```javascript
if (file.type !== 'application/pdf') {
  alert('Please select a PDF file.');
  return;
}
```

### 2. التحقق من حجم الملف
```javascript
if (file.size > 10 * 1024 * 1024) {
  alert('File size must be less than 10MB.');
  return;
}
```

### 3. التحقق من صلاحية المستخدم
- جميع الروتات محمية بـ middleware `auth`
- فقط المستخدمين المصرح لهم يمكنهم الوصول

### 4. Laravel Validation
```php
$request->validate([
    'signed_document' => 'required|file|mimes:pdf|max:10240',
]);
```

---

## ملاحظات مهمة

### 1. التخزين
- يتم تخزين الملفات الموقعة في `storage/app/public/test-requests/signed/`
- يتم حذف الملف القديم تلقائيًا عند رفع ملف جديد
- اسم الملف يتضمن `receiving_record_no` و `timestamp` لتجنب التكرار

### 2. الحالة
- عند رفع ملف موقع، يتم تحديث حالة الطلب تلقائيًا إلى `signed`
- يمكن تتبع جميع الطلبات الموقعة من خلال هذه الحالة

### 3. الأداء
- PDF يتم توليده مباشرة عند الطلب
- لا يتم تخزين PDF في الخادم
- الملف الموقع فقط يتم تخزينه

### 4. التوافق
- يعمل مع جميع أجهزة التوقيع الإلكتروني التي تنتج ملفات PDF
- يدعم المتصفحات الحديثة
- responsive design

---

## الاختبار

### 1. اختبار تحميل PDF
1. افتح صفحة Test Request
2. اضغط على "Download PDF"
3. تأكد من تحميل الملف بنجاح
4. تحقق من محتوى الملف

### 2. اختبار رفع الملف
1. وقع الملف باستخدام جهاز التوقيع
2. اضغط على "Upload Signed Document"
3. اختر الملف الموقع
4. تأكد من رفع الملف بنجاح
5. تحقق من تغيير الحالة إلى "Signed"

### 3. اختبار عرض القائمة
1. افتح صفحة Artifacts
2. اضغط على "Signed Test Requests"
3. تأكد من ظهور الطلبات الموقعة
4. جرب تحميل الملف الموقع

---

## استكشاف الأخطاء

### مشكلة: PDF لا يتم توليده
**الحل**:
```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### مشكلة: لا يمكن رفع الملف
**الحل**:
1. تحقق من صلاحيات المجلد
2. تأكد من وجود symbolic link
3. تحقق من إعدادات PHP:
   - `upload_max_filesize = 10M`
   - `post_max_size = 12M`

### مشكلة: لا يمكن تحميل الملف الموقع
**الحل**:
1. تأكد من وجود الملف في `storage/app/public/`
2. تحقق من صلاحيات القراءة
3. تأكد من عمل symbolic link

---

## التطوير المستقبلي

### اقتراحات للتحسين:
1. إضافة معاينة للملف قبل الرفع
2. إضافة إمكانية التوقيع المباشر من المتصفح
3. إرسال إشعار عند رفع ملف موقع
4. إضافة سجل لجميع التغييرات (Audit Log)
5. إضافة إمكانية تصدير قائمة الطلبات الموقعة
6. دعم ملفات متعددة لنفس الطلب

---

## الخلاصة

تم تنفيذ آلية متكاملة وآمنة لتوقيع طلبات الاختبار إلكترونيًا تشمل:
- ✅ تحميل PDF احترافي
- ✅ رفع آمن للملفات الموقعة
- ✅ عرض منظم للطلبات الموقعة
- ✅ واجهة مستخدم سهلة وواضحة
- ✅ دعم اللغتين العربية والإنجليزية
- ✅ أمان متقدم للملفات
- ✅ توثيق شامل

الآلية جاهزة للاستخدام مباشرة!

