# Queue Workers Setup Guide

This guide explains how to configure and manage queue workers for Graphic School 2.0.

---

## Overview

Graphic School uses Redis queues for:
- Email sending
- Notifications
- Payment webhooks
- Background jobs
- Scheduled tasks

---

## Supervisor Configuration

Supervisor manages queue workers to ensure they're always running.

### Configuration File

Location: `/etc/supervisor/conf.d/graphic-school-workers.conf`

See `supervisor.conf.example` for full configuration.

### Worker Types

1. **Default Worker** (`graphic-school-worker`)
   - Handles general queue jobs
   - 2 processes
   - Queue: `default`

2. **Email Worker** (`graphic-school-email-worker`)
   - Handles email sending
   - 1 process
   - Queue: `emails`

3. **Notification Worker** (`graphic-school-notification-worker`)
   - Handles in-app notifications
   - 1 process
   - Queue: `notifications`

4. **Payment Worker** (`graphic-school-payment-worker`)
   - Handles payment webhooks
   - 1 process
   - Queue: `payments`
   - Higher priority (5 retries)

5. **Scheduler** (`graphic-school-scheduler`)
   - Runs Laravel scheduled tasks
   - 1 process
   - Replaces cron for `schedule:run`

---

## Setup Instructions

### 1. Install Supervisor

```bash
sudo apt install supervisor
```

### 2. Create Configuration

```bash
sudo nano /etc/supervisor/conf.d/graphic-school-workers.conf
```

Copy content from `supervisor.conf.example`.

### 3. Reload Supervisor

```bash
# Read new configuration
sudo supervisorctl reread

# Update supervisor
sudo supervisorctl update

# Start all workers
sudo supervisorctl start graphic-school-worker:*
sudo supervisorctl start graphic-school-email-worker:*
sudo supervisorctl start graphic-school-notification-worker:*
sudo supervisorctl start graphic-school-payment-worker:*
sudo supervisorctl start graphic-school-scheduler:*
```

---

## Management Commands

### Check Status

```bash
# Check all workers
sudo supervisorctl status

# Check specific worker
sudo supervisorctl status graphic-school-worker:*
```

### Restart Workers

```bash
# Restart all workers
sudo supervisorctl restart all

# Restart specific worker
sudo supervisorctl restart graphic-school-worker:*

# Restart all graphic-school workers
sudo supervisorctl restart graphic-school-worker:* graphic-school-email-worker:* graphic-school-notification-worker:* graphic-school-payment-worker:*
```

### Stop Workers

```bash
# Stop all workers
sudo supervisorctl stop all

# Stop specific worker
sudo supervisorctl stop graphic-school-worker:*
```

### View Logs

```bash
# Default worker logs
tail -f /var/www/graphic-school/graphic-school-api/storage/logs/worker.log

# Email worker logs
tail -f /var/www/graphic-school/graphic-school-api/storage/logs/email-worker.log

# Notification worker logs
tail -f /var/www/graphic-school/graphic-school-api/storage/logs/notification-worker.log

# Payment worker logs
tail -f /var/www/graphic-school/graphic-school-api/storage/logs/payment-worker.log

# Scheduler logs
tail -f /var/www/graphic-school/graphic-school-api/storage/logs/scheduler.log
```

---

## Queue Configuration

### Environment Variables

In `.env`:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
QUEUE_WORKER_TIMEOUT=300
QUEUE_MAX_TRIES=3
```

### Queue Names

- `default` - General jobs
- `emails` - Email sending
- `notifications` - In-app notifications
- `payments` - Payment webhooks

---

## Dispatching Jobs to Queues

### In Code

```php
// Default queue
dispatch(new SendEmailJob($user));

// Specific queue
SendEmailJob::dispatch($user)->onQueue('emails');

// High priority
SendEmailJob::dispatch($user)->onQueue('payments');
```

---

## Monitoring

### Check Queue Size

```bash
# Using Redis CLI
redis-cli
> LLEN queues:default
> LLEN queues:emails
> LLEN queues:notifications
> LLEN queues:payments
```

### Failed Jobs

```bash
# View failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {job-id}

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

## Troubleshooting

### Workers Not Starting

```bash
# Check supervisor logs
sudo tail -f /var/log/supervisor/supervisord.log

# Check worker logs
tail -f /var/www/graphic-school/graphic-school-api/storage/logs/worker.log

# Restart supervisor
sudo systemctl restart supervisor
```

### High Memory Usage

```bash
# Reduce number of processes
# Edit supervisor config
sudo nano /etc/supervisor/conf.d/graphic-school-workers.conf
# Change numprocs=2 to numprocs=1

# Reload
sudo supervisorctl reread
sudo supervisorctl update
```

### Jobs Stuck in Queue

```bash
# Clear queue (use with caution)
php artisan queue:clear

# Restart workers
sudo supervisorctl restart all
```

---

## Best Practices

1. **Monitor Queue Size**
   - Set up alerts for queue size > 1000
   - Monitor failed jobs daily

2. **Worker Scaling**
   - Start with 1-2 workers per queue
   - Scale based on load
   - Monitor CPU and memory

3. **Error Handling**
   - Set appropriate `--tries` value
   - Implement retry logic in jobs
   - Log all failures

4. **Performance**
   - Use `--sleep` to reduce CPU usage
   - Set `--max-time` to prevent memory leaks
   - Monitor worker logs for errors

---

## Cron Alternative

Instead of using cron for Laravel scheduler, use supervisor:

```ini
[program:graphic-school-scheduler]
command=php /var/www/graphic-school/graphic-school-api/artisan schedule:work
```

This runs the scheduler continuously (recommended for production).

---

**Queue workers setup complete!** 🎉

