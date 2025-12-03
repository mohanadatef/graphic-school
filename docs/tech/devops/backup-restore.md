# Backup and Restore Guide

## Overview

This document covers backup and restore procedures for Graphic School 2.0, including database backups, file backups, and disaster recovery.

## Backup Strategy

### Backup Types

1. **Full Backup:** Complete system backup
2. **Incremental Backup:** Changes since last backup
3. **Differential Backup:** Changes since last full backup

### Backup Frequency

- **Database:** Daily full backups
- **Files:** Weekly full backups
- **Configuration:** On changes
- **Logs:** Monthly archives

## Database Backup

### Automated Backup Script

Create `/usr/local/bin/backup-db.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/database"
DB_NAME="graphic_school"
DB_USER="gs_user"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete

# Upload to S3 (optional)
# aws s3 cp $BACKUP_DIR/db_$DATE.sql.gz s3://your-bucket/backups/database/
```

Make executable:
```bash
chmod +x /usr/local/bin/backup-db.sh
```

### Cron Schedule

Add to crontab:
```bash
# Daily backup at 2 AM
0 2 * * * /usr/local/bin/backup-db.sh
```

### Laravel Backup Package

Using `spatie/laravel-backup`:

```bash
composer require spatie/laravel-backup
```

Configure in `config/backup.php`:
```php
'backup' => [
    'name' => env('APP_NAME', 'Graphic School'),
    'source' => [
        'databases' => [
            'mysql',
        ],
        'files' => [
            base_path('storage/app'),
        ],
    ],
    'destination' => [
        'disks' => ['local', 's3'],
    ],
],
```

Schedule backup:
```php
// In app/Console/Kernel.php
$schedule->command('backup:run')->daily()->at('02:00');
```

## File Backup

### Storage Backup Script

Create `/usr/local/bin/backup-files.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/files"
APP_DIR="/var/www/graphic-school/graphic-school-api/storage/app"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup storage
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR .

# Keep only last 90 days
find $BACKUP_DIR -name "files_*.tar.gz" -mtime +90 -delete

# Upload to S3 (optional)
# aws s3 cp $BACKUP_DIR/files_$DATE.tar.gz s3://your-bucket/backups/files/
```

### Cron Schedule

```bash
# Weekly backup on Sunday at 3 AM
0 3 * * 0 /usr/local/bin/backup-files.sh
```

## Configuration Backup

### Environment Backup

```bash
# Backup .env file
cp /var/www/graphic-school/graphic-school-api/.env /backups/config/.env.$(date +%Y%m%d)
```

### Nginx Configuration

```bash
# Backup Nginx config
tar -czf /backups/config/nginx_$(date +%Y%m%d).tar.gz /etc/nginx/
```

## Off-Site Backup

### S3 Backup

Configure AWS CLI:
```bash
aws configure
```

Upload backups:
```bash
aws s3 sync /backups/ s3://your-bucket/backups/
```

### Automated S3 Sync

```bash
# Sync daily
0 4 * * * aws s3 sync /backups/ s3://your-bucket/backups/
```

## Restore Procedures

### Database Restore

#### From SQL Dump

```bash
# Uncompress if needed
gunzip db_20250120_020000.sql.gz

# Restore database
mysql -u gs_user -p graphic_school < db_20250120_020000.sql
```

#### From Laravel Backup

```bash
# List backups
php artisan backup:list

# Restore backup
php artisan backup:restore backup-name.zip
```

### File Restore

```bash
# Extract backup
tar -xzf files_20250120_030000.tar.gz -C /var/www/graphic-school/graphic-school-api/storage/app/
```

### Full System Restore

1. Restore database
2. Restore files
3. Restore configuration
4. Run migrations
5. Clear caches
6. Test functionality

## Backup Verification

### Verify Backups

```bash
# Test database backup
gunzip -t db_20250120_020000.sql.gz

# Test file backup
tar -tzf files_20250120_030000.tar.gz > /dev/null
```

### Regular Testing

- Test restore monthly
- Verify backup integrity
- Test disaster recovery
- Document procedures

## Disaster Recovery

### Recovery Plan

1. **Assess Damage**
   - Identify affected systems
   - Determine data loss
   - Estimate downtime

2. **Restore Systems**
   - Restore from backups
   - Verify data integrity
   - Test functionality

3. **Resume Operations**
   - Monitor systems
   - Verify backups
   - Update procedures

### Recovery Time Objectives (RTO)

- **Database:** 1 hour
- **Files:** 2 hours
- **Full System:** 4 hours

### Recovery Point Objectives (RPO)

- **Database:** 24 hours
- **Files:** 7 days
- **Configuration:** On change

## Backup Best Practices

1. **Automate Backups**
   - Use cron jobs
   - Verify automation
   - Monitor backup success

2. **Multiple Locations**
   - Local backups
   - Off-site backups
   - Cloud backups

3. **Encryption**
   - Encrypt sensitive backups
   - Secure backup storage
   - Protect backup access

4. **Testing**
   - Test restores regularly
   - Verify backup integrity
   - Document procedures

5. **Monitoring**
   - Monitor backup success
   - Alert on failures
   - Review backup logs

## Conclusion

Backup and restore procedures ensure:
- Data protection
- Disaster recovery
- Business continuity
- Peace of mind

Regular backups and tested restore procedures are essential for production systems.

