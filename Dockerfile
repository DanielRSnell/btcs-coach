# BTCS Coach - Production Ready Container
FROM php:8.3-fpm-alpine

# Install system dependencies and Node.js
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    nginx \
    supervisor \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite gd xml intl zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies and build assets
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build && npm cache clean --force

# Set up Laravel environment
RUN cp .env.example .env && \
    php artisan key:generate

# Set up storage and database directories
RUN mkdir -p database storage/logs storage/framework/{cache,sessions,views} bootstrap/cache && \
    touch database/database.sqlite && \
    chmod -R 775 storage bootstrap/cache database

# Run initial setup
RUN php artisan migrate --force && \
    php artisan db:seed --class=ProductionDataSeeder --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Configure Nginx to use existing www-data user
RUN sed -i 's/user nginx;/user www-data;/' /etc/nginx/nginx.conf

# Configure Nginx virtual host
COPY <<EOF /etc/nginx/http.d/default.conf
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php index.html index.htm;

    client_max_body_size 100M;
    
    # Add custom headers for debugging
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    # Main location block for Laravel
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # Handle missing favicon gracefully
    location = /favicon.ico {
        access_log off;
        log_not_found off;
        try_files \$uri =204;
    }
    
    # Handle robots.txt
    location = /robots.txt {
        access_log off;
        log_not_found off;
        try_files \$uri =204;
    }

    # PHP-FPM configuration
    location ~ \.php\$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)\$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    # Block access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Static files with caching
    location ~* \.(css|gif|ico|jpeg|jpg|js|png|svg|woff|woff2|ttf|eot)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
        try_files \$uri =404;
    }

    # Deny access to sensitive files
    location ~ /(\.env|\.git|composer\.|package\.|docker|Dockerfile) {
        deny all;
        access_log off;
        log_not_found off;
    }
}
EOF

# Configure Supervisor
COPY <<EOF /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root
logfile=/dev/stdout
logfile_maxbytes=0
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm -F
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
priority=1

[program:nginx]
command=nginx -g "daemon off;"
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
priority=2
EOF

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/bootstrap/cache && \
    chmod 644 /var/www/html/public/index.php

# Health check script
COPY <<EOF /health-check.sh
#!/bin/sh
curl -f http://localhost/ || exit 1
EOF
RUN chmod +x /health-check.sh

# Expose port
EXPOSE 80

# Create startup script to fix permissions on container startup
COPY <<EOF /startup.sh
#!/bin/sh
set -e

echo "🚀 Starting BTCS Coach container..."

# Ensure directories exist
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/database

# Fix permissions for all Laravel directories
echo "📁 Setting file permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache  
chmod -R 775 /var/www/html/database
chmod 644 /var/www/html/public/index.php

# Verify index.php exists and is readable
if [ ! -f /var/www/html/public/index.php ]; then
    echo "❌ ERROR: /var/www/html/public/index.php not found!"
    exit 1
fi

echo "✅ File permissions set successfully"
echo "📄 index.php permissions: \$(ls -la /var/www/html/public/index.php)"

# Test nginx configuration
echo "🔧 Testing nginx configuration..."
nginx -t

echo "🎯 Starting services with supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

RUN chmod +x /startup.sh

# Start with our custom script
CMD ["/startup.sh"]