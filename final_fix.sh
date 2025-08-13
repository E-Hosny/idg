#!/bin/bash

echo "🔧 الإصلاح النهائي لمشكلة 403"
echo "============================="

echo ""
echo "📁 الخطوة 1: فحص fastcgi configuration"
echo "--------------------------------------"
echo "فحص fastcgi-php.conf:"
if [ -f "/etc/nginx/snippets/fastcgi-php.conf" ]; then
    cat /etc/nginx/snippets/fastcgi-php.conf
else
    echo "❌ fastcgi-php.conf غير موجود"
fi

echo ""
echo "🔧 الخطوة 2: إنشاء fastcgi configuration صحيح"
echo "---------------------------------------------"

# إنشاء fastcgi configuration صحيح
cat > /etc/nginx/snippets/fastcgi-php.conf << 'EOF'
# regex to split $uri to $fastcgi_script_name and $fastcgi_path
fastcgi_split_path_info ^(.+\.php)(/.+)$;

# Check that the PHP script exists before passing it
try_files $fastcgi_script_name =404;

# Bypass the fact that try_files resets $fastcgi_path_info
# see: http://trac.nginx.org/nginx/ticket/321
set $path_info $fastcgi_path_info;
fastcgi_param PATH_INFO $path_info;

fastcgi_index index.php;
include fastcgi_params;
EOF

echo "✅ تم إنشاء fastcgi configuration صحيح"

echo ""
echo "🔧 الخطوة 3: تحديث app.idg-lab.com.sa config"
echo "--------------------------------------------"

# تحديث config
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

    # PHP processing with correct fastcgi configuration
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        try_files $fastcgi_script_name =404;
        set $path_info $fastcgi_path_info;
        fastcgi_param PATH_INFO $path_info;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
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

echo "✅ تم تحديث app.idg-lab.com.sa config"

echo ""
echo "🧪 الخطوة 4: اختبار Nginx configuration"
echo "--------------------------------------"
if nginx -t; then
    echo "✅ Nginx configuration صحيح"
else
    echo "❌ Nginx configuration خاطئ"
    exit 1
fi

echo ""
echo "🔄 الخطوة 5: إعادة تشغيل Nginx"
echo "-------------------------------"
systemctl reload nginx
if [ $? -eq 0 ]; then
    echo "✅ تم إعادة تشغيل Nginx بنجاح"
else
    echo "❌ فشل في إعادة تشغيل Nginx"
    exit 1
fi

echo ""
echo "🧪 الخطوة 6: اختبار الحل النهائي"
echo "--------------------------------"
echo "1. اختبار route محلي:"
curl -s -o /dev/null -w "Local: %{http_code}\n" "http://localhost/certificate-file/certificates/certificate-IDG-2025-009-1755088881.pdf"

echo ""
echo "2. اختبار route خارجي:"
curl -s -o /dev/null -w "External: %{http_code}\n" "https://app.idg-lab.com.sa/certificate-file/certificates/certificate-IDG-2025-009-1755088881.pdf"

echo ""
echo "📋 النتيجة النهائية:"
echo "-------------------"
echo "إذا كان كلاهما يعطي نفس النتيجة: المشكلة محلولة! 🎉"
echo "إذا استمرت المشكلة: تحقق من Laravel logs"

echo ""
echo "🔍 للتشخيص النهائي:"
echo "tail -f storage/logs/laravel.log" 