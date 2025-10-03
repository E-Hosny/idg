# إصلاح اللوجو في عرض السعر / Quote Logo Fix

## المشكلة / Problem
كان هناك خطأ `Undefined variable $logoDataUri` في ملف `print-view.blade.php` واللوجو لا يظهر.

## الحلول المُطبقة / Applied Solutions

### 1. تمرير متغير اللوجو / Passing Logo Variable
- أضيف `$logoDataUri` إلى دالة `printQuote()` في `DashboardController.php`
- يتم إنتاج البيانات المدمجة (base64) عند كل استدعاء

### 2. طرق متعددة لعرض اللوجو / Multiple Logo Display Methods
```php
@if($logoDataUri ?? false)
    <img class="company-logo" src="{{ $logoDataUri }}" alt="IDG Logo">
@elseif(file_exists(public_path('images/idg_logo.jpg')))
    <img class="company-logo" src="{{ public_path('images/idg_logo.jpg') }}" alt="IDG Logo">
@else
    <div class="company-logo-placeholder">
        <span style="font-size: 24px; color: #007bff;">IDG</span>
    </div>
@endif
```

### 3. استخدام طريق السايدبار / Sidebar Path Reference
تم استنساخ طريقة عرض اللوجو من السايدبار:
- `DashboardLayout.vue`: `:src="'/images/idg_logo.jpg'"`
- `dashboard.blade.php`: `{{ asset('images/idg-logo.png') }}`

### 4. نسخ اللوجو لمجلد Storage
تم نسخ اللوجو إلى `storage/app/public/logos/idg_logo.jpg` للوصول الأفضل لـ DomPDF

## النتيجة / Result
✅ اللوجو يظهر الآن بشكل صحيح في PDF  
✅ اتجاه الصفحة أصبح landscape  
✅ لا توجد أخطاء في المتغيرات  
✅ النظام يعمل بنفس جودة السايدبار  

## ملفات التعديل / Modified Files
- `app/Http/Controllers/DashboardController.php`
- `resources/views/quotes/professional-pdf.blade.php`
- `storage/app/public/logos/idg_logo.jpg` (نسخة جديدة)
