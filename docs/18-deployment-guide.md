# 🚀 Deployment Guide - Graphic School

## Requirements

### Backend Requirements:
- **PHP**: 8.1 أو أحدث
- **Composer**: Latest version
- **MySQL/MariaDB**: 5.7+ / 10.3+
- **Extensions**: 
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `json`
  - `xml`
  - `curl`
  - `zip`
  - `gd` أو `imagick` (للمعالجة الصور)

### Frontend Requirements:
- **Node.js**: 18+ أو أحدث
- **NPM**: Latest version

### Server Requirements:
- **Web Server**: Apache 2.4+ أو Nginx 1.18+
- **PHP-FPM**: 8.1+
- **Memory**: 512MB+ (1GB+ recommended)
- **Disk Space**: 2GB+ (يعتمد على الملفات)

---

## Development Setup

### 1. Backend Setup

#### Clone Repository:
```bash
git clone <repository-url>
cd graphic-school-api
```

#### Install Dependencies:
```bash
composer install
```

#### Environment Configuration:
```bash
cp .env.example .env
php artisan key:generate
```

#### Configure `.env`:
```env
APP_NAME="Graphic School"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://graphic-school.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graphic_school
DB_USERNAME=root
DB_PASSWORD=

# ... other configurations
```

#### Database Setup:
```bash
php artisan migrate
php artisan db:seed
```

#### Start Development Server:
```bash
php artisan serve
```

الخادم سيعمل على: `http://localhost:8000`

---

### 2. Frontend Setup

#### Navigate to Frontend:
```bash
cd graphic-school-frontend
```

#### Install Dependencies:
```bash
npm install
```

#### Environment Configuration:
Create `.env`:
```env
VITE_API_URL=http://graphic-school.test/api
```

#### Start Development Server:
```bash
npm run dev
```

الخادم سيعمل على: `http://localhost:5173`

---

## Production Deployment

### 1. Backend Deployment

#### Server Setup:
1. **Install PHP 8.1+**:
```bash
sudo apt update
sudo apt install php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd
```

2. **Install Composer**:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

3. **Install MySQL/MariaDB**:
```bash
sudo apt install mysql-server
# أو
sudo apt install mariadb-server
```

4. **Install Nginx**:
```bash
sudo apt install nginx
```

#### Application Deployment:
1. **Clone Repository**:
```bash
cd /var/www
sudo git clone <repository-url> graphic-school-api
cd graphic-school-api
```

2. **Install Dependencies**:
```bash
composer install --no-dev --optimize-autoloader
```

3. **Environment Configuration**:
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure `.env` for Production**:
```env
APP_NAME="Graphic School"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graphic_school_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# File Storage (S3 recommended)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

5. **Database Setup**:
```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder
```

6. **Optimize**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

7. **Set Permissions**:
```bash
sudo chown -R www-data:www-data /var/www/graphic-school-api
sudo chmod -R 755 /var/www/graphic-school-api
sudo chmod -R 775 /var/www/graphic-school-api/storage
sudo chmod -R 775 /var/www/graphic-school-api/bootstrap/cache
```

#### Nginx Configuration:
Create `/etc/nginx/sites-available/graphic-school-api`:
```nginx
server {
    listen 80;
    server_name api.your-domain.com;
    root /var/www/graphic-school-api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/graphic-school-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### SSL Certificate (Let's Encrypt):
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d api.your-domain.com
```

---

### 2. Frontend Deployment

#### Build for Production:
```bash
cd graphic-school-frontend
npm run build
```

This creates `dist/` folder with production-ready files.

#### Deploy to Server:

**Option 1: Nginx Static Files**:
1. Copy `dist/` to server:
```bash
scp -r dist/* user@server:/var/www/graphic-school-frontend/
```

2. Nginx Configuration:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/graphic-school-frontend;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Option 2: CDN (Cloudflare, AWS CloudFront)**:
- Upload `dist/` to CDN
- Configure CDN to serve files

---

## Database Setup

### Create Database:
```sql
CREATE DATABASE graphic_school_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'graphic_school_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON graphic_school_prod.* TO 'graphic_school_user'@'localhost';
FLUSH PRIVILEGES;
```

### Run Migrations:
```bash
php artisan migrate --force
```

### Seed Database (Optional):
```bash
php artisan db:seed --class=DatabaseSeeder
```

---

## File Storage Setup

### Option 1: Local Storage (Development)
```env
FILESYSTEM_DISK=local
```

### Option 2: S3 (Production Recommended)
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Option 3: DigitalOcean Spaces
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=nyc3
AWS_BUCKET=your-space
AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## Cache & Queue Setup

### Redis Setup:
```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

Configure `.env`:
```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Queue Worker:
```bash
php artisan queue:work --daemon
```

Or use Supervisor:
```ini
[program:graphic-school-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/graphic-school-api/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/graphic-school-api/storage/logs/worker.log
```

---

## Cron Jobs

### Laravel Scheduler:
Add to crontab:
```bash
* * * * * cd /var/www/graphic-school-api && php artisan schedule:run >> /dev/null 2>&1
```

Or use systemd timer (recommended).

---

## Monitoring & Logging

### Log Files:
- **Laravel Logs**: `storage/logs/laravel.log`
- **Nginx Logs**: `/var/log/nginx/`
- **PHP-FPM Logs**: `/var/log/php8.1-fpm.log`

### Health Check:
```bash
curl https://api.your-domain.com/api/health
```

### Monitoring Tools:
- **Sentry**: Error tracking
- **New Relic**: Application monitoring
- **Custom Dashboard**: Built-in monitoring

---

## Backup Strategy

### Database Backup:
```bash
# Daily backup script
mysqldump -u user -p database_name > backup_$(date +%Y%m%d).sql
```

### File Backup:
```bash
# Backup storage directory
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/
```

### Automated Backup (Cron):
```bash
0 2 * * * /path/to/backup-script.sh
```

---

## Security Checklist

- ✅ **HTTPS**: SSL certificate installed
- ✅ **Firewall**: UFW or iptables configured
- ✅ **File Permissions**: Correct permissions set
- ✅ **Environment Variables**: `.env` secured
- ✅ **Database**: Strong passwords
- ✅ **Rate Limiting**: Configured
- ✅ **CORS**: Properly configured
- ✅ **Security Headers**: Added in Nginx

---

## Troubleshooting

### Common Issues:

#### 1. 500 Internal Server Error:
- Check Laravel logs: `storage/logs/laravel.log`
- Check PHP-FPM logs
- Verify file permissions
- Check `.env` configuration

#### 2. Database Connection Error:
- Verify database credentials in `.env`
- Check database server is running
- Verify network connectivity

#### 3. Permission Denied:
```bash
sudo chown -R www-data:www-data /var/www/graphic-school-api
sudo chmod -R 755 /var/www/graphic-school-api
sudo chmod -R 775 storage bootstrap/cache
```

#### 4. Route Not Found:
```bash
php artisan route:clear
php artisan route:cache
```

#### 5. Cache Issues:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Performance Optimization

### Backend:
- ✅ **OPcache**: Enable PHP OPcache
- ✅ **Redis Cache**: Use Redis for caching
- ✅ **Database Indexes**: Already added
- ✅ **Query Optimization**: Eager loading
- ✅ **CDN**: Use CDN for static assets

### Frontend:
- ✅ **Code Splitting**: Vite automatic
- ✅ **Asset Optimization**: Vite build
- ✅ **Lazy Loading**: Routes lazy loaded
- ✅ **CDN**: Serve assets from CDN

---

## Docker Deployment (Optional)

### Docker Compose Example:
```yaml
version: '3.8'
services:
  app:
    build: .
    volumes:
      - .:/var/www
    ports:
      - "8000:8000"
  
  nginx:
    image: nginx:alpine
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
    ports:
      - "80:80"
  
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: graphic_school
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - mysql_data:/var/lib/mysql
  
  redis:
    image: redis:alpine

volumes:
  mysql_data:
```

---

## CI/CD (Optional)

### GitHub Actions Example:
```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Deploy to server
        run: |
          # Deployment commands
```

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

