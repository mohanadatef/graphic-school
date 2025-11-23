# Production Launch Checklist

Use this checklist before launching Graphic School 2.0 to production.

---

## Backend Checklist

### Environment Configuration
- [ ] `.env` file configured with production values
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` set to production domain
- [ ] Database credentials configured
- [ ] Redis configured
- [ ] Mail service configured
- [ ] Payment gateway keys configured
- [ ] File storage configured (S3 or local)
- [ ] All API keys in `.env` (not hardcoded)

### Database
- [ ] All migrations run (NO fresh - incremental only)
- [ ] Database seeded (if needed)
- [ ] Database backups configured
- [ ] Database user has correct permissions
- [ ] Database connection tested

### Application
- [ ] `php artisan config:cache` run
- [ ] `php artisan route:cache` run
- [ ] `php artisan view:cache` run
- [ ] `php artisan event:cache` run
- [ ] `php artisan optimize` run
- [ ] Application key generated
- [ ] Storage link created (`php artisan storage:link`)

### Queues & Workers
- [ ] Supervisor configured
- [ ] Queue workers running
- [ ] Email worker running
- [ ] Notification worker running
- [ ] Payment worker running
- [ ] Scheduler running
- [ ] Queue connection set to `redis`
- [ ] Failed jobs table exists

### Cron Jobs
- [ ] Laravel scheduler configured
- [ ] Cron job active: `* * * * * php artisan schedule:run`
- [ ] Or supervisor scheduler running

### Email Service
- [ ] Email service configured (SendGrid/Mailgun/SMTP)
- [ ] Domain verified
- [ ] DNS records added (SPF, DKIM, DMARC)
- [ ] Test email sent successfully
- [ ] Email verification routes enabled (if needed)

### Payment Gateway
- [ ] Payment gateway configured (Stripe/Paymob)
- [ ] Live API keys set
- [ ] Webhook endpoints configured
- [ ] Webhook signature verification working
- [ ] Test payment processed successfully
- [ ] Invoice generation tested

### Caching
- [ ] Cache driver set to `redis`
- [ ] Cache cleared and warmed
- [ ] Config cache built
- [ ] Route cache built
- [ ] View cache built

### Logs
- [ ] Log rotation configured
- [ ] Log level set to `error` (production)
- [ ] Log directory writable
- [ ] No errors in current logs

### Security
- [ ] Security headers configured
- [ ] Rate limiting on auth endpoints
- [ ] CSRF protection enabled
- [ ] File upload validation
- [ ] Directory listing disabled
- [ ] SSL certificate installed
- [ ] Firewall configured
- [ ] File permissions correct

---

## Frontend Checklist

### Build
- [ ] Frontend built for production (`npm run build`)
- [ ] All pages responsive
- [ ] No console errors
- [ ] No broken links
- [ ] No missing images
- [ ] All assets loading correctly

### Branding
- [ ] Branding colors loaded
- [ ] Branding fonts loaded
- [ ] Logo displayed correctly
- [ ] Theme toggle working

### SEO
- [ ] Meta tags correct
- [ ] Open Graph tags (if implemented)
- [ ] Sitemap generated (if implemented)
- [ ] Robots.txt configured

### Public Pages
- [ ] Homepage renders correctly
- [ ] Course pages render correctly
- [ ] Program pages render correctly
- [ ] Page Builder pages render correctly
- [ ] Public pages work in EN
- [ ] Public pages work in AR
- [ ] RTL layout correct for Arabic

### Page Builder
- [ ] Page Builder editor accessible
- [ ] Can create new pages
- [ ] Can add blocks
- [ ] Can save structure
- [ ] Can publish pages
- [ ] Public pages render correctly
- [ ] Works in EN
- [ ] Works in AR

---

## SaaS Features Checklist

### Academy Creation
- [ ] New academy can be created
- [ ] Academy gets default subscription (trial)
- [ ] Homepage auto-created
- [ ] Default branding applied

### Subscriptions
- [ ] Trial period working (14 days)
- [ ] Subscription status correct
- [ ] Usage tracking working
- [ ] Limit enforcement working
- [ ] Upgrade flow tested
- [ ] Downgrade flow tested
- [ ] Cancellation flow tested

### Usage Limits
- [ ] Student limit enforced
- [ ] Program limit enforced
- [ ] Batch limit enforced
- [ ] Group limit enforced
- [ ] Page limit enforced
- [ ] Storage limit enforced (if applicable)
- [ ] Error messages clear when limit reached

### Billing
- [ ] Invoice generation working
- [ ] Payment processing working
- [ ] Webhook handling working
- [ ] Invoice status updates correctly
- [ ] Payment history recorded

---

## QA Checklist

### Console Errors
- [ ] 0 console errors in browser
- [ ] 0 console warnings (critical)
- [ ] Network requests successful

### Broken Links
- [ ] All internal links work
- [ ] All external links work (if any)
- [ ] No 404 errors

### Missing Images
- [ ] All images load correctly
- [ ] Placeholder images work
- [ ] Avatar images display

### Performance
- [ ] Page load time < 3 seconds
- [ ] API response time < 1 second
- [ ] Images optimized
- [ ] CSS/JS minified

### Functionality
- [ ] User registration works
- [ ] User login works
- [ ] Password reset works (if enabled)
- [ ] Enrollment flow works
- [ ] Attendance marking works
- [ ] Assignment submission works
- [ ] Gradebook displays correctly
- [ ] Certificate generation works
- [ ] Community posts work
- [ ] Gamification XP awarded
- [ ] Leaderboard displays

---

## Monitoring Checklist

### Health Check
- [ ] `/api/health` endpoint returns 200
- [ ] Database check passes
- [ ] Redis check passes
- [ ] Cache check passes

### Error Monitoring
- [ ] Sentry configured (if using)
- [ ] Error alerts configured
- [ ] Log aggregation working (if using)

### Uptime Monitoring
- [ ] Uptime monitor configured
- [ ] Alert contacts set
- [ ] Test alert received

### Performance Monitoring
- [ ] APM tool configured (if using)
- [ ] Slow query log enabled
- [ ] Queue monitoring active

---

## Pre-Launch Final Steps

### 1. Final Backup
```bash
# Database backup
mysqldump -u user -p database > backup_pre_launch.sql

# Files backup
tar -czf files_backup_pre_launch.tar.gz /var/www/graphic-school
```

### 2. Test All Critical Paths
- [ ] User registration → Login → Dashboard
- [ ] Enrollment → Payment → Certificate
- [ ] Page Builder → Create → Publish → View
- [ ] Subscription → Trial → Upgrade

### 3. Load Test (Optional)
- [ ] Test with 10 concurrent users
- [ ] Test with 50 concurrent users
- [ ] Monitor server resources

### 4. Security Scan (Optional)
- [ ] Run security scanner
- [ ] Check for vulnerabilities
- [ ] Review OWASP Top 10

---

## Launch Day

### Morning
- [ ] Final backup
- [ ] Review logs
- [ ] Check monitoring
- [ ] Test critical paths
- [ ] Notify team

### Launch
- [ ] DNS pointed to production
- [ ] SSL certificate active
- [ ] All services running
- [ ] Health check passing
- [ ] Monitor for 1 hour

### Post-Launch
- [ ] Monitor error logs
- [ ] Monitor performance
- [ ] Check user registrations
- [ ] Verify payments
- [ ] Review feedback

---

## Rollback Plan

### If Issues Occur

1. **Immediate Rollback**
   ```bash
   # Restore previous version
   cd /var/www/graphic-school
   git checkout previous-stable-tag
   php artisan config:cache
   php artisan route:cache
   sudo supervisorctl restart all
   ```

2. **Database Rollback**
   ```bash
   # Restore database backup
   mysql -u user -p database < backup_pre_launch.sql
   ```

3. **Notify Team**
   - Alert development team
   - Document issue
   - Plan fix

---

## Post-Launch Monitoring (First 24 Hours)

- [ ] Check error logs every hour
- [ ] Monitor server resources
- [ ] Check queue sizes
- [ ] Verify payments processing
- [ ] Monitor user registrations
- [ ] Check email delivery
- [ ] Review user feedback

---

## Success Criteria

✅ **Launch is successful if:**
- All health checks passing
- No critical errors in logs
- Users can register and login
- Payments processing correctly
- All core features working
- Performance acceptable
- No security issues

---

**Ready for launch!** 🚀

