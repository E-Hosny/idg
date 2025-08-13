#!/bin/bash

echo "🔧 إصلاح Nginx Configuration تلقائياً"
echo "===================================="

echo ""
echo "📁 الخطوة 1: نسخ احتياطي للـ config الحالي"
echo "------------------------------------------"
cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup
echo "✅ تم إنشاء نسخة احتياطية: default.backup"

echo ""
echo "🔧 الخطوة 2: إنشاء Nginx config جديد"
echo "------------------------------------"

# إنشاء config جديد
cat > /etc/nginx/sites-available/default << 'EOF'
# Default server configuration for Laravel
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /var/www/idg/public;
    index index.php index.html index.htm;

    server_name _;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }

    # Increase upload size for certificates
    client_max_body_size 100M;
}
EOF

echo "✅ تم إنشاء Nginx config جديد"

echo ""
echo "🧪 الخطوة 3: اختبار Nginx configuration"
echo "--------------------------------------"
if nginx -t; then
    echo "✅ Nginx configuration صحيح"
else
    echo "❌ Nginx configuration خاطئ"
    echo "استعادة النسخة الاحتياطية..."
    cp /etc/nginx/sites-available/default.backup /etc/nginx/sites-available/default
    exit 1
fi

echo ""
echo "🔄 الخطوة 4: إعادة تشغيل Nginx"
echo "-------------------------------"
systemctl reload nginx
if [ $? -eq 0 ]; then
    echo "✅ تم إعادة تشغيل Nginx بنجاح"
else
    echo "❌ فشل في إعادة تشغيل Nginx"
    exit 1
fi

echo ""
echo "🧪 الخطوة 5: اختبار الحل"
echo "------------------------"
echo "1. اختبار route محلي:"
curl -s -o /dev/null -w "Local: %{http_code}\n" "http://localhost/certificate-file/certificates/certificate-IDG-2025-009-1755088881.pdf"

echo ""
echo "2. اختبار route خارجي:"
curl -s -o /dev/null -w "External: %{http_code}\n" "https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-009-1755088881.pdf"

echo ""
echo "📋 النتيجة:"
echo "----------"
echo "إذا كان كلاهما يعطي نفس النتيجة: المشكلة محلولة! 🎉"
echo "إذا استمرت المشكلة: تحقق من Laravel logs"

echo ""
echo "🔍 للتشخيص:"
echo "tail -f storage/logs/laravel.log" 