# 📋 ميزة الشهادات المرفوعة - التحديث الكامل

## 🎯 الهدف من التحديث:

1. **إظهار الشهادات المرفوعة في قائمة الشهادات المعتمدة**
2. **تغيير زر "Upload Certificate" إلى "Uploaded" بعد الرفع**
3. **إمكانية عرض وتحميل الشهادات المرفوعة**

## ✅ التحديثات المطبقة:

### 1. تحديث قاعدة البيانات
```sql
-- إضافة 'uploaded' إلى enum status في جدول certificates
ALTER TABLE certificates MODIFY status ENUM('draft', 'issued', 'cancelled', 'uploaded') DEFAULT 'draft';
```

### 2. تحديث Certificate Model
```php
// إضافة دعم لحالة 'uploaded'
public function getStatusColorAttribute()
{
    return match($this->status) {
        'draft' => 'yellow',
        'issued' => 'green',
        'uploaded' => 'blue',      // جديد
        'cancelled' => 'red',
        default => 'gray'
    };
}

// إضافة scope للشهادات المرفوعة
public function scopeUploaded($query)
{
    return $query->where('status', 'uploaded');
}
```

### 3. تحديث صفحة الشهادات المعتمدة
**الملف:** `resources/js/Pages/Dashboard/Certificates/Certified.vue`

```javascript
// إضافة منطق مختلف للشهادات المرفوعة مقابل المُنشأة
<!-- For uploaded certificates (user uploaded) -->
<template v-if="artifact.latest_certificate && artifact.latest_certificate.status === 'uploaded'">
  <!-- View Uploaded Certificate -->
  <button @click="viewUploadedCertificate(artifact.latest_certificate)">
    📋 View
  </button>
  
  <!-- Download Uploaded PDF -->
  <button @click="downloadUploadedPDF(artifact.latest_certificate)">
    📄 Download
  </button>

  <!-- Status Badge -->
  <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
    Uploaded
  </span>
</template>

// إضافة methods للتعامل مع الشهادات المرفوعة
viewUploadedCertificate(certificate) {
  if (certificate.uploaded_certificate_path) {
    const url = `/storage/${certificate.uploaded_certificate_path}`
    window.open(url, '_blank')
  }
},

downloadUploadedPDF(certificate) {
  if (certificate.uploaded_certificate_path) {
    const url = `/storage/${certificate.uploaded_certificate_path}`
    const link = document.createElement('a')
    link.href = url
    link.download = `uploaded-certificate-${certificate.certificate_number}.pdf`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}
```

### 4. تحديث صفحة Evaluated Artifacts
**الملف:** `resources/js/Pages/Dashboard/Artifacts/EvaluatedIndex.vue`

```javascript
// تغيير زر Upload Certificate حسب الحالة
<template v-if="hasUploadedCertificate(artifact)">
  <!-- Certificate Already Uploaded -->
  <button @click="viewUploadedCertificate(artifact)" 
          class="px-3 py-1 bg-green-600 text-white rounded">
    ✅ Uploaded
  </button>
</template>
<template v-else>
  <!-- Upload Certificate Button -->
  <button @click="showUploadModal(artifact)" 
          class="px-3 py-1 bg-indigo-600 text-white rounded">
    📄 Upload Certificate
  </button>
</template>

// إضافة methods للتحقق والعرض
hasUploadedCertificate(artifact) {
  return artifact.latest_certificate && artifact.latest_certificate.status === 'uploaded'
},

viewUploadedCertificate(artifact) {
  if (artifact.latest_certificate && artifact.latest_certificate.uploaded_certificate_path) {
    const url = `/storage/${artifact.latest_certificate.uploaded_certificate_path}`
    window.open(url, '_blank')
  }
}
```

### 5. تحديث Controllers

#### CertificateController.php
```php
// تحديث certified() method لتشمل الشهادات المرفوعة
public function certified()
{
    $artifacts = Artifact::with(['client', 'latestCertificate'])
        ->where('status', 'certified')
        ->whereHas('latestCertificate', function($query) {
            $query->whereIn('status', ['issued', 'uploaded']);
        })
        ->orderBy('updated_at', 'desc')
        ->paginate(15);
}

// تحديث uploadCertificate() method
public function uploadCertificate(Request $request, Artifact $artifact)
{
    // ... validation and upload logic ...
    
    // Update artifact status to certified if not already
    if ($artifact->status !== 'certified') {
        $artifact->update(['status' => 'certified']);
    }
}
```

#### DashboardController.php
```php
// تحديث evaluatedArtifacts() method لتضمين بيانات الشهادة
public function evaluatedArtifacts()
{
    $artifacts = Artifact::with(['client', 'category', 'latestCertificate'])
        ->whereIn('status', ['evaluated', 'certified'])
        ->orderBy('updated_at', 'desc')
        ->paginate(15);
}
```

## 🎯 النتائج:

### ✅ ما تم تحقيقه:

1. **قائمة الشهادات المعتمدة (`/certificates/certified/list`)**:
   - ✅ تُظهر الشهادات المرفوعة والمُنشأة معاً
   - ✅ أزرار مختلفة لكل نوع (عرض/تحميل للمرفوعة، عرض/طباعة/تحميل للمُنشأة)
   - ✅ badge أزرق للشهادات المرفوعة
   - ✅ إمكانية فتح الشهادة المرفوعة في نافذة جديدة
   - ✅ إمكانية تحميل الشهادة المرفوعة

2. **صفحة Evaluated Artifacts**:
   - ✅ تغيير زر "Upload Certificate" إلى "✅ Uploaded" بعد الرفع
   - ✅ إمكانية عرض الشهادة المرفوعة من نفس الصفحة
   - ✅ تحديث تلقائي للحالة بعد الرفع

3. **النظام العام**:
   - ✅ تحديث تلقائي لحالة الـ artifact إلى "certified"
   - ✅ دعم كامل للشهادات المرفوعة في النظام
   - ✅ عرض الشهادات حسب نوعها (مُنشأة/مرفوعة)

## 🧪 كيفية الاختبار:

### 1. اختبار رفع الشهادة:
1. اذهب إلى: `http://127.0.0.1:8000/dashboard/evaluated-artifacts`
2. اختر artifact واضغط "📄 Upload Certificate"
3. ارفع ملف PDF
4. يجب أن يتغير الزر إلى "✅ Uploaded"

### 2. اختبار قائمة الشهادات المعتمدة:
1. اذهب إلى: `http://127.0.0.1:8000/certificates/certified/list`
2. يجب أن تظهر الشهادة المرفوعة في القائمة
3. يجب أن ترى badge "مرفوعة" باللون الأزرق
4. اضغط "📋 View" لعرض الشهادة
5. اضغط "📄 Download" لتحميل الشهادة

### 3. اختبار العرض والتحميل:
1. من قائمة الشهادات المعتمدة أو Evaluated Artifacts
2. اضغط على زر العرض للشهادة المرفوعة
3. يجب أن تُفتح الشهادة في نافذة جديدة
4. جرب تحميل الشهادة

## 🎉 الخلاصة:

النظام الآن يدعم **بالكامل** الشهادات المرفوعة مع:
- ✅ **عرض في القائمة المناسبة**
- ✅ **تغيير حالة الزر بعد الرفع**
- ✅ **إمكانية العرض والتحميل**
- ✅ **تصنيف واضح بين المُنشأة والمرفوعة**
- ✅ **واجهة مستخدم محسنة**

الميزة جاهزة للاستخدام! 🚀 