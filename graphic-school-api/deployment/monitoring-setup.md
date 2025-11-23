# Monitoring & Logging Setup Guide

This guide covers setting up monitoring and logging for Graphic School 2.0 production.

---

## 1. Laravel Telescope (Development/Staging)

### Installation

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Production Safe Mode

In `.env`:

```env
TELESCOPE_ENABLED=true
TELESCOPE_DRIVER=database
```

### Configuration

Edit `config/telescope.php`:

```php
'watchers' => [
    Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
    Watchers\DumpWatcher::class => env('TELESCOPE_DUMP_WATCHER', true),
    Watchers\EventWatcher::class => env('TELESCOPE_EVENT_WATCHER', true),
    Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
    Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
    Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
    Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
    Watchers\ModelWatcher::class => env('TELESCOPE_MODEL_WATCHER', true),
    Watchers\QueryWatcher::class => env('TELESCOPE_QUERY_WATCHER', true),
    Watchers\RequestWatcher::class => env('TELESCOPE_REQUEST_WATCHER', true),
    Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
],
```

### Access Control

Add to `app/Providers/TelescopeServiceProvider.php`:

```php
protected function gate()
{
    Gate::define('viewTelescope', function ($user) {
        return in_array($user->email, [
            'admin@yourdomain.com',
        ]);
    });
}
```

---

## 2. Sentry Error Monitoring

### Installation

```bash
composer require sentry/sentry-laravel
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

### Configuration

In `.env`:

```env
SENTRY_LARAVEL_DSN=https://your-sentry-dsn@sentry.io/project-id
SENTRY_TRACES_SAMPLE_RATE=0.1
SENTRY_ENVIRONMENT=production
```

### Configuration File

Edit `config/sentry.php`:

```php
return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),
    'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.1),
    'environment' => env('SENTRY_ENVIRONMENT', 'production'),
    'release' => env('SENTRY_RELEASE'),
    'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
        // Filter sensitive data
        return $event;
    },
];
```

---

## 3. Health Check Endpoint

### Route

Already exists at `/api/health`

### Enhanced Health Check

Update `app/Http/Controllers/HealthController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'cache' => $this->checkCache(),
        ];

        $allHealthy = !in_array(false, $checks);

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $allHealthy ? 200 : 503);
    }

    protected function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkRedis(): bool
    {
        try {
            Redis::connection()->ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkCache(): bool
    {
        try {
            Cache::put('health_check', 'ok', 1);
            return Cache::get('health_check') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }
}
```

---

## 4. Log Rotation

### Configuration

Create `/etc/logrotate.d/graphic-school`:

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

### Laravel Log Configuration

Edit `config/logging.php`:

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'sentry'],
        'ignore_exceptions' => false,
    ],

    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'error'),
        'days' => 14,
    ],

    'sentry' => [
        'driver' => 'sentry',
        'level' => env('LOG_LEVEL', 'error'),
    ],
],
```

---

## 5. Application Performance Monitoring (APM)

### New Relic (Optional)

```bash
# Install New Relic PHP agent
wget -O - https://download.newrelic.com/548C16BF.gpg | sudo apt-key add -
sudo sh -c 'echo "deb https://download.newrelic.com/debian/ newrelic non-free" > /etc/apt/sources.list.d/newrelic.list'
sudo apt update
sudo apt install newrelic-php5

# Configure
sudo newrelic-install install
```

### Datadog (Optional)

```bash
# Install Datadog agent
DD_API_KEY=your_api_key DD_SITE="datadoghq.com" bash -c "$(curl -L https://s3.amazon.com/dd-agent/scripts/install_script_agent7.sh)"
```

---

## 6. Server Monitoring

### Uptime Monitoring

Use external services:
- **UptimeRobot** (Free)
- **Pingdom** (Paid)
- **StatusCake** (Free tier)

### Setup UptimeRobot

1. Create account at https://uptimerobot.com
2. Add monitor:
   - Type: HTTP(s)
   - URL: `https://yourdomain.com/api/health`
   - Interval: 5 minutes
   - Alert contacts: Email/SMS

---

## 7. Database Monitoring

### Slow Query Log

Edit MySQL config `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2
```

### Query Monitoring

Use Laravel Telescope or database monitoring tools.

---

## 8. Queue Monitoring

### Monitor Queue Size

```bash
# Check queue size
redis-cli LLEN queues:default
redis-cli LLEN queues:emails
redis-cli LLEN queues:notifications
```

### Failed Jobs Alert

Create scheduled task:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('queue:failed')
        ->daily()
        ->emailOnFailure('admin@yourdomain.com');
}
```

---

## 9. Disk Space Monitoring

### Setup Alert

```bash
# Create monitoring script
cat > /usr/local/bin/check-disk.sh << 'EOF'
#!/bin/bash
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "Disk usage is ${DISK_USAGE}%" | mail -s "Disk Space Alert" admin@yourdomain.com
fi
EOF

chmod +x /usr/local/bin/check-disk.sh

# Add to crontab
echo "0 * * * * /usr/local/bin/check-disk.sh" | crontab -
```

---

## 10. Log Aggregation

### Centralized Logging (Optional)

Use services like:
- **Papertrail** (Free tier)
- **Loggly** (Paid)
- **ELK Stack** (Self-hosted)

---

## Best Practices

1. **Log Levels**: Use appropriate log levels (error, warning, info, debug)
2. **Sensitive Data**: Never log passwords, tokens, or credit card numbers
3. **Rotation**: Rotate logs daily, keep 14 days
4. **Monitoring**: Set up alerts for critical errors
5. **Performance**: Monitor slow queries and optimize
6. **Uptime**: Monitor health endpoint every 5 minutes
7. **Queue**: Alert if queue size > 1000
8. **Disk**: Alert if disk usage > 80%

---

**Monitoring setup complete!** 🎉

