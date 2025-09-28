# إعداد Qoyod API

## الخطوات المطلوبة:

### 1. إضافة متغيرات البيئة
أضف المتغيرات التالية إلى ملف `.env`:

```env
# Qoyod API Configuration
QOYOD_BASE_URL=https://api.qoyod.com/api/v1
QOYOD_API_KEY=your_qoyod_api_key_here
QOYOD_TIMEOUT=30
```

### 2. الحصول على API Key من Qoyod
1. سجل دخولك إلى حساب Qoyod
2. اذهب إلى الإعدادات > API
3. أنشئ API Key جديد
4. انسخ المفتاح وأضفه إلى ملف `.env`

### 3. اختبار الاتصال
بعد إضافة المفتاح، يمكنك اختبار الاتصال من خلال:
- الذهاب إلى صفحة العملاء في لوحة التحكم
- الضغط على زر "تحديث" لتحميل العملاء من Qoyod

## الميزات المتاحة:

### ✅ تم تنفيذها:
- عرض العملاء من Qoyod API
- إضافة عميل جديد
- حذف عميل
- البحث والفلترة
- دعم اللغة العربية والإنجليزية

### 🔄 قيد التطوير:
- تعديل العميل
- عرض تفاصيل العميل
- إدارة الفواتير

## استكشاف الأخطاء:

### خطأ "Failed to load customers from Qoyod"
1. تأكد من صحة API Key
2. تحقق من اتصال الإنترنت
3. راجع سجلات Laravel: `storage/logs/laravel.log`

### خطأ CORS
- تأكد من تشغيل خادم Vite: `npm run dev`
- تأكد من تشغيل خادم Laravel: `php artisan serve`

## الملفات المضافة/المعدلة:

### Backend:
- `app/Services/QoyodService.php` - خدمة Qoyod API
- `app/Http/Controllers/DashboardController.php` - إضافة دوال العملاء
- `config/services.php` - إعدادات Qoyod
- `routes/web.php` - مسارات العملاء

### Frontend:
- `resources/js/Layouts/DashboardLayout.vue` - إضافة قسم العملاء
- `resources/js/Pages/Dashboard/Customers/Index.vue` - صفحة العملاء

## الخطوات التالية:
1. إضافة إدارة الفواتير
2. ربط العملاء بالقطع المقيمة
3. إنشاء تقارير مالية
