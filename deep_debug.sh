#!/bin/bash

echo "🔍 تشخيص عميق لمشكلة 403"
echo "========================="

echo ""
echo "📁 الخطوة 1: فحص Laravel logs عند الطلب الخارجي"
echo "-----------------------------------------------"
echo "في terminal منفصل، شغل:"
echo "tail -f storage/logs/laravel.log"
echo ""
echo "ثم في terminal آخر، شغل:"
echo "curl -v \"https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-009-1755088881.pdf\""
echo ""
echo "اضغط Enter عندما تنتهي..."
read

echo ""
echo "🔍 الخطوة 2: فحص Nginx logs"
echo "----------------------------"
echo "آخر 10 أسطر من Nginx access logs:"
tail -10 /var/log/nginx/access.log 2>/dev/null || echo "لا يمكن الوصول لـ Nginx access logs"

echo ""
echo "آخر 10 أسطر من Nginx error logs:"
tail -10 /var/log/nginx/error.log 2>/dev/null || echo "لا يمكن الوصول لـ Nginx error logs"

echo ""
echo "🧪 الخطوة 3: اختبار route بسيط"
echo "-----------------------------"
echo "اختبار route بسيط:"
curl -s -o /dev/null -w "Route بسيط: %{http_code}\n" "https://app.idg-lab.com.sa/test-simple"

echo ""
echo "🧪 الخطوة 4: اختبار index.php مباشرة"
echo "-----------------------------------"
echo "اختبار index.php:"
curl -s -o /dev/null -w "index.php: %{http_code}\n" "https://app.idg-lab.com.sa/index.php"

echo ""
echo "🔍 الخطوة 5: فحص Laravel routes"
echo "-------------------------------"
echo "فحص routes موجودة:"
php artisan route:list | grep -E "(certificate-file|test-simple)" || echo "❌ Routes غير موجودة"

echo ""
echo "🔍 الخطوة 6: فحص Laravel cache"
echo "-------------------------------"
echo "فحص Laravel cache:"
php artisan route:cache --help > /dev/null 2>&1 && echo "✅ Laravel يعمل" || echo "❌ Laravel لا يعمل"

echo ""
echo "🔍 الخطوة 7: فحص PHP-FPM"
echo "-------------------------"
echo "فحص PHP-FPM status:"
systemctl status php8.2-fpm --no-pager -l | head -10

echo ""
echo "🔍 الخطوة 8: فحص Nginx configuration النهائي"
echo "--------------------------------------------"
echo "فحص app.idg-lab.com.sa config:"
grep -A 20 -B 5 "location /" /etc/nginx/sites-available/app.idg-lab.com.sa

echo ""
echo "📊 ملخص المشكلة:"
echo "---------------"
echo "1. إذا كان Laravel logs لا تظهر أي شيء: المشكلة في Nginx"
echo "2. إذا كان Nginx logs تظهر الطلب: المشكلة في Laravel"
echo "3. إذا كان Route بسيط لا يعمل: المشكلة في Nginx config"

echo ""
echo "🚀 الحلول المقترحة:"
echo "-------------------"
echo "1. فحص Laravel logs عند الطلب الخارجي"
echo "2. فحص Nginx logs"
echo "3. اختبار route بسيط"
echo "4. إعادة تشغيل PHP-FPM إذا لزم الأمر" 