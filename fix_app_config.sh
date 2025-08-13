#!/bin/bash

echo "🔧 إصلاح app.idg-lab.com.sa Nginx Configuration"
echo "==============================================="

echo ""
echo "📁 الخطوة 1: نسخ احتياطي للـ config الحالي"
echo "------------------------------------------"
cp /etc/nginx/sites-available/app.idg-lab.com.sa /etc/nginx/sites-available/app.idg-lab.com.sa.backup
echo "✅ تم إنشاء نسخة احتياطية: app.idg-lab.com.sa.backup"

echo ""
echo "🔧 الخطوة 2: إنشاء app.idg-lab.com.sa config جديد"
echo "------------------------------------------------"

# إنشاء config جديد
cat > /etc/nginx/sites-available/app.idg-lab.com.sa << 'EOF'
# HTTP -> HTTPS redirect
server {
    listen 80;
    listen [::]:80;
    server_name app.idg-lab.com.sa;
    return 301 https://$host$request_uri;
}

# HTTPS Laravel configuration
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name app.idg-lab.com.sa;

    root /var/www/idg/public;
    index index.php index.html;

    # SSL certificates
    ssl_certificate /etc/letsencrypt/live/app.idg-lab.com.sa/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/app.idg-lab.com.sa/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Increase upload size for certificates
    client_max_body_size 100M;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    # Main location block - pass all requests to Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\.ht {
        deny all;
    }

    # Deny access to sensitive files
    location ~ /\.(env|log|git) {
        deny all;
    }

    # Cache static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

echo "✅ تم إنشاء app.idg-lab.com.sa config جديد"

echo ""
echo "🧪 الخطوة 3: اختبار Nginx configuration"
echo "--------------------------------------"
if nginx -t; then
    echo "✅ Nginx configuration صحيح"
else
    echo "❌ Nginx configuration خاطئ"
    echo "استعادة النسخة الاحتياطية..."
    cp /etc/nginx/sites-available/app.idg-lab.com.sa.backup /etc/nginx/sites-available/app.idg-lab.com.sa
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