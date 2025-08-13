#!/bin/bash

echo "🚀 حل سريع لمشكلة 403 على السيرفر"
echo "=================================="

echo ""
echo "📁 الخطوة 1: فحص الملفات المحدثة"
echo "--------------------------------"

# فحص Certificate.php
if grep -q "certificate-file" app/Models/Certificate.php 2>/dev/null; then
    echo "✅ Certificate.php محدث"
else
    echo "❌ Certificate.php يحتاج تحديث"
    echo "   رفع app/Models/Certificate.php المحدث"
fi

# فحص PublicCertificateController.php
if grep -q "serveFile" app/Http/Controllers/PublicCertificateController.php 2>/dev/null; then
    echo "✅ PublicCertificateController.php محدث"
else
    echo "❌ PublicCertificateController.php يحتاج تحديث"
    echo "   رفع app/Http/Controllers/PublicCertificateController.php المحدث"
fi

# فحص routes/web.php
if grep -q "certificate-file" routes/web.php 2>/dev/null; then
    echo "✅ routes/web.php محدث"
else
    echo "❌ routes/web.php يحتاج تحديث"
    echo "   رفع routes/web.php المحدث"
fi

# فحص Seeder
if [ -f "database/seeders/UpdateQRCodeUrlsSeeder.php" ]; then
    echo "✅ UpdateQRCodeUrlsSeeder.php موجود"
else
    echo "❌ UpdateQRCodeUrlsSeeder.php غير موجود"
    echo "   رفع database/seeders/UpdateQRCodeUrlsSeeder.php"
fi

echo ""
echo "🔧 الخطوة 2: تنفيذ الحل"
echo "------------------------"

# تحديث QR codes
echo "📱 تحديث QR codes..."
php artisan db:seed --class=UpdateQRCodeUrlsSeeder

# مسح Cache
echo "🧹 مسح Cache..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🧪 الخطوة 3: اختبار الحل"
echo "------------------------"

# اختبار Routes
echo "1. اختبار route الاختبار:"
if curl -s -o /dev/null -w "%{http_code}" "https://app.idg-lab.com.sa/test-pdf-file/certificates/certificate-IDG-2025-002-1755105616.pdf" | grep -q "200"; then
    echo "✅ route الاختبار يعمل"
else
    echo "❌ route الاختبار لا يعمل"
fi

echo "2. اختبار route الملفات:"
if curl -s -o /dev/null -w "%{http_code}" "https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-002-1755105616.pdf" | grep -q "200"; then
    echo "✅ route الملفات يعمل"
else
    echo "❌ route الملفات لا يعمل"
fi

echo ""
echo "📋 النتيجة:"
echo "-----------"
echo "إذا كانت جميع Routes تعمل: المشكلة محلولة! 🎉"
echo "إذا كانت Routes لا تعمل: رفع الملفات المحدثة أولاً"
echo ""
echo "🔍 للتشخيص:"
echo "tail -f storage/logs/laravel.log" 