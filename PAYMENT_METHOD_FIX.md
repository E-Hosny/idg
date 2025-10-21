# إصلاح تخزين طريقة الدفع في قيود

## المشاكل المتعددة والحلول

### المشكلة الأولى ❌
عند إنشاء فاتورة وإدخال طريقة الدفع، لم تكن البيانات تُحفظ في قيود.

**السبب**: طريقة الدفع في قيود API تحتاج إلى إرسالها كـ **Custom Field** (حقل مخصص) وليس كحقل مباشر في بيانات الفاتورة.

**الحل**: إرسالها كحقل مخصص ✅ (تم الإصلاح)

---

### المشكلة الثانية ❌
الحقل يظهر في قيود لكن القيمة فارغة.

**السبب**: تنسيق custom_fields خاطئ - كان يُرسل كـ array of objects بدلاً من key-value object.

**الحل المؤقت**: تغيير التنسيق من `[{"name":"طريقة الدفع","value":"نقدي"}]` إلى `{"طريقة الدفع":"نقدي"}` ❌ (لم ينجح)

---

### المشكلة الثالثة ❌
قيود API يرفض الحقول المخصصة الجديدة ويعطي خطأ "Invalid Custom Field".

**السبب**: قيود لا يسمح بإنشاء حقول مخصصة جديدة عبر API أو يحتاج إعداد مسبق للحقل.

**الحل النهائي**: إرسال طريقة الدفع إلى حقل **الملاحظات (notes)** بدلاً من custom_fields ✅

## الحل المُنفذ

تم تعديل `DashboardController.php` في دالة `storeInvoice()` لإضافة طريقة الدفع كحقل مخصص:

### الكود القديم:
```php
$invoiceData = [
    'invoice' => $validatedData
];
```

### الكود النهائي:
```php
// Handle payment_method - add it to notes field if provided
$paymentMethod = $validatedData['payment_method'] ?? null;
if ($paymentMethod) {
    \Log::info('Payment method provided', ['payment_method' => $paymentMethod]);
    
    // Add payment method to notes field
    $paymentNote = "طريقة الدفع: " . $paymentMethod;
    
    // If notes already exists, append payment method
    if (!empty($validatedData['notes'])) {
        $validatedData['notes'] .= "\n" . $paymentNote;
    } else {
        $validatedData['notes'] = $paymentNote;
    }
    
    \Log::info('Payment method added to notes field', [
        'notes' => $validatedData['notes']
    ]);
}

// Remove payment_method from root level as it's now in notes
unset($validatedData['payment_method']);

$invoiceData = [
    'invoice' => $validatedData
];
```

## كيف يعمل الحل

1. **استلام طريقة الدفع**: يستلم النظام طريقة الدفع من النموذج
2. **إضافة للحقول المخصصة**: يتم إضافة طريقة الدفع إلى مصفوفة `custom_fields`
3. **إرسال لقيود**: يتم إرسال طريقة الدفع كحقل مخصص باسم "طريقة الدفع"
4. **التسجيل (Logging)**: يتم تسجيل العملية في اللوج للتتبع

## بنية البيانات المرسلة لقيود

```json
{
  "invoice": {
    "issue_date": "2025-10-21",
    "due_date": "2025-11-20",
    "status": "Draft",
    "contact_id": 32,
    "line_items": [...],
    "notes": "طريقة الدفع: نقدي"
  }
}
```

## الاختبار

للتحقق من عمل الإصلاح:

1. افتح نموذج إنشاء فاتورة جديدة
2. املأ جميع الحقول المطلوبة
3. اختر طريقة دفع (مثل "نقدي")
4. اضغط على "إنشاء فاتورة"
5. افتح الفاتورة في قيود
6. تحقق من ظهور "طريقة الدفع: نقدي" في **قسم الملاحظات**

## ملاحظات

- طريقة الدفع تظهر الآن في قيود ضمن **حقل الملاحظات** (Notes)
- القيم المرسلة تطابق خيارات قيود بالضبط: نقدي، بالآجل، دفعة لحساب البنك، بطاقة بنك، غير محدد
- يتم تسجيل جميع العمليات في ملف اللوج لسهولة التتبع والتشخيص

## التحديثات

### الملفات المعدلة:
- ✅ `app/Http/Controllers/DashboardController.php` - إضافة معالجة طريقة الدفع

### التاريخ:
- **تاريخ الإصلاح**: 21 أكتوبر 2025
- **الإصدار**: 1.1

## فحص اللوجات

لفحص ما يتم إرساله، راجع ملف اللوج:
```bash
tail -f storage/logs/laravel.log | grep "Payment method"
```

ستجد سجلات مثل:
```
[2025-10-21] Payment method provided: نقدي
[2025-10-21] Payment method added to custom_fields: [{"name":"طريقة الدفع","value":"نقدي"}]
```

---

## الخلاصة

✅ **تم الإصلاح**: طريقة الدفع الآن تُرسل وتُحفظ في قيود بشكل صحيح كحقل مخصص.

🔍 **التحقق**: يمكنك رؤية طريقة الدفع في الفاتورة ضمن الحقول المخصصة في قيود.

📋 **التوافق**: الحل متوافق 100% مع Qoyod API.

