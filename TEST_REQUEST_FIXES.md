# إصلاحات طلب الاختبار (Test Request Fixes)

## المشاكل التي تم إصلاحها

### 1. مشكلة تنسيق العربي في PDF ✅

#### المشكلة:
- النص العربي يظهر بشكل غير صحيح في ملف PDF
- الأحرف متقطعة أو معكوسة

#### الحل:
تم تحديث ملف `resources/views/test-request-pdf.blade.php`:

```html
<!-- قبل -->
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
    </style>
</head>

<!-- بعد -->
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
        }
    </style>
</head>
```

#### التغييرات:
1. ✅ تغيير اللغة من `en` إلى `ar`
2. ✅ تغيير الاتجاه من `ltr` إلى `rtl`
3. ✅ إضافة `direction: rtl` في CSS
4. ✅ استخدام خط DejaVu Sans الذي يدعم العربية بشكل كامل

---

### 2. مشكلة اللوجو لا يظهر في PDF ✅

#### المشكلة:
- اللوجو لا يظهر في header الـ PDF
- فقط نص "IDG" يظهر

#### الحل:
تم تحويل الصورة إلى Base64 وتضمينها في HTML:

```php
<td class="logo-cell" rowspan="3">
    @php
        $logoPath = public_path('images/idg_logo.jpg');
        $logoData = '';
        if (file_exists($logoPath)) {
            $imageData = base64_encode(file_get_contents($logoPath));
            $logoData = 'data:image/jpeg;base64,' . $imageData;
        }
    @endphp
    @if($logoData)
        <img src="{{ $logoData }}" alt="IDG Logo" style="width: 50px; height: 50px; margin: 0 auto; display: block;">
    @else
        <div style="font-size: 12pt; font-weight: bold;">IDG</div>
    @endif
</td>
```

#### كيف يعمل:
1. ✅ قراءة ملف الصورة من `public/images/idg_logo.jpg`
2. ✅ تحويل الصورة إلى Base64
3. ✅ تضمين الصورة مباشرة في HTML
4. ✅ fallback إلى نص "IDG" إذا لم تكن الصورة موجودة

---

### 3. مشكلة فشل رفع الملف (Upload Failed) ✅

#### المشكلة:
- عند محاولة رفع الملف الموقع، يظهر خطأ
- "Failed to upload signed document. Please try again."

#### الأسباب المحتملة:
1. مشكلة في CSRF token
2. طريقة إرسال FormData مع Inertia
3. عدم وجود JSON response من الـ backend

#### الحل:

**أ) تحديث Frontend (TestRequest.vue):**

```javascript
// قبل - استخدام Inertia مع FormData
this.$inertia.post(url, formData, {
  forceFormData: true,
  onError: (errors) => {
    alert('Failed to upload');
  }
});

// بعد - استخدام fetch مع CSRF token
async handleFileUpload(event) {
  const file = event.target.files[0];
  
  // Validation...
  
  this.uploadingFile = true;
  
  try {
    const formData = new FormData();
    formData.append('signed_document', file);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    // Upload using fetch
    const response = await fetch(`/dashboard/customers/${this.customer.qoyod_customer_id}/test-request/upload-signed`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: formData,
    });
    
    if (response.ok) {
      this.uploadingFile = false;
      this.$refs.fileInput.value = '';
      alert('تم رفع المستند الموقع بنجاح! | Signed document uploaded successfully!');
      this.$inertia.reload();
    } else {
      throw new Error('Upload failed');
    }
  } catch (error) {
    console.error('Upload error:', error);
    this.uploadingFile = false;
    alert('فشل رفع المستند. الرجاء المحاولة مرة أخرى.');
  }
}
```

**ب) تحديث Backend (TestRequestController.php):**

```php
public function uploadSignedDocument(Request $request, $customerId)
{
    try {
        \Log::info('Uploading signed document', [
            'customer_id' => $customerId,
            'has_file' => $request->hasFile('signed_document')
        ]);

        $request->validate([
            'signed_document' => 'required|file|mimes:pdf|max:10240',
        ]);

        // ... upload logic ...

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Signed document uploaded successfully.',
                'success' => true,
                'path' => $path
            ]);
        }

        return redirect()->back()->with('success', 'Signed document uploaded successfully.');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error', [
            'customer_id' => $customerId,
            'errors' => $e->errors()
        ]);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'success' => false
            ], 422);
        }

        return redirect()->back()->withErrors($e->errors());
        
    } catch (\Exception $e) {
        \Log::error('Error uploading', [
            'customer_id' => $customerId,
            'error' => $e->getMessage()
        ]);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Failed to upload: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }

        return redirect()->back()->withErrors(['error' => 'Failed to upload.']);
    }
}
```

#### التحسينات:
1. ✅ استخدام `fetch` بدلاً من Inertia لرفع الملفات
2. ✅ إضافة CSRF token بشكل صريح في headers
3. ✅ إرجاع JSON response من Backend
4. ✅ معالجة أفضل للأخطاء مع logging
5. ✅ رسائل خطأ بالعربية والإنجليزية
6. ✅ إعادة تحميل الصفحة بعد النجاح لإظهار التحديثات

---

## المجلدات والصلاحيات

### إنشاء المجلد للملفات الموقعة:

```bash
# إنشاء المجلد
mkdir -p storage/app/public/test-requests/signed

# تعيين الصلاحيات
chmod -R 775 storage/app/public/test-requests

# تعيين المالك (على السيرفر)
chown -R www-data:www-data storage/app/public/test-requests
```

### التأكد من Symbolic Link:

```bash
php artisan storage:link
```

هذا يُنشئ رابط بين:
- `storage/app/public` → `public/storage`

---

## الاختبار

### 1. اختبار تحميل PDF ✅
```
1. افتح صفحة Test Request
2. اضغط على "Download PDF"
3. تحقق من:
   - ظهور النص العربي بشكل صحيح
   - ظهور اللوجو في الهيدر
   - التنسيق العام للملف
```

### 2. اختبار رفع الملف ✅
```
1. وقع الملف على جهاز التوقيع
2. اضغط على "Upload Signed Document"
3. اختر الملف الموقع
4. تأكد من:
   - ظهور رسالة نجاح
   - تحديث حالة الطلب إلى "Signed"
   - حفظ الملف في storage/app/public/test-requests/signed/
```

### 3. اختبار عرض الطلبات الموقعة ✅
```
1. افتح صفحة Artifacts
2. اضغط على "Signed Test Requests"
3. تأكد من:
   - ظهور الطلب الموقع في القائمة
   - إمكانية تحميل الملف الموقع
   - عرض معلومات الطلب بشكل صحيح
```

---

## السجلات (Logs)

تم إضافة سجلات مفصلة لتتبع عملية الرفع:

```php
// عند بداية الرفع
\Log::info('Uploading signed document', [
    'customer_id' => $customerId,
    'has_file' => $request->hasFile('signed_document')
]);

// عند النجاح
\Log::info('Signed document uploaded successfully', [
    'test_request_id' => $testRequest->id,
    'path' => $path
]);

// عند الفشل
\Log::error('Error uploading signed document', [
    'customer_id' => $customerId,
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

### عرض السجلات:
```bash
# عرض آخر 50 سطر
tail -n 50 storage/logs/laravel.log

# متابعة السجلات في الوقت الفعلي
tail -f storage/logs/laravel.log
```

---

## استكشاف الأخطاء

### إذا استمرت مشكلة رفع الملف:

1. **تحقق من الصلاحيات:**
```bash
ls -la storage/app/public/test-requests/
```
يجب أن يكون الناتج:
```
drwxrwxr-x 3 www-data www-data 4096 ...
```

2. **تحقق من PHP settings:**
```bash
php -i | grep upload_max_filesize
php -i | grep post_max_size
```
يجب أن تكون:
- `upload_max_filesize = 10M` أو أكثر
- `post_max_size = 12M` أو أكثر

3. **تحقق من CSRF token:**
افتح console في المتصفح واكتب:
```javascript
document.querySelector('meta[name="csrf-token"]')?.content
```
يجب أن يرجع token

4. **تحقق من الـ logs:**
```bash
tail -n 100 storage/logs/laravel.log | grep -i "upload"
```

### إذا استمرت مشكلة PDF:

1. **تحقق من وجود الخط:**
```bash
ls -la storage/fonts/DejaVuSans.ttf
```

إذا لم يكن موجوداً، قم بتحميله:
```bash
# تحميل خط DejaVu Sans
mkdir -p storage/fonts
cd storage/fonts
wget https://github.com/dejavu-fonts/dejavu-fonts/releases/download/version_2_37/dejavu-fonts-ttf-2.37.zip
unzip dejavu-fonts-ttf-2.37.zip
cp dejavu-fonts-ttf-2.37/ttf/DejaVuSans.ttf .
```

2. **تحقق من وجود اللوجو:**
```bash
ls -la public/images/idg_logo.jpg
```

---

## الملفات المعدلة

### 1. resources/views/test-request-pdf.blade.php
- ✅ تغيير اللغة إلى العربية
- ✅ إضافة RTL direction
- ✅ تضمين اللوجو كـ Base64
- ✅ تحسين دعم الخطوط العربية

### 2. resources/js/Pages/Dashboard/Customers/TestRequest.vue
- ✅ تغيير طريقة الرفع من Inertia إلى fetch
- ✅ إضافة CSRF token
- ✅ معالجة أفضل للأخطاء
- ✅ رسائل بالعربية والإنجليزية

### 3. app/Http/Controllers/TestRequestController.php
- ✅ إضافة JSON response
- ✅ معالجة ValidationException بشكل منفصل
- ✅ إضافة logging مفصل
- ✅ دعم كل من AJAX وredirect

### 4. storage/app/public/test-requests/signed/
- ✅ إنشاء المجلد للملفات الموقعة
- ✅ تعيين الصلاحيات المناسبة

---

## ملخص الإصلاحات

| المشكلة | الحل | الحالة |
|---------|------|--------|
| تنسيق العربي في PDF | تغيير اللغة والاتجاه + خط DejaVu Sans | ✅ تم |
| اللوجو لا يظهر | تضمين الصورة كـ Base64 | ✅ تم |
| فشل رفع الملف | استخدام fetch + CSRF token + JSON response | ✅ تم |
| معالجة الأخطاء | إضافة try-catch + logging + رسائل واضحة | ✅ تم |

---

## التحديثات المستقبلية

### اقتراحات للتحسين:
1. إضافة progress bar لرفع الملف
2. معاينة الملف قبل الرفع
3. إمكانية حذف الملف الموقع
4. إشعار email عند رفع ملف موقع
5. تاريخ التوقيعات لنفس الطلب
6. تصدير قائمة الطلبات الموقعة كـ Excel

---

## الخلاصة

✅ **تم إصلاح جميع المشاكل بنجاح!**

الآن:
- ✅ PDF يُحمل بتنسيق عربي صحيح
- ✅ اللوجو يظهر في الهيدر
- ✅ رفع الملفات يعمل بشكل صحيح
- ✅ معالجة الأخطاء محسنة
- ✅ رسائل واضحة بالعربية والإنجليزية

الآلية جاهزة للاستخدام! 🎉

