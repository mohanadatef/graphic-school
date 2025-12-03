# Monitoring Guide

## Overview

This document covers monitoring strategies for Graphic School 2.0, including system monitoring, application monitoring, and alerting.

## System Monitoring

### Server Metrics

#### CPU Monitoring

Monitor CPU usage:
```bash
# Real-time monitoring
top
htop

# Historical data
sar -u 1 10
```

Alerts:
- CPU > 80% for 5 minutes
- CPU > 90% for 1 minute

#### Memory Monitoring

Monitor memory usage:
```bash
# Current usage
free -h

# Detailed info
cat /proc/meminfo
```

Alerts:
- Memory > 85%
- Swap usage > 50%

#### Disk Monitoring

Monitor disk usage:
```bash
# Disk space
df -h

# Disk I/O
iostat -x 1
```

Alerts:
- Disk usage > 80%
- Disk I/O wait > 50ms

#### Network Monitoring

Monitor network:
```bash
# Network stats
netstat -i
iftop
```

Alerts:
- Bandwidth > 80%
- Network errors > 1%

### Database Monitoring

#### MySQL Metrics

Monitor MySQL:
```sql
-- Connection count
SHOW STATUS LIKE 'Threads_connected';

-- Query performance
SHOW STATUS LIKE 'Slow_queries';

-- Table sizes
SELECT table_name, 
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE table_schema = 'graphic_school'
ORDER BY size_mb DESC;
```

Alerts:
- Connections > 80% of max
- Slow queries > 10/minute
- Replication lag > 5 seconds

## Application Monitoring

### Health Checks

#### Health Endpoint

Monitor health endpoint:
```bash
curl https://yourdomain.com/api/health
```

Response:
```json
{
  "status": "healthy",
  "database": "connected",
  "cache": "connected",
  "queue": "running"
}
```

#### Uptime Monitoring

- Monitor every 1 minute
- Alert on downtime
- Track uptime percentage

### Error Monitoring

#### Laravel Logs

Monitor error logs:
```bash
tail -f storage/logs/laravel.log
```

#### Error Tracking

Use Sentry or similar:
```env
SENTRY_LARAVEL_DSN=your_sentry_dsn
```

### Performance Monitoring

#### Response Times

Monitor API response times:
- Average response time
- 95th percentile
- 99th percentile

#### Database Queries

Monitor query performance:
- Slow query log
- Query execution time
- Query frequency

## Monitoring Tools

### Prometheus + Grafana

#### Setup Prometheus

```yaml
# prometheus.yml
global:
  scrape_interval: 15s

scrape_configs:
  - job_name: 'graphic-school'
    static_configs:
      - targets: ['localhost:8000']
```

#### Setup Grafana

- Import dashboards
- Configure alerts
- Visualize metrics

### New Relic

#### Setup

```bash
composer require newrelic/monolog-enricher
```

Configure:
```php
// config/logging.php
'newrelic' => [
    'driver' => 'monolog',
    'handler' => NewrelicMonologEnricher\NewrelicHandler::class,
],
```

### Datadog

#### Setup

Install agent:
```bash
DD_API_KEY=your_key bash -c "$(curl -L https://s3.amazonaws.com/dd-agent/scripts/install_script_agent7.sh)"
```

Configure application:
```env
DD_AGENT_HOST=localhost
DD_TRACE_AGENT_PORT=8126
```

## Alerting

### Alert Rules

#### Critical Alerts

- System down
- Database down
- Disk full
- High error rate

#### Warning Alerts

- High CPU usage
- High memory usage
- Slow response times
- Queue backup

### Notification Channels

- Email
- Slack
- PagerDuty
- SMS

### Alert Configuration

```yaml
# Alert rules
groups:
  - name: graphic_school
    rules:
      - alert: HighCPUUsage
        expr: cpu_usage > 80
        for: 5m
        annotations:
          summary: "High CPU usage detected"
```

## Log Management

### Log Aggregation

#### Centralized Logging

Use ELK Stack:
- Elasticsearch
- Logstash
- Kibana

#### Log Rotation

Configure log rotation:
```bash
# /etc/logrotate.d/graphic-school
/var/www/graphic-school/graphic-school-api/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### Log Analysis

#### Error Analysis

- Count errors by type
- Track error trends
- Identify patterns

#### Performance Analysis

- Analyze slow requests
- Identify bottlenecks
- Track improvements

## Dashboard

### System Dashboard

Metrics to display:
- CPU usage
- Memory usage
- Disk usage
- Network traffic
- Database connections

### Application Dashboard

Metrics to display:
- Request rate
- Response times
- Error rate
- Active users
- Queue length

### Business Dashboard

Metrics to display:
- Enrollments
- Active courses
- Attendance rate
- User growth

## Best Practices

1. **Comprehensive Monitoring**
   - Monitor all systems
   - Track key metrics
   - Set appropriate alerts

2. **Proactive Alerts**
   - Alert before issues
   - Set meaningful thresholds
   - Avoid alert fatigue

3. **Regular Review**
   - Review metrics weekly
   - Analyze trends
   - Optimize based on data

4. **Documentation**
   - Document monitoring setup
   - Document alert procedures
   - Keep runbooks updated

## Conclusion

Monitoring provides:
- System visibility
- Performance insights
- Proactive alerting
- Data-driven optimization

Comprehensive monitoring is essential for production systems.

