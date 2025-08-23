# 🔍 دليل تشخيص مشكلة التواريخ

## 🎯 المشكلة:
مازالت التواريخ لا تظهر تلقائياً عند التعديل على الفورم.

## 🔧 خطوات التشخيص:

### 1. **فحص Console في المتصفح**
افتح Developer Tools (F12) وانتقل إلى Console، ثم اذهب إلى صفحة تعديل التقييم. يجب أن ترى:

```javascript
// يجب أن ترى هذه الرسائل:
Evaluate props: {
  existingEvaluation: {...},
  test_date: "2024-01-15 14:30:25",
  test_date_type: "string"
}

formatDate input: 2024-01-15 14:30:25 string
formatDate output: 2024-01-15

Form initialized with values: {
  test_date: "2024-01-15",
  grader_date: "2024-01-15"
}
```

### 2. **فحص Laravel Logs**
تحقق من ملف `storage/logs/laravel.log` لرؤية البيانات المرسلة من Controller:

```bash
tail -f storage/logs/laravel.log
```

يجب أن ترى:
```
Edit evaluation data: {
  artifact_id: 49,
  artifact_type: "Colored Gemstones",
  evaluation_id: 123,
  test_date: "2024-01-15 14:30:25",
  test_date_type: "string"
}
```

### 3. **فحص Network Tab**
في Developer Tools، انتقل إلى Network tab وتأكد من أن البيانات تصل من Server.

### 4. **فحص Props في Vue**
أضف هذا الكود في بداية `<script setup>` للتأكد من البيانات:

```javascript
// Debug props
console.log('All props:', props);
console.log('existingEvaluation:', props.existingEvaluation);
console.log('test_date raw:', props.existingEvaluation?.test_date);
console.log('test_date type:', typeof props.existingEvaluation?.test_date);
```

## 🐛 المشاكل المحتملة:

### **المشكلة 1: البيانات لا تصل من Controller**
**الأعراض:**
- `existingEvaluation` هو `null` أو `undefined`
- لا توجد رسائل في Console

**الحل:**
تحقق من Controller:
```php
// في DashboardController.php
public function editEvaluation(Artifact $artifact)
{
    $evaluation = $artifact->evaluations()->latest()->first();
    
    // أضف هذا للتشخيص
    \Log::info('Evaluation found:', [
        'evaluation' => $evaluation ? $evaluation->toArray() : null,
        'test_date' => $evaluation?->test_date,
        'test_date_type' => gettype($evaluation?->test_date)
    ]);
    
    return Inertia::render('Dashboard/Artifacts/Evaluate', [
        'artifact' => $artifact->load('client'),
        'existingEvaluation' => $evaluation,
        'isEditing' => true,
    ]);
}
```

### **المشكلة 2: البيانات تصل لكن لا تظهر في النموذج**
**الأعراض:**
- `existingEvaluation` يحتوي على بيانات
- `formatDate` تعمل بشكل صحيح
- لكن النموذج لا يعرض التواريخ

**الحل:**
تحقق من أن النموذج يستخدم `v-model` بشكل صحيح:
```vue
<input type="date" v-model="form.test_date" class="input" />
```

### **المشكلة 3: مشكلة في Reactivity**
**الأعراض:**
- البيانات تظهر في Console لكن لا تظهر في النموذج
- تغيير البيانات لا يحدث النموذج

**الحل:**
استخدم `watch` و `onMounted`:
```javascript
// Watch for changes
watch(() => props.existingEvaluation, (newEvaluation) => {
  if (newEvaluation) {
    form.test_date = formatDate(newEvaluation.test_date);
    form.grader_date = formatDate(newEvaluation.grader_date);
  }
}, { immediate: true, deep: true });

// Debug on mount
onMounted(() => {
  console.log('Form values after mount:', {
    test_date: form.test_date,
    grader_date: form.grader_date
  });
});
```

## 🧪 اختبار سريع:

### **الخطوة 1: فتح صفحة تعديل التقييم**
اذهب إلى `/artifacts/{id}/edit-evaluation`

### **الخطوة 2: فتح Console**
اضغط F12 → Console

### **الخطوة 3: البحث عن الرسائل**
ابحث عن:
- `Evaluate props:`
- `formatDate input:`
- `Form initialized with values:`

### **الخطوة 4: فحص البيانات**
تأكد من أن `existingEvaluation` يحتوي على:
- `test_date`
- `grader_date`
- `retained_date`
- `checked_date`

## 📝 مثال على البيانات المتوقعة:

```javascript
// في Console يجب أن ترى:
existingEvaluation: {
  id: 123,
  test_date: "2024-01-15 14:30:25",
  grader_date: "2024-01-15 14:30:25",
  retained_date: "2024-01-15 14:30:25",
  checked_date: "2024-01-15 14:30:25"
}

// بعد formatDate:
form.test_date: "2024-01-15"
form.grader_date: "2024-01-15"
form.retained_date: "2024-01-15"
form.checked_date: "2024-01-15"
```

## 🚨 إذا لم تظهر أي رسائل:

1. **تحقق من أن JavaScript يعمل**
2. **تحقق من أن Vue DevTools مثبتة**
3. **تحقق من أن Console لا يحتوي على أخطاء**
4. **تحقق من أن الصفحة تحمل بشكل صحيح**

## 🔍 التشخيص النهائي:

إذا استمرت المشكلة، أرسل:
1. **لقطة شاشة من Console**
2. **لقطة شاشة من Network tab**
3. **محتوى Laravel logs**
4. **وصف دقيق لما تراه**

**هذا سيساعد في تحديد المشكلة بدقة!** 🎯
