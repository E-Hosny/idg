# 🚀 دمج DigitalOcean Spaces - دليل سريع

## 📌 استخدام سريع

### رفع ملف:
```php
$path = upload_file($file, 'directory', 'filename.pdf');
```

### الحصول على URL:
```php
$url = file_url($path);
```

### في Model:
```php
$certificate->uploaded_certificate_url
$testRequest->signed_document_url
```

---

## 🧪 الاختبار

افتح في المتصفح:
```
http://127.0.0.1:8000/test/files
```

---

## 🔄 نقل الملفات القديمة

```bash
# تجربة أولاً
php artisan files:migrate-to-spaces --dry-run

# نقل فعلي
php artisan files:migrate-to-spaces
```

---

## 📚 المزيد من التفاصيل

- **دليل شامل**: `SPACES_INTEGRATION_GUIDE.md`
- **ملخص كامل**: `SPACES_MIGRATION_SUMMARY.md`

---

## ⚙️ إعدادات .env

```env
DO_SPACES_KEY=your_key
DO_SPACES_SECRET=your_secret
DO_SPACES_REGION=sgp1
DO_SPACES_BUCKET=idg-files
DO_SPACES_ENDPOINT=https://sgp1.digitaloceanspaces.com
```

---

## ✅ تم بنجاح!

النظام الآن يدعم:
- ✅ رفع تلقائي إلى Spaces
- ✅ البحث الذكي (Spaces ثم local)
- ✅ توافق مع الملفات القديمة
- ✅ نقل سهل للملفات

**استمتع! 🎉**



