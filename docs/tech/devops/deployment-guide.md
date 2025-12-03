# Deployment Guide

## Overview

This guide covers deploying Graphic School 2.0 to production environments. It includes step-by-step instructions for server setup, application deployment, and post-deployment configuration.

## Prerequisites

- Linux server (Ubuntu 22.04 LTS recommended)
- Domain name pointed to server IP
- SSH access to server
- Basic server administration knowledge

## Server Setup

### 1. Initial Server Configuration

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install essential packages
sudo apt install -y nginx mysql-server redis-server supervisor certbot python3-certbot-nginx git curl unzip
```

### 2. PHP Installation

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 and extensions
sudo apt install -y \
    php8.2 php8.2-fpm php8.2-mysql php8.2-xml \
    php8.2-mbstring php8.2-curl php8.2-zip \
    php8.2-gd php8.2-redis php8.2-bcmath \
    php8.2-intl php8.2-opcache
```

### 3. Node.js Installation

```bash
# Install Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 4. Composer Installation

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 5. Database Setup

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -e "CREATE DATABASE graphic_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'gs_user'@'localhost' IDENTIFIED BY 'strong_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON graphic_school.* TO 'gs_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

## Application Deployment

### 1. Clone Repository

```bash
# Create application directory
sudo mkdir -p /var/www/graphic-school
sudo chown $USER:$USER /var/www/graphic-school

# Clone repository
cd /var/www/graphic-school
git clone <repository-url> .
```

### 2. Backend Setup

```bash
# Navigate to backend
cd graphic-school-api

# Install dependencies
composer install --optimize-autoloader --no-dev

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure .env file
nano .env
```

### 3. Environment Configuration

Edit `.env` file:
```env
APP_NAME="Graphic School"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graphic_school
DB_USERNAME=gs_user
DB_PASSWORD=strong_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force
```

### 5. Storage Setup

```bash
# Create storage link
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 6. Frontend Setup

```bash
# Navigate to frontend
cd ../graphic-school-frontend

# Install dependencies
npm install

# Build for production
npm run build
```

### 7. Frontend Environment

Create `.env.production`:
```env
VITE_API_URL=https://yourdomain.com/api
VITE_APP_NAME=Graphic School
```

Rebuild:
```bash
npm run build
```

## Web Server Configuration

### Nginx Configuration

Create `/etc/nginx/sites-available/graphic-school`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/graphic-school/graphic-school-frontend/dist;

    # Frontend routes
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API routes
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
        root /var/www/graphic-school/graphic-school-api/public;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/graphic-school /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL Certificate

```bash
# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (already configured)
sudo certbot renew --dry-run
```

## Queue Workers

### Supervisor Configuration

Create `/etc/supervisor/conf.d/graphic-school-worker.conf`:

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

Start workers:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start graphic-school-worker:*
```

## Scheduled Tasks

### Cron Configuration

```bash
# Edit crontab
sudo crontab -e -u www-data

# Add Laravel scheduler
* * * * * cd /var/www/graphic-school/graphic-school-api && php artisan schedule:run >> /dev/null 2>&1
```

## Performance Optimization

### PHP OPcache

Edit `/etc/php/8.2/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Redis Configuration

Edit `/etc/redis/redis.conf`:
```conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

### Nginx Optimization

Add to nginx config:
```nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/json;
```

## Monitoring

### Application Logs

Logs location:
- Laravel: `/var/www/graphic-school/graphic-school-api/storage/logs/`
- Nginx: `/var/log/nginx/`
- PHP-FPM: `/var/log/php8.2-fpm.log`

### Health Checks

Monitor health endpoint:
```bash
curl https://yourdomain.com/api/health
```

## Backup Strategy

### Database Backup

```bash
# Daily backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u gs_user -p graphic_school > /backups/db_$DATE.sql
```

### File Backup

```bash
# Backup storage and uploads
tar -czf /backups/files_$DATE.tar.gz /var/www/graphic-school/graphic-school-api/storage/app
```

## Security

### Firewall

```bash
# Configure UFW
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### File Permissions

```bash
# Secure file permissions
sudo chown -R www-data:www-data /var/www/graphic-school
sudo find /var/www/graphic-school -type f -exec chmod 644 {} \;
sudo find /var/www/graphic-school -type d -exec chmod 755 {} \;
```

## Post-Deployment

### 1. Run Setup Wizard

Visit: `https://yourdomain.com/setup`

Complete setup:
- General information
- Branding
- Pages configuration
- Contact information
- Activate website

### 2. Verify Installation

- Check health endpoint
- Test login
- Verify API endpoints
- Check frontend rendering

### 3. Monitor Performance

- Check server resources
- Monitor application logs
- Review error logs
- Test functionality

## Troubleshooting

### Common Issues

1. **500 Errors**
   - Check Laravel logs
   - Verify file permissions
   - Check PHP-FPM status

2. **Database Connection**
   - Verify credentials
   - Check MySQL status
   - Test connection

3. **Frontend Not Loading**
   - Check Nginx configuration
   - Verify build files
   - Check browser console

## Conclusion

This deployment guide provides:
- Complete server setup
- Application deployment
- Web server configuration
- Performance optimization
- Security hardening
- Monitoring setup

Follow these steps for a successful production deployment.

