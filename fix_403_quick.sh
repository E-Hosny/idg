#!/bin/bash

echo "🚀 بدء حل مشكلة 403 FORBIDDEN..."
echo "=================================="

# Step 1: تحديث QR Codes الموجودة
echo "📱 الخطوة 1: تحديث QR Codes الموجودة..."
php artisan db:seed --class=UpdateQRCodeUrlsSeeder

# Step 2: مسح Cache
echo "🧹 الخطوة 2: مسح Cache..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Step 3: اختبار الحل
echo "🧪 الخطوة 3: اختبار الحل..."
echo "اختبار route الجديد..."

# اختبار route للاختبار السريع
if curl -s "https://app.idg-lab.com.sa/test-pdf-file/certificates/certificate-IDG-2025-011-1755104963.pdf" > /dev/null; then
    echo "✅ Route الاختبار يعمل!"
else
    echo "⚠️ Route الاختبار لا يعمل - تحقق من Laravel logs"
fi

echo ""
echo "🎉 تم الانتهاء من الحل!"
echo ""
echo "📱 QR Codes الجديدة ستوجه إلى:"
echo "https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-011-1755104963.pdf"
echo ""
echo "🔍 للتشخيص:"
echo "tail -f storage/logs/laravel.log"
echo ""
echo "🧪 للاختبار:"
echo "curl -v \"https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-011-1755104963.pdf\"" 