#!/bin/bash

echo "🔍 تشخيص دقيق لمشكلة 403 على السيرفر"
echo "====================================="

echo ""
echo "📁 فحص الملفات الموجودة:"
echo "------------------------"
ls -la storage/app/public/certificates/

echo ""
echo "🔧 اختبار Routes محلياً:"
echo "------------------------"
echo "1. اختبار route محلي:"
curl -s -o /dev/null -w "Local: %{http_code}\n" "http://localhost/certificate-file/certificates/certificate-IDG-2025-009-1755088881.pdf"

echo ""
echo "2. اختبار route خارجي:"
curl -s -o /dev/null -w "External: %{http_code}\n" "https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-009-1755088881.pdf"

echo ""
echo "📋 فحص Laravel logs:"
echo "-------------------"
echo "آخر 10 أسطر من Laravel logs:"
tail -10 storage/logs/laravel.log

echo ""
echo "🔍 فحص Nginx logs:"
echo "-----------------"
echo "آخر 10 أسطر من Nginx error logs:"
tail -10 /var/log/nginx/error.log 2>/dev/null || echo "لا يمكن الوصول لـ Nginx logs"

echo ""
echo "🧪 اختبار Controller مباشرة:"
echo "---------------------------"
echo "اختبار method serveFile:"
php artisan tinker --execute="
try {
    \$controller = new \App\Http\Controllers\PublicCertificateController();
    \$result = \$controller->serveFile('certificates/certificate-IDG-2025-009-1755088881.pdf');
    echo '✅ Controller يعمل\n';
} catch (\Exception \$e) {
    echo '❌ Controller error: ' . \$e->getMessage() . '\n';
}
"

echo ""
echo "📊 ملخص المشكلة:"
echo "---------------"
echo "1. إذا كان Local يعمل و External لا يعمل: مشكلة في Nginx/SSL"
echo "2. إذا كان كلاهما لا يعمل: مشكلة في Laravel/Controller"
echo "3. إذا كان Controller يعمل: مشكلة في Route/Web Server" 