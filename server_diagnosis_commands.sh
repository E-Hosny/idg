#!/bin/bash

echo "🔍 تشخيص شامل لمشكلة 403 على السيرفر"
echo "======================================"

echo ""
echo "📁 فحص الملفات المحدثة:"
echo "------------------------"
echo "1. فحص Certificate.php:"
if [ -f "app/Models/Certificate.php" ]; then
    echo "✅ app/Models/Certificate.php موجود"
    grep -n "certificate-file" app/Models/Certificate.php || echo "❌ لم يتم تحديث Certificate.php"
else
    echo "❌ app/Models/Certificate.php غير موجود"
fi

echo ""
echo "2. فحص PublicCertificateController.php:"
if [ -f "app/Http/Controllers/PublicCertificateController.php" ]; then
    echo "✅ PublicCertificateController.php موجود"
    grep -n "serveFile" app/Http/Controllers/PublicCertificateController.php || echo "❌ لم يتم تحديث PublicCertificateController.php"
else
    echo "❌ PublicCertificateController.php غير موجود"
fi

echo ""
echo "3. فحص routes/web.php:"
if [ -f "routes/web.php" ]; then
    echo "✅ routes/web.php موجود"
    grep -n "certificate-file" routes/web.php || echo "❌ لم يتم تحديث routes/web.php"
else
    echo "❌ routes/web.php غير موجود"
fi

echo ""
echo "4. فحص Seeder:"
if [ -f "database/seeders/UpdateQRCodeUrlsSeeder.php" ]; then
    echo "✅ UpdateQRCodeUrlsSeeder.php موجود"
else
    echo "❌ UpdateQRCodeUrlsSeeder.php غير موجود"
fi

echo ""
echo "🔧 فحص Laravel:"
echo "---------------"
echo "1. فحص Routes:"
php artisan route:list | grep -E "(certificate-file|test-pdf-file)" || echo "❌ Routes الجديدة غير موجودة"

echo ""
echo "2. فحص Cache:"
php artisan config:cache --help > /dev/null 2>&1 && echo "✅ Laravel يعمل" || echo "❌ Laravel لا يعمل"

echo ""
echo "📁 فحص الملفات:"
echo "---------------"
echo "1. فحص مجلد certificates:"
ls -la storage/app/public/certificates/ | head -5

echo ""
echo "2. فحص storage link:"
ls -la public/storage | head -3

echo ""
echo "🧪 اختبار Routes:"
echo "----------------"
echo "1. اختبار route الاختبار:"
curl -s -o /dev/null -w "%{http_code}" "https://app.idg-lab.com.sa/test-pdf-file/certificates/certificate-IDG-2025-002-1755105616.pdf" || echo "❌ فشل في الاختبار"

echo ""
echo "2. اختبار route الملفات:"
curl -s -o /dev/null -w "%{http_code}" "https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-002-1755105616.pdf" || echo "❌ فشل في الاختبار"

echo ""
echo "📋 التوصيات:"
echo "------------"
echo "1. إذا كانت الملفات غير موجودة: رفع الملفات المحدثة"
echo "2. إذا كانت Routes لا تعمل: مسح Cache"
echo "3. إذا كانت المشكلة مستمرة: فحص Laravel logs" 