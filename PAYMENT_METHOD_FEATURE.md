# إضافة حقل طريقة الدفع في الفواتير

## نظرة عامة
تم إضافة حقل "طريقة الدفع" (Payment Method) إلى نموذج إنشاء الفاتورة في النظام، مطابقاً لما هو موجود في قيود.

## التغييرات المنفذة

### 1. واجهة المستخدم (Vue.js)
**الملف**: `resources/js/Pages/Dashboard/Customers/CreateInvoice.vue`

#### التعديلات:
- ✅ إضافة حقل قائمة منسدلة لاختيار طريقة الدفع
- ✅ إضافة الحقل في نموذج البيانات (`form.payment_method`)
- ✅ إرسال البيانات إلى الخادم عند إنشاء الفاتورة
- ✅ إضافة الترجمات العربية والإنجليزية

#### خيارات طريقة الدفع المتاحة (مطابقة لقيود):
1. **نقدي** (Cash)
2. **بالآجل** (On Credit)
3. **دفعة لحساب البنك** (Bank Account Payment)
4. **بطاقة بنك** (Bank Card)
5. **غير محدد** (Unspecified)

### 2. المعالجة في الخادم (Laravel)
**الملف**: `app/Http/Controllers/DashboardController.php`

#### التعديلات:
- ✅ إضافة قاعدة التحقق (validation): `'payment_method' => 'nullable|string|max:100'`
- ✅ الحقل اختياري ولكن يُرسل إلى Qoyod API إذا تم تحديده

### 3. دمج مع Qoyod API
- ✅ الحقل يُرسل تلقائياً إلى Qoyod API ضمن بيانات الفاتورة
- ✅ متوافق مع معايير Qoyod API للفواتير

## كيفية الاستخدام

### للمستخدم:
1. انتقل إلى صفحة إنشاء فاتورة جديدة من خلال صفحة العميل
2. املأ جميع الحقول المطلوبة
3. اختر طريقة الدفع من القائمة المنسدلة (اختياري)
4. أكمل بقية النموذج واضغط "إنشاء فاتورة"

### موقع الحقل:
يظهر حقل "طريقة الدفع" في قسم "معلومات الفاتورة" بجانب حقل "الموقع"، وهو حقل اختياري.

## الترجمة

### العربية:
- **التسمية**: طريقة الدفع
- **القيم** (مطابقة لقيود):
  - نقدي
  - بالآجل
  - دفعة لحساب البنك
  - بطاقة بنك
  - غير محدد

### الإنجليزية:
- **Label**: Payment Method
- **Values**:
  - Cash
  - On Credit
  - Bank Account Payment
  - Bank Card
  - Unspecified

## ملاحظات تقنية

1. **نوع الحقل**: قائمة منسدلة (Select)
2. **الحقل اختياري**: لا يُطلب ملء الحقل لإنشاء الفاتورة
3. **القيمة المخزنة**: النص العربي المطابق لقيود (مثل "نقدي")
4. **التوافق**: متوافق تماماً مع Qoyod API - الخيارات مطابقة 100%

## الاختبار

للتأكد من عمل الميزة بشكل صحيح:

1. افتح نموذج إنشاء فاتورة جديدة
2. تحقق من ظهور حقل "طريقة الدفع"
3. اختر طريقة دفع من القائمة
4. أنشئ الفاتورة وتحقق من إرسال البيانات بنجاح
5. راجع الفاتورة في قيود للتأكد من ظهور طريقة الدفع

## التاريخ
- **تاريخ التنفيذ**: 21 أكتوبر 2025
- **الإصدار**: 1.0

---

## الملفات المعدلة

```
resources/js/Pages/Dashboard/Customers/CreateInvoice.vue
app/Http/Controllers/DashboardController.php
```

## كود المرجع

### في Vue Component:
```vue
<!-- Payment Method -->
<div class="lg:col-span-1">
  <label for="payment_method" class="block text-sm font-medium text-gray-700">
    {{ __('Payment Method') }}
  </label>
  <select
    id="payment_method"
    v-model="form.payment_method"
    :disabled="creating"
    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
  >
    <option value="">{{ __('Select Payment Method') }}</option>
    <option value="نقدي">{{ __('Cash') }}</option>
    <option value="بالآجل">{{ __('On Credit') }}</option>
    <option value="دفعة لحساب البنك">{{ __('Bank Account Payment') }}</option>
    <option value="بطاقة بنك">{{ __('Bank Card') }}</option>
    <option value="غير محدد">{{ __('Unspecified') }}</option>
  </select>
</div>
```

### في Controller:
```php
'payment_method' => 'nullable|string|max:100',
```

