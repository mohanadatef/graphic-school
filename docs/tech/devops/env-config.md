# Environment Configuration

## Overview

This document explains all environment variables used in Graphic School 2.0 and how to configure them for different environments.

## Backend Environment Variables

### Application Configuration

```env
APP_NAME="Graphic School"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=UTC
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graphic_school
DB_USERNAME=gs_user
DB_PASSWORD=strong_password
```

### Cache Configuration

```env
CACHE_DRIVER=redis
CACHE_PREFIX=gs_

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
```

### Session Configuration

```env
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

### Queue Configuration

```env
QUEUE_CONNECTION=redis
QUEUE_DEFAULT_QUEUE=default
```

### Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### File Storage

```env
FILESYSTEM_DISK=local
# For S3:
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=
# AWS_BUCKET=
# AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Logging

```env
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null
```

### Broadcasting

```env
BROADCAST_DRIVER=log
```

## Frontend Environment Variables

### API Configuration

```env
VITE_API_URL=https://yourdomain.com/api
VITE_APP_NAME=Graphic School
VITE_APP_URL=https://yourdomain.com
```

### Feature Flags

```env
VITE_ENABLE_ANALYTICS=false
VITE_ENABLE_SENTRY=false
```

## Environment-Specific Configurations

### Development

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_DATABASE=graphic_school_dev

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

LOG_LEVEL=debug
```

### Staging

```env
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.yourdomain.com

DB_DATABASE=graphic_school_staging

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

LOG_LEVEL=info
```

### Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=graphic_school

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

LOG_LEVEL=error
```

## Security Configuration

### Application Key

Generate with:
```bash
php artisan key:generate
```

### Encryption

Uses Laravel's encryption:
- Automatically configured
- Uses APP_KEY

### CORS

Configure in `config/cors.php`:
```php
'allowed_origins' => [
    'https://yourdomain.com',
],
```

## Database Configuration

### Connection Settings

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graphic_school
DB_USERNAME=gs_user
DB_PASSWORD=strong_password
```

### Connection Pooling

Configure in `config/database.php`:
```php
'options' => [
    PDO::ATTR_PERSISTENT => true,
],
```

## Cache Configuration

### Redis Settings

```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

### Cache Prefix

```env
CACHE_PREFIX=gs_
```

## Queue Configuration

### Queue Driver

```env
QUEUE_CONNECTION=redis
```

### Queue Settings

Configure in `config/queue.php`:
```php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => env('QUEUE_DEFAULT_QUEUE', 'default'),
    'retry_after' => 90,
],
```

## Mail Configuration

### SMTP Settings

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Graphic School"
```

### Mail Services

- **Mailtrap:** For development
- **SendGrid:** For production
- **AWS SES:** For high volume
- **Mailgun:** Alternative option

## File Storage Configuration

### Local Storage

```env
FILESYSTEM_DISK=local
```

### S3 Storage

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_USE_PATH_STYLE_ENDPOINT=false
```

## Logging Configuration

### Log Channels

```env
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Log Rotation

Configure in `config/logging.php`:
```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'error'),
    'days' => 14,
],
```

## Performance Configuration

### OPcache

Configure in `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Database Optimization

```env
DB_STRICT_MODE=true
```

## Monitoring Configuration

### Error Tracking

```env
SENTRY_LARAVEL_DSN=null
SENTRY_TRACES_SAMPLE_RATE=0.1
```

### Analytics

```env
GOOGLE_ANALYTICS_ID=null
```

## Backup Configuration

### Database Backup

```env
BACKUP_DISK=local
BACKUP_RETENTION_DAYS=30
```

## Environment File Security

### .env File Protection

- Never commit `.env` to version control
- Use `.env.example` as template
- Restrict file permissions: `chmod 600 .env`
- Use different keys per environment

### Secret Management

- Use environment variables for secrets
- Consider secret management services
- Rotate keys regularly
- Never hardcode secrets

## Configuration Validation

### Check Configuration

```bash
# Validate environment
php artisan config:cache
php artisan config:clear

# Test database connection
php artisan migrate:status

# Test cache
php artisan cache:clear
```

## Conclusion

Proper environment configuration ensures:
- Security
- Performance
- Reliability
- Scalability

Always use environment-specific configurations and never commit sensitive data.

