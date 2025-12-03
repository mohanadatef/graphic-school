# Health Checks

## Overview

Health checks monitor system status and ensure all components are functioning correctly. This document describes health check endpoints and monitoring procedures.

## Health Check Endpoint

### API Endpoint

`GET /api/health`

### Response Format

```json
{
  "status": "healthy",
  "timestamp": "2025-01-20T12:00:00Z",
  "services": {
    "database": {
      "status": "connected",
      "response_time": "5ms"
    },
    "cache": {
      "status": "connected",
      "response_time": "2ms"
    },
    "queue": {
      "status": "running",
      "pending_jobs": 0
    },
    "storage": {
      "status": "accessible",
      "free_space": "50GB"
    }
  },
  "version": "2.0.0"
}
```

## Health Check Components

### Database Health

Checks:
- Database connection
- Query execution
- Response time

Implementation:
```php
try {
    DB::connection()->getPdo();
    $responseTime = $this->measureQueryTime();
    return ['status' => 'connected', 'response_time' => $responseTime];
} catch (\Exception $e) {
    return ['status' => 'disconnected', 'error' => $e->getMessage()];
}
```

### Cache Health

Checks:
- Cache connection
- Read/write operations
- Response time

Implementation:
```php
try {
    Cache::put('health_check', 'ok', 1);
    $value = Cache::get('health_check');
    $responseTime = $this->measureCacheTime();
    return ['status' => 'connected', 'response_time' => $responseTime];
} catch (\Exception $e) {
    return ['status' => 'disconnected', 'error' => $e->getMessage()];
}
```

### Queue Health

Checks:
- Queue connection
- Worker status
- Pending jobs

Implementation:
```php
try {
    $pendingJobs = Queue::size();
    $workersRunning = $this->checkWorkers();
    return [
        'status' => $workersRunning ? 'running' : 'stopped',
        'pending_jobs' => $pendingJobs
    ];
} catch (\Exception $e) {
    return ['status' => 'error', 'error' => $e->getMessage()];
}
```

### Storage Health

Checks:
- Storage accessibility
- Free space
- Write permissions

Implementation:
```php
try {
    $freeSpace = disk_free_space(storage_path());
    $writable = is_writable(storage_path());
    return [
        'status' => $writable ? 'accessible' : 'readonly',
        'free_space' => $this->formatBytes($freeSpace)
    ];
} catch (\Exception $e) {
    return ['status' => 'inaccessible', 'error' => $e->getMessage()];
}
```

## Health Status Levels

### Healthy

All services operational:
- Database: Connected
- Cache: Connected
- Queue: Running
- Storage: Accessible

### Degraded

Some services have issues:
- Database: Slow responses
- Cache: Intermittent failures
- Queue: High pending jobs

### Unhealthy

Critical services down:
- Database: Disconnected
- Cache: Disconnected
- Queue: Stopped
- Storage: Inaccessible

## Monitoring Integration

### Uptime Monitoring

Configure monitoring service:
- Check `/api/health` every 1 minute
- Alert on unhealthy status
- Track uptime percentage

### Alerting

Alert conditions:
- Status: unhealthy
- Response time > 5 seconds
- Database disconnected
- Queue stopped

### Dashboards

Display health metrics:
- Current status
- Service response times
- Historical trends
- Alert history

## Automated Health Checks

### Cron Job

Run health check:
```bash
# Every 5 minutes
*/5 * * * * curl -s https://yourdomain.com/api/health > /dev/null
```

### Script

Health check script:
```bash
#!/bin/bash
RESPONSE=$(curl -s https://yourdomain.com/api/health)
STATUS=$(echo $RESPONSE | jq -r '.status')

if [ "$STATUS" != "healthy" ]; then
    # Send alert
    echo "Health check failed: $STATUS"
    exit 1
fi
```

## Health Check Best Practices

1. **Comprehensive Checks**
   - Check all critical services
   - Verify functionality
   - Measure performance

2. **Fast Response**
   - Optimize health checks
   - Cache results when appropriate
   - Minimize external calls

3. **Meaningful Status**
   - Clear status indicators
   - Detailed error messages
   - Actionable information

4. **Regular Monitoring**
   - Check frequently
   - Alert on issues
   - Track trends

## Conclusion

Health checks provide:
- System status visibility
- Early problem detection
- Monitoring integration
- Automated alerting

Regular health checks ensure system reliability and quick problem resolution.

