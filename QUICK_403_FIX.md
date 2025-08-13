# ⚡ حل سريع لخطأ 403 FORBIDDEN

## 🚨 المشكلة:
ملفات PDF تظهر خطأ 403 عند فتحها على السيرفر.

## ✅ الحل السريع (3 خطوات):

### 1. **إنشاء Storage Link على السيرفر**:
```bash
php artisan storage:link
```

### 2. **تحديث Permissions**:
```bash
chmod -R 755 storage/ public/storage/
chown -R www-data:www-data storage/ public/storage/
```

### 3. **مسح Cache**:
```bash
php artisan optimize:clear
```

## 🔧 الحل البديل (إذا استمرت المشكلة):

تم إنشاء route مخصص `/files/{filename}` يتجاوز مشكلة 403.

### **الآن QR codes ستوجه إلى**:
```
https://yourdomain.com/files/certificates/certificate-IDG-2025-009-1755088881.pdf
```

### **بدلاً من**:
```
https://yourdomain.com/storage/certificates/certificate-IDG-2025-009-1755088881.pdf
```

## 🧪 للاختبار:

1. **جرب الحل السريع أولاً**
2. **إذا استمرت المشكلة، QR codes ستستخدم Route الجديد تلقائياً**
3. **اختبار**: امسح QR code وأعد توجيهه

## ✅ النتيجة:
- ملفات PDF تفتح بدون خطأ 403
- QR codes تعمل بشكل طبيعي
- لا توجد مشاكل في permissions

**جرب الحل السريع أولاً! 🎯** 