# Setup Guide

## Overview

This guide covers setting up Graphic School 2.0 LMC system for local development. It includes backend and frontend setup, database configuration, and first-time initialization.

## Prerequisites

- **PHP:** 8.2 or higher
- **Composer:** Latest version
- **Node.js:** 18.x or higher
- **npm:** 9.x or higher
- **MySQL/MariaDB:** 8.0+ or 10.6+
- **Git:** Latest version

## Backend Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd graphic-school-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Configure Environment Variables

Edit `.env` file with your settings:

```env
APP_NAME="Graphic School"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graphic_school
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=local
# Or for S3:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=
# AWS_BUCKET=
```

### 5. Create Database

Create MySQL database:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE graphic_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed Database (Optional)

Seed with default data:

```bash
php artisan db:seed
```

Or seed specific seeders:

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=LanguageSeeder
php artisan db:seed --class=CurrencySeeder
```

### 8. Create Storage Link

```bash
php artisan storage:link
```

### 9. Start Development Server

```bash
php artisan serve
```

Backend API will be available at: `http://localhost:8000`

## Frontend Setup

### 1. Navigate to Frontend Directory

```bash
cd ../graphic-school-frontend
```

### 2. Install Dependencies

```bash
npm install
```

### 3. Environment Configuration

Create `.env` file:

```env
VITE_API_URL=http://localhost:8000/api
VITE_APP_NAME="Graphic School"
```

### 4. Start Development Server

```bash
npm run dev
```

Frontend will be available at: `http://localhost:5173` (or configured port)

## First-Time Setup

### 1. Access Setup Wizard

When you first open the frontend, you'll be redirected to the Setup Wizard.

### 2. Setup Steps

**Step 1: General Information**
- Academy name
- Country selection
- Timezone selection

**Step 2: Branding**
- Upload logo
- Select main color
- Choose fonts

**Step 3: Languages**
- Default language: English (en)
- Add additional languages (e.g., Arabic - ar)
- RTL support for Arabic

**Step 4: Currency**
- Default currency: EGP
- Add additional currencies

**Step 5: Pages Activation**
- Activate/deactivate pages:
  - Home
  - About
  - Courses
  - Instructors
  - FAQ
  - Contact

**Step 6: Launch**
- Activate website
- System goes live

### 3. Create Admin User

After setup, create an admin user:

```bash
php artisan tinker
```

```php
$role = \Modules\ACL\Roles\Models\Role::where('name', 'admin')->first();
$user = \Modules\ACL\Users\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role_id' => $role->id,
]);
```

Or use seeder (if available):

```bash
php artisan db:seed --class=AdminUserSeeder
```

### 4. Login and Configure

1. Login with admin credentials
2. Access admin dashboard
3. Start creating courses, groups, and managing the system

## Development Workflow

### Backend Development

```bash
# Run migrations
php artisan migrate

# Run specific migration
php artisan migrate --path=/database/migrations/2025_01_28_200000_update_certificates_table_for_business_spec.php

# Rollback last migration
php artisan migrate:rollback

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run tests
php artisan test
```

### Frontend Development

```bash
# Start dev server
npm run dev

# Build for production
npm run build

# Run tests
npm run test

# Run E2E tests
npm run test:e2e
```

### Database Seeding

```bash
# Seed all seeders
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=CourseSeeder

# Refresh and seed
php artisan migrate:fresh --seed
```

## Common Setup Issues

### Issue: Database Connection Failed

**Solution:**
1. Check `.env` database credentials
2. Verify MySQL is running: `sudo systemctl status mysql`
3. Test connection: `mysql -u root -p -e "SELECT 1"`

### Issue: Permission Denied (Storage)

**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Issue: Node Modules Not Found

**Solution:**
```bash
rm -rf node_modules package-lock.json
npm install
```

### Issue: Composer Dependencies Not Installed

**Solution:**
```bash
composer install --no-interaction
```

### Issue: API Routes Not Working

**Solution:**
1. Check `.env` API_URL matches backend URL
2. Clear route cache: `php artisan route:clear`
3. Check CORS configuration

## Environment Variables Reference

### Backend (.env)

**Application:**
- `APP_NAME` - Application name
- `APP_ENV` - Environment (local, production)
- `APP_KEY` - Application key
- `APP_DEBUG` - Debug mode (true/false)
- `APP_URL` - Application URL

**Database:**
- `DB_CONNECTION` - Database driver (mysql)
- `DB_HOST` - Database host
- `DB_PORT` - Database port (3306)
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

**Mail:**
- `MAIL_MAILER` - Mail driver (smtp, mailpit, etc.)
- `MAIL_HOST` - SMTP host
- `MAIL_PORT` - SMTP port
- `MAIL_FROM_ADDRESS` - From email address
- `MAIL_FROM_NAME` - From name

**File Storage:**
- `FILESYSTEM_DISK` - Storage disk (local, s3)
- AWS credentials (if using S3)

### Frontend (.env)

**API:**
- `VITE_API_URL` - Backend API URL

**Application:**
- `VITE_APP_NAME` - Application name

## Post-Setup Configuration

### 1. Configure Mail

For local development, use Mailpit:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

Mailpit UI: `http://localhost:8025`

### 2. Configure File Storage

**Local Storage:**
```env
FILESYSTEM_DISK=local
```

Files stored in: `storage/app/public/`

**S3 Storage (Production):**
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

### 3. Configure Queue (Optional)

For async jobs, configure queue:

```env
QUEUE_CONNECTION=database
# Or
QUEUE_CONNECTION=redis
```

Run queue worker:

```bash
php artisan queue:work
```

### 4. Configure Scheduler (Optional)

For scheduled tasks, add to crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Testing

### Backend Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter EnrollmentTest

# Run with coverage
php artisan test --coverage
```

### Frontend Tests

```bash
# Unit tests
npm run test:unit

# E2E tests
npm run test:e2e

# E2E with UI
npm run test:e2e:open
```

## Troubleshooting

### Clear All Caches

```bash
php artisan optimize:clear
```

### Reset Database

```bash
php artisan migrate:fresh --seed
```

**Warning:** This will delete all data!

### Check Logs

```bash
# Backend logs
tail -f storage/logs/laravel.log

# Frontend errors
# Check browser console
```

## Next Steps

After setup:

1. ✅ Complete Setup Wizard
2. ✅ Create admin user
3. ✅ Create a course
4. ✅ Create a group
5. ✅ Create sessions
6. ✅ Test enrollment flow
7. ✅ Test attendance marking
8. ✅ Issue a certificate
9. ✅ Test public verification

---

**Setup Status:** ✅ Ready for development

