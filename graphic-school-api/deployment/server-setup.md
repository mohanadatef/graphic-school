# Server Setup Guide - Production Linux Deployment

This guide covers setting up a production Linux server for Graphic School 2.0.

---

## Prerequisites

- Ubuntu 22.04 LTS or Debian 11+ server
- Root or sudo access
- Domain name pointed to server IP
- SSH access configured

---

## Step 1: Update System & Install Packages

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install essential packages
sudo apt install -y \
    nginx \
    mysql-server \
    redis-server \
    supervisor \
    certbot \
    python3-certbot-nginx \
    git \
    curl \
    unzip \
    software-properties-common

# Install PHP 8.2 and extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-xml \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-zip \
    php8.2-gd \
    php8.2-redis \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

---

## Step 2: Configure MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p <<EOF
CREATE DATABASE graphic_school_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'graphic_school_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON graphic_school_prod.* TO 'graphic_school_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF
```

---

## Step 3: Configure Redis

```bash
# Redis is already installed, just ensure it's running
sudo systemctl enable redis-server
sudo systemctl start redis-server
sudo systemctl status redis-server
```

---

## Step 4: Setup Laravel Application

```bash
# Create application directory
sudo mkdir -p /var/www/graphic-school
cd /var/www/graphic-school

# Clone repository (or upload files)
# git clone https://github.com/your-org/graphic-school.git .

# Set ownership
sudo chown -R www-data:www-data /var/www/graphic-school

# Set permissions
sudo chmod -R 755 /var/www/graphic-school
sudo chmod -R 775 /var/www/graphic-school/storage
sudo chmod -R 775 /var/www/graphic-school/bootstrap/cache

# Install dependencies
cd /var/www/graphic-school/graphic-school-api
composer install --no-dev --optimize-autoloader

# Copy environment file
cp environment/.env.production.example .env

# Generate application key
php artisan key:generate

# Edit .env file with production values
nano .env

# Run migrations (NO fresh - incremental only)
php artisan migrate --force

# Seed database (optional - only if needed)
# php artisan db:seed --force

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Step 5: Configure Nginx

```bash
# Create Nginx configuration
sudo nano /etc/nginx/sites-available/graphic-school
```

**Content:** (See `nginx.conf.example` for full configuration)

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/graphic-school/graphic-school-api/public;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security Headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;

    # Disable directory listing
    autoindex off;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/graphic-school /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

## Step 6: Enable SSL with Let's Encrypt

```bash
# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run

# Certbot will automatically renew certificates
```

---

## Step 7: Setup Supervisor for Queue Workers

```bash
# Create supervisor configuration
sudo nano /etc/supervisor/conf.d/graphic-school-workers.conf
```

**Content:** (See `supervisor.conf.example` for full configuration)

```ini
[program:graphic-school-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/graphic-school/graphic-school-api/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/graphic-school/graphic-school-api/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start graphic-school-worker:*

# Check status
sudo supervisorctl status
```

---

## Step 8: Setup Cron Jobs

```bash
# Edit crontab
sudo crontab -e -u www-data

# Add Laravel scheduler
* * * * * cd /var/www/graphic-school/graphic-school-api && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 9: Enable Services

```bash
# Enable and start services
sudo systemctl enable nginx
sudo systemctl enable redis-server
sudo systemctl enable supervisor
sudo systemctl enable php8.2-fpm

# Start services
sudo systemctl start nginx
sudo systemctl start redis-server
sudo systemctl start supervisor
sudo systemctl start php8.2-fpm

# Check status
sudo systemctl status nginx
sudo systemctl status redis-server
sudo systemctl status supervisor
sudo systemctl status php8.2-fpm
```

---

## Step 10: Configure Log Rotation

```bash
# Create log rotation config
sudo nano /etc/logrotate.d/graphic-school
```

**Content:**

```
/var/www/graphic-school/graphic-school-api/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        systemctl reload php8.2-fpm > /dev/null 2>&1 || true
    endscript
}
```

---

## Step 11: Firewall Configuration

```bash
# Enable UFW firewall
sudo ufw enable

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Check status
sudo ufw status
```

---

## Step 12: Final Checks

```bash
# Check Laravel health
curl https://yourdomain.com/api/health

# Check queue workers
sudo supervisorctl status

# Check logs
tail -f /var/www/graphic-school/graphic-school-api/storage/logs/laravel.log

# Test application
# Visit https://yourdomain.com in browser
```

---

## Troubleshooting

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/graphic-school
sudo chmod -R 775 /var/www/graphic-school/storage
sudo chmod -R 775 /var/www/graphic-school/bootstrap/cache
```

### Queue Workers Not Running
```bash
sudo supervisorctl restart graphic-school-worker:*
sudo supervisorctl status
```

### Nginx 502 Bad Gateway
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check socket
ls -la /var/run/php/php8.2-fpm.sock
```

### SSL Certificate Issues
```bash
# Renew certificate manually
sudo certbot renew

# Check certificate
sudo certbot certificates
```

---

## Security Checklist

- [x] Firewall enabled (UFW)
- [x] SSH key authentication only
- [x] Fail2ban installed (optional but recommended)
- [x] Regular security updates
- [x] SSL certificate installed
- [x] Security headers configured
- [x] File permissions set correctly
- [x] APP_DEBUG=false in production
- [x] Strong database passwords
- [x] Redis password set (if exposed)

---

**Server setup complete!** 🎉

