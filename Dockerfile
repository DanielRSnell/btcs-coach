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
    index index.php index.html;

    client_max_body_size 100M;

    # Ensure proper file permissions and handle static files
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # Handle missing favicon gracefully
    location = /favicon.ico {
        access_log off;
        log_not_found off;
        try_files \$uri =204;
    }

    location ~ \.php\$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(css|gif|ico|jpeg|jpg|js|png|svg|woff|woff2|ttf|eot)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files \$uri =404;
    }

    # Deny access to sensitive files
    location ~ /(\.env|\.git|composer\.|package\.|docker) {
        deny all;
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
# Fix permissions for mounted volumes
chown -R www-data:www-data /var/www/html/storage /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chmod 644 /var/www/html/public/index.php
# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

RUN chmod +x /startup.sh

# Start with our custom script
CMD ["/startup.sh"]