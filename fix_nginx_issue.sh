#!/bin/bash

echo "🔧 إصلاح مشكلة Nginx مع Laravel"
echo "================================"

echo ""
echo "📁 الخطوة 1: فحص Nginx configuration"
echo "-----------------------------------"

# فحص Nginx config
if [ -f "/etc/nginx/sites-available/default" ]; then
    echo "✅ Nginx default config موجود"
    echo "فحص محتوى default config:"
    grep -A 20 -B 5 "location /" /etc/nginx/sites-available/default
else
    echo "❌ Nginx default config غير موجود"
fi

if [ -f "/etc/nginx/sites-available/app.idg-lab.com.sa" ]; then
    echo ""
    echo "✅ Nginx app.idg-lab.com.sa config موجود"
    echo "فحص محتوى app.idg-lab.com.sa config:"
    grep -A 20 -B 5 "location /" /etc/nginx/sites-available/app.idg-lab.com.sa
else
    echo "❌ Nginx app.idg-lab.com.sa config غير موجود"
fi

echo ""
echo "🔧 الخطوة 2: إنشاء route بسيط للاختبار"
echo "--------------------------------------"

# إنشاء route بسيط
php artisan tinker --execute="
Route::get('/test-simple', function() {
    return response('Hello World from Laravel!');
});
echo '✅ Route بسيط تم إنشاؤه\n';
"

echo ""
echo "🧪 الخطوة 3: اختبار Route البسيط"
echo "--------------------------------"
echo "1. اختبار محلي:"
curl -s -o /dev/null -w "Local: %{http_code}\n" "http://localhost/test-simple"

echo ""
echo "2. اختبار خارجي:"
curl -s -o /dev/null -w "External: %{http_code}\n" "https://app.idg-lab.com.sa/test-simple"

echo ""
echo "📋 الخطوة 4: فحص Laravel logs"
echo "-----------------------------"
echo "آخر 5 أسطر من Laravel logs:"
tail -5 storage/logs/laravel.log

echo ""
echo "🔍 الخطوة 5: فحص Nginx logs"
echo "---------------------------"
echo "آخر 5 أسطر من Nginx access logs:"
tail -5 /var/log/nginx/access.log 2>/dev/null || echo "لا يمكن الوصول لـ Nginx access logs"

echo ""
echo "📊 ملخص المشكلة:"
echo "---------------"
echo "1. إذا كان Local يعمل و External لا يعمل: مشكلة في Nginx config"
echo "2. إذا كان كلاهما لا يعمل: مشكلة في Laravel"
echo "3. إذا كان Route البسيط يعمل: المشكلة في route معين"

echo ""
echo "🚀 الحلول المقترحة:"
echo "-------------------"
echo "1. فحص Nginx configuration"
echo "2. التأكد من أن Nginx يوجه جميع الطلبات إلى Laravel"
echo "3. إعادة تشغيل Nginx بعد التعديلات" 