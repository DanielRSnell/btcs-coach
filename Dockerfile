# BTCS Coach - One-Click Deployment
FROM php:8.2-fpm-alpine

# Install system dependencies and Node.js
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    nginx \
    supervisor \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files (this will use the repository context in Coolify)
COPY . .

# Install dependencies and build assets
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install
RUN npm run build

# Set up Laravel environment
RUN cp .env.example .env
RUN php artisan key:generate

# Configure SQLite database
RUN mkdir -p database
RUN touch database/database.sqlite
RUN chmod 664 database/database.sqlite

# Set up storage directories
RUN mkdir -p storage/logs
RUN mkdir -p storage/framework/cache
RUN mkdir -p storage/framework/sessions
RUN mkdir -p storage/framework/views
RUN mkdir -p bootstrap/cache

# Run migrations and seed data
RUN php artisan migrate --force
RUN php artisan db:seed --class=ProductionDataSeeder --force

# Cache configuration for production
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

# Configure Nginx
COPY <<EOF /etc/nginx/nginx.conf
worker_processes auto;
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    log_format main '\$remote_addr - \$remote_user [\$time_local] "\$request" '
                    '\$status \$body_bytes_sent "\$http_referer" '
                    '"\$http_user_agent" "\$http_x_forwarded_for"';
    
    access_log /var/log/nginx/access.log main;
    
    sendfile on;
    tcp_nopush on;
    keepalive_timeout 65;
    gzip on;
    
    server {
        listen 80;
        server_name localhost;
        root /var/www/html/public;
        index index.php;
        
        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }
        
        location ~ \.php\$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            include fastcgi_params;
        }
        
        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
}
EOF

# Configure Supervisor
COPY <<EOF /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm -F
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0

[program:nginx]
command=nginx -g 'daemon off;'
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=false
startretries=0
EOF

# Expose port
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]