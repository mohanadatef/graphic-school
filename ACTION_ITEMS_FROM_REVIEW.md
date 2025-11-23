# 📋 قائمة الإجراءات المطلوبة - Action Items
## بناءً على مراجعة Product Manager الشاملة

**التاريخ**: 2025-01-27  
**الأولوية**: 🔴 حرج | 🟡 مهم | 🟢 تحسين

---

## 🔴 المرحلة 1: قبل الإنتاج (Critical - 1-2 أسبوع)

### 1. إضافة ملف `.env.example` 🔴
**الأهمية**: حرج جداً  
**الوصف**: إنشاء ملف `.env.example` يحتوي على جميع المتغيرات المطلوبة  
**الملف**: `graphic-school-api/.env.example`  
**المحتوى المطلوب**:
```env
APP_NAME="Graphic School"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=graphic_school
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:5173
SUPPORT_API_KEY=

# ... (راجع التقرير الكامل للقائمة الكاملة)
```

---

### 2. إضافة Security Headers Middleware 🔴
**الأهمية**: حرج  
**الوصف**: إضافة Security Headers لحماية التطبيق  
**الملف**: `graphic-school-api/app/Http/Middleware/SecurityHeadersMiddleware.php`

**الكود المطلوب**:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Only add HSTS in production
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
```

**التسجيل في Kernel.php**:
```php
protected $middleware = [
    // ... existing middleware
    \App\Http\Middleware\SecurityHeadersMiddleware::class,
];
```

---

### 3. تحسين Rate Limiting على Auth Endpoints 🔴
**الأهمية**: حرج  
**الوصف**: تقليل Rate Limit على Login/Register لحماية من Brute Force  
**الملف**: `graphic-school-api/routes/api.php`

**التعديل المطلوب**:
```php
// Before
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// After
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

---

### 4. نقل API Keys إلى .env 🔴
**الأهمية**: حرج  
**الوصف**: نقل `support_api_key` من Config إلى .env  
**الملف**: `graphic-school-api/config/app.php` أو ملف Config منفصل

**التعديل المطلوب**:
```php
// في config/app.php أو config/support.php
'support_api_key' => env('SUPPORT_API_KEY'),
```

**في .env**:
```env
SUPPORT_API_KEY=your-secret-api-key-here
```

---

### 5. إضافة Email Verification System 🟡
**الأهمية**: مهم  
**الوصف**: إضافة نظام التحقق من البريد الإلكتروني  
**المتطلبات**:
- Migration: `email_verified_at` column (موجود في users table)
- Middleware: `EnsureEmailIsVerified`
- Routes: Email verification endpoints
- Email Template: Verification email

**الخطوات**:
1. التأكد من وجود `email_verified_at` في users table
2. إضافة Middleware للتحقق من Email
3. إضافة Routes للتحقق
4. إضافة Email Template

---

## 🟡 المرحلة 2: بعد الإنتاج مباشرة (Important - 2-4 أسابيع)

### 6. إضافة Payment Integration 🟡
**الأهمية**: مهم للعمل التجاري  
**الوصف**: إضافة تكامل مع Stripe أو PayPal  
**المتطلبات**:
- Package: `stripe/stripe-php` أو `paypal/rest-api-sdk-php`
- Service: `PaymentGatewayService`
- Controller: `PaymentController` (موجود - يحتاج توسيع)
- Routes: Payment webhooks

---

### 7. إضافة Email Notifications System 🟡
**الأهمية**: مهم  
**الوصف**: إضافة نظام إشعارات عبر البريد الإلكتروني  
**المتطلبات**:
- Email Templates: Laravel Mail
- Events: User events (Registered, Enrolled, etc.)
- Listeners: Send email notifications
- Queue: للـ Background processing

---

### 8. إضافة API Versioning 🟡
**الأهمية**: مهم للتوسع المستقبلي  
**الوصف**: إضافة `/api/v1/` prefix  
**الملف**: `graphic-school-api/routes/api.php`

**التعديل المطلوب**:
```php
Route::prefix('v1')->group(function () {
    // All existing routes here
});
```

---

### 9. إضافة Frontend Tests 🟡
**الأهمية**: مهم للجودة  
**الوصف**: إضافة Tests للـ Frontend  
**المتطلبات**:
- Package: `vitest` أو `jest`
- Test Files: Unit tests للـ Components و Services
- E2E Tests: Playwright أو Cypress

---

### 10. إضافة OpenAPI/Swagger Documentation 🟡
**الأهمية**: مهم للمطورين  
**الوصف**: إضافة Swagger للتوثيق التفاعلي  
**المتطلبات**:
- Package: `darkaonline/l5-swagger`
- Configuration: Swagger config
- Annotations: في Controllers

---

## 🟢 المرحلة 3: تحسينات (Nice to Have - 1-2 شهر)

### 11. إضافة Docker Support 🟢
**الأهمية**: تحسين  
**الوصف**: إضافة Docker Configuration  
**المتطلبات**:
- `Dockerfile` للـ Backend
- `Dockerfile` للـ Frontend
- `docker-compose.yml` للـ Development
- `.dockerignore` files

---

### 12. إضافة CI/CD Pipeline 🟢
**الأهمية**: تحسين  
**الوصف**: إضافة Automated Testing و Deployment  
**المتطلبات**:
- GitHub Actions أو GitLab CI
- Automated Tests
- Automated Deployment
- Environment-specific configs

---

### 13. تحسين Caching Strategy 🟢
**الأهمية**: تحسين  
**الوصف**: إضافة Redis Caching للبيانات المتكررة  
**المتطلبات**:
- Redis Configuration
- Cache Tags للـ Modules
- Cache Invalidation Strategy

---

### 14. إضافة Performance Monitoring 🟢
**الأهمية**: تحسين  
**الوصف**: إضافة أدوات مراقبة الأداء  
**المتطلبات**:
- Laravel Telescope (Development)
- APM Tool (Production): New Relic, Datadog, etc.
- Error Tracking: Sentry

---

## 📊 ملخص الأولويات

### 🔴 حرج (يجب قبل الإنتاج):
1. ✅ ملف `.env.example`
2. ✅ Security Headers
3. ✅ Rate Limiting على Auth
4. ✅ نقل API Keys إلى .env

### 🟡 مهم (يُفضل بعد الإنتاج):
5. ✅ Email Verification
6. ✅ Payment Integration
7. ✅ Email Notifications
8. ✅ API Versioning
9. ✅ Frontend Tests
10. ✅ Swagger Documentation

### 🟢 تحسين (Nice to Have):
11. ✅ Docker Support
12. ✅ CI/CD Pipeline
13. ✅ Caching Strategy
14. ✅ Performance Monitoring

---

## 📝 ملاحظات التنفيذ

### ترتيب التنفيذ الموصى به:
1. **الأسبوع 1**: إكمال جميع المهام الحرجة (🔴)
2. **الأسبوع 2-3**: إكمال المهام المهمة الأساسية (🟡)
3. **الشهر 2**: إضافة التحسينات (🟢)

### التقديرات الزمنية:
- **Critical Items**: 1-2 أسبوع (40-80 ساعة)
- **Important Items**: 2-4 أسابيع (80-160 ساعة)
- **Nice to Have**: 1-2 شهر (160-320 ساعة)

---

**آخر تحديث**: 2025-01-27  
**الحالة**: قيد المراجعة





