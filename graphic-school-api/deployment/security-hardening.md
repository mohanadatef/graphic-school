# Production Security Hardening Guide

This guide covers final security hardening for Graphic School 2.0 production deployment.

---

## 1. Environment Variables

### Critical Checks

```bash
# Verify APP_DEBUG is false
grep APP_DEBUG .env
# Should output: APP_DEBUG=false

# Verify APP_ENV is production
grep APP_ENV .env
# Should output: APP_ENV=production
```

### Enforce in Code

Add to `app/Providers/AppServiceProvider.php`:

```php
public function boot()
{
    // Enforce APP_DEBUG=false in production
    if (app()->environment('production')) {
        config(['app.debug' => false]);
    }
}
```

---

## 2. Nginx Security Headers

Already configured in `nginx.conf.example`:

- ✅ X-Frame-Options: DENY
- ✅ X-Content-Type-Options: nosniff
- ✅ X-XSS-Protection: 1; mode=block
- ✅ Referrer-Policy: strict-origin-when-cross-origin
- ✅ Strict-Transport-Security: max-age=31536000

### Additional Headers

Add to Nginx config:

```nginx
# Content Security Policy
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data: https:; connect-src 'self' https://api.qrserver.com;" always;

# Permissions Policy
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
```

---

## 3. Disable Directory Listing

Already in Nginx config:

```nginx
autoindex off;
```

---

## 4. CSRF Protection

### Verify CSRF Middleware

Check `app/Http/Kernel.php`:

```php
'web' => [
    \App\Http\Middleware\VerifyCsrfToken::class,
    // ...
],
```

### Exclude API Routes

In `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    // API routes are excluded (using Sanctum)
    // Only exclude if necessary
];
```

---

## 5. File Upload Validation

### Controller Validation

Example in upload controller:

```php
$request->validate([
    'file' => [
        'required',
        'file',
        'max:10240', // 10MB
        'mimes:jpg,jpeg,png,pdf,doc,docx',
        function ($attribute, $value, $fail) {
            $mimeType = $value->getMimeType();
            $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (!in_array($mimeType, $allowedMimes)) {
                $fail('Invalid file type.');
            }
        },
    ],
]);
```

### Storage Validation

Add to `config/filesystems.php`:

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'visibility' => 'private',
    ],
    
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'permissions' => [
            'file' => [
                'public' => 0644,
                'private' => 0600,
            ],
            'dir' => [
                'public' => 0755,
                'private' => 0700,
            ],
        ],
    ],
],
```

---

## 6. Rate Limiting

### Auth Endpoints

Already configured in `routes/api.php`:

```php
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1'); // 5 attempts per minute

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

### Password Reset

Add to `routes/api.php`:

```php
Route::post('/password/email', [AuthController::class, 'sendResetLink'])
    ->middleware('throttle:5,1');

Route::post('/password/reset', [AuthController::class, 'reset'])
    ->middleware('throttle:5,1');
```

### Public Community Endpoints

Add to community routes:

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/community/posts', [CommunityController::class, 'index']);
    Route::get('/community/posts/{id}', [CommunityController::class, 'show']);
});
```

---

## 7. SQL Injection Protection

### Use Query Builder

✅ Already using Laravel Query Builder (protected)

### Parameterized Queries

Example:

```php
// ✅ Safe
DB::table('users')->where('email', $email)->first();

// ❌ Unsafe (never do this)
DB::select("SELECT * FROM users WHERE email = '{$email}'");
```

---

## 8. XSS Protection

### Input Sanitization

Already implemented in `InputSanitizationMiddleware`.

### Output Escaping

Laravel Blade automatically escapes:

```blade
{{ $userInput }} {{-- Automatically escaped --}}
{!! $trustedHtml !!} {{-- Only for trusted HTML --}}
```

---

## 9. Password Security

### Hashing

✅ Using bcrypt (10 rounds) - default Laravel

### Password Policy

Add validation:

```php
'password' => [
    'required',
    'string',
    'min:8',
    'regex:/[a-z]/',
    'regex:/[A-Z]/',
    'regex:/[0-9]/',
    'regex:/[@$!%*#?&]/',
],
```

---

## 10. Session Security

### Configuration

In `.env`:

```env
SESSION_DRIVER=redis
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
COOKIE_DOMAIN=.yourdomain.com
```

### Session Timeout

In `config/session.php`:

```php
'lifetime' => 120, // 2 hours
'expire_on_close' => false,
```

---

## 11. API Security

### Token Expiration

Configure in `config/sanctum.php`:

```php
'expiration' => 60 * 24, // 24 hours
```

### CORS Configuration

Already configured in `CorsMiddleware`.

---

## 12. Server Security

### Firewall

```bash
# Enable UFW
sudo ufw enable

# Allow only necessary ports
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS

# Deny all other traffic
sudo ufw default deny incoming
sudo ufw default allow outgoing
```

### SSH Hardening

```bash
# Disable root login
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no

# Use key authentication only
# Set: PasswordAuthentication no

# Restart SSH
sudo systemctl restart sshd
```

### Fail2ban (Optional)

```bash
# Install
sudo apt install fail2ban

# Configure
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local

# Enable
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## 13. Database Security

### Strong Passwords

```bash
# Generate strong password
openssl rand -base64 32
```

### Limit Access

```sql
-- Only allow local connections
-- In /etc/mysql/mysql.conf.d/mysqld.cnf
bind-address = 127.0.0.1
```

### Regular Backups

```bash
# Daily backup script
cat > /usr/local/bin/backup-db.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u graphic_school_user -p'password' graphic_school_prod > /backups/db_$DATE.sql
# Keep only last 30 days
find /backups -name "db_*.sql" -mtime +30 -delete
EOF

chmod +x /usr/local/bin/backup-db.sh

# Add to crontab
echo "0 2 * * * /usr/local/bin/backup-db.sh" | crontab -
```

---

## 14. File Permissions

### Correct Permissions

```bash
# Application files
sudo chown -R www-data:www-data /var/www/graphic-school
sudo chmod -R 755 /var/www/graphic-school

# Storage and cache
sudo chmod -R 775 /var/www/graphic-school/graphic-school-api/storage
sudo chmod -R 775 /var/www/graphic-school/graphic-school-api/bootstrap/cache

# Protect .env
sudo chmod 600 /var/www/graphic-school/graphic-school-api/.env
```

---

## 15. Security Checklist

### Pre-Launch

- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Strong database passwords
- [ ] SSL certificate installed
- [ ] Security headers configured
- [ ] Rate limiting on auth endpoints
- [ ] File upload validation
- [ ] CSRF protection enabled
- [ ] Directory listing disabled
- [ ] Firewall configured
- [ ] SSH hardened
- [ ] File permissions correct
- [ ] .env file secured (600)
- [ ] Regular backups scheduled
- [ ] Monitoring enabled

### Post-Launch

- [ ] Monitor error logs daily
- [ ] Review failed login attempts
- [ ] Check queue sizes
- [ ] Monitor disk space
- [ ] Review security alerts
- [ ] Update dependencies regularly
- [ ] Test backups regularly

---

## 16. Security Headers Verification

### Test Headers

```bash
curl -I https://yourdomain.com | grep -i "x-"
```

Should show:
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Strict-Transport-Security: max-age=31536000

---

## 17. Regular Security Updates

### System Updates

```bash
# Weekly updates
sudo apt update && sudo apt upgrade -y
```

### Laravel Updates

```bash
# Check for updates
composer outdated

# Update dependencies
composer update --no-dev
```

---

**Security hardening complete!** 🎉

