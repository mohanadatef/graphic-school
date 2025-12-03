# Deployment Guide

## Overview

This guide covers deploying Graphic School 2.0 LMC system to production environments. It includes server setup, application deployment, and post-deployment configuration.

**Deployment Model:** One client = one deployment (own domain, own DB)  
**Stack:** Laravel 11 (Backend) + Vue.js 3 (Frontend) + MySQL + Nginx

## Prerequisites

- **Linux Server:** Ubuntu 22.04 LTS recommended
- **Domain Name:** Pointed to server IP
- **SSH Access:** With sudo privileges
- **Basic Knowledge:** Server administration, Git, Linux commands

## Server Requirements

### Minimum Requirements
- **CPU:** 2 cores
- **RAM:** 4GB
- **Storage:** 50GB SSD
- **OS:** Ubuntu 22.04 LTS

### Recommended Requirements
- **CPU:** 4+ cores
- **RAM:** 8GB+
- **Storage:** 100GB+ SSD
- **OS:** Ubuntu 22.04 LTS

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

# Install dependencies (production)
composer install --optimize-autoloader --no-dev

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Environment Configuration

Edit `.env` file:

```env
APP_NAME="Graphic School"
APP_ENV=production
APP_KEY=base64:... (generated)
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

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

FILESYSTEM_DISK=local
# Or for S3:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=...
# AWS_SECRET_ACCESS_KEY=...
# AWS_DEFAULT_REGION=...
# AWS_BUCKET=...
```

### 4. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed database (optional, for default data)
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=LanguageSeeder --force
php artisan db:seed --class=CurrencySeeder --force
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

# Create production environment file
cat > .env.production << EOF
VITE_API_URL=https://yourdomain.com/api
VITE_APP_NAME="Graphic School"
EOF

# Build for production
npm run build
```

### 7. Optimize Application

```bash
# Backend optimization
cd ../graphic-school-api
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Web Server Configuration

### Nginx Configuration

Create `/etc/nginx/sites-available/graphic-school`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/graphic-school/graphic-school-frontend/dist;

    # Frontend routes (SPA)
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API routes
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
        root /var/www/graphic-school/graphic-school-api/public;
        
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files (storage)
    location /storage {
        alias /var/www/graphic-school/graphic-school-api/storage/app/public;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Static assets (frontend)
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/json;
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
# Edit crontab for www-data user
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

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

### Redis Configuration

Edit `/etc/redis/redis.conf`:

```conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

Restart Redis:

```bash
sudo systemctl restart redis
```

## Monitoring & Logging

### Application Logs

Logs location:
- **Laravel:** `/var/www/graphic-school/graphic-school-api/storage/logs/`
- **Nginx:** `/var/log/nginx/`
- **PHP-FPM:** `/var/log/php8.2-fpm.log`
- **Queue Workers:** `/var/www/graphic-school/graphic-school-api/storage/logs/worker.log`

### Health Checks

Monitor health endpoint:

```bash
curl https://yourdomain.com/api/health
```

Expected response:

```json
{
    "status": "ok",
    "database": "connected",
    "cache": "connected"
}
```

## Backup Strategy

### Database Backup

Daily backup script (`/usr/local/bin/backup-db.sh`):

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/database"
mkdir -p $BACKUP_DIR

mysqldump -u gs_user -p'password' graphic_school | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete
```

Add to crontab:

```bash
0 2 * * * /usr/local/bin/backup-db.sh
```

### File Backup

Backup storage and uploads:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/files"
mkdir -p $BACKUP_DIR

tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/graphic-school/graphic-school-api/storage/app

# Keep only last 7 days
find $BACKUP_DIR -name "files_*.tar.gz" -mtime +7 -delete
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

# Storage permissions (writable)
sudo chmod -R 775 /var/www/graphic-school/graphic-school-api/storage
sudo chmod -R 775 /var/www/graphic-school/graphic-school-api/bootstrap/cache
```

### Environment Security

- Set `APP_DEBUG=false` in production
- Use strong database passwords
- Keep `.env` file secure (600 permissions)
- Never commit `.env` to version control

## Post-Deployment

### 1. Run Setup Wizard

Visit: `https://yourdomain.com/setup`

Complete setup:
1. General information (academy name, country, timezone)
2. Branding (logo, colors, fonts)
3. Languages (default + additional)
4. Currency (default + additional)
5. Pages activation
6. Activate website

### 2. Create Admin User

```bash
php artisan tinker
```

```php
$role = \Modules\ACL\Roles\Models\Role::where('name', 'admin')->first();
$user = \Modules\ACL\Users\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@yourdomain.com',
    'password' => bcrypt('secure-password'),
    'role_id' => $role->id,
]);
```

### 3. Verify Installation

- ✅ Check health endpoint: `curl https://yourdomain.com/api/health`
- ✅ Test login with admin credentials
- ✅ Verify API endpoints respond correctly
- ✅ Check frontend renders properly
- ✅ Test public enrollment flow
- ✅ Verify certificate verification works

### 4. Monitor Performance

- Check server resources: `htop` or `top`
- Monitor application logs: `tail -f storage/logs/laravel.log`
- Review error logs regularly
- Monitor disk space: `df -h`
- Check queue workers: `sudo supervisorctl status`

## Build Steps

### Backend Build

```bash
cd graphic-school-api
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Frontend Build

```bash
cd graphic-school-frontend
npm install
npm run build
```

Output: `dist/` directory

## Storage Configuration

### Local Storage (Default)

Files stored in: `/var/www/graphic-school/graphic-school-api/storage/app/public/`

Ensure storage link exists:
```bash
php artisan storage:link
```

### S3 Storage (Optional)

Configure in `.env`:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_URL=your-bucket-url
```

## Cache Configuration

### Redis Cache

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Scheduler Configuration

For scheduled tasks, ensure cron is running:

```bash
* * * * * cd /var/www/graphic-school/graphic-school-api && php artisan schedule:run >> /dev/null 2>&1
```

Common scheduled tasks:
- Log cleanup (daily)
- Backup jobs
- Email notifications
- Cache warming

## Troubleshooting

### 500 Errors

1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Verify file permissions
3. Check PHP-FPM status: `sudo systemctl status php8.2-fpm`
4. Check Nginx error logs: `tail -f /var/log/nginx/error.log`

### Database Connection Issues

1. Verify credentials in `.env`
2. Check MySQL status: `sudo systemctl status mysql`
3. Test connection: `mysql -u gs_user -p graphic_school`
4. Check firewall rules

### Frontend Not Loading

1. Check Nginx configuration: `sudo nginx -t`
2. Verify build files exist: `ls -la dist/`
3. Check browser console for errors
4. Verify API URL in `.env.production`

### Queue Not Processing

1. Check supervisor status: `sudo supervisorctl status`
2. Check queue worker logs
3. Restart workers: `sudo supervisorctl restart graphic-school-worker:*`

## Maintenance

### Regular Tasks

1. **Daily:**
   - Monitor logs
   - Check disk space
   - Verify backups

2. **Weekly:**
   - Review error logs
   - Check performance metrics
   - Update dependencies (if needed)

3. **Monthly:**
   - Review and clean old logs
   - Update system packages
   - Review security updates

### Updates

```bash
# Backend updates
cd graphic-school-api
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache

# Frontend updates
cd graphic-school-frontend
git pull origin main
npm install
npm run build
```

---

**Deployment Status:** ✅ Production-ready

