# 🛠️ Tech Stack - Graphic School

## Backend Stack

### Framework:
- **Laravel**: `^10.0`
- **PHP**: `^8.1`

### Authentication:
- **Laravel Sanctum**: `^3.2` - Token-based authentication

### Database:
- **MySQL/MariaDB** - Primary database
- **Eloquent ORM** - Laravel's ORM

### Development Tools:
- **Laravel Tinker**: `^2.8` - REPL
- **Laravel Pint**: `^1.0` - Code formatter
- **PHPUnit**: `^10.0` - Testing framework
- **Mockery**: `^1.4.4` - Mocking library
- **Faker**: `^1.9.1` - Fake data generation

### HTTP Client:
- **Guzzle HTTP**: `^7.2` - HTTP client

---

## Frontend Stack

### Framework:
- **Vue.js**: `^3.5.24` - Progressive JavaScript framework

### State Management:
- **Pinia**: `^2.1.7` - State management for Vue

### Routing:
- **Vue Router**: `^4.6.3` - Official router for Vue.js

### Internationalization:
- **Vue I18n**: `^9.9.1` - Internationalization plugin

### HTTP Client:
- **Axios**: `^1.13.2` - Promise-based HTTP client

### Styling:
- **Tailwind CSS**: `^3.4.13` - Utility-first CSS framework
- **PostCSS**: `^8.5.6` - CSS post-processor
- **Autoprefixer**: `^10.4.22` - CSS vendor prefixer

### Build Tool:
- **Vite**: `^7.2.2` - Next generation frontend tooling
- **@vitejs/plugin-vue**: `^6.0.1` - Vue plugin for Vite

---

## Database

### Primary Database:
- **MySQL** أو **MariaDB**
- **Version**: 5.7+ أو 10.3+

### Features Used:
- Foreign Keys
- Indexes (15+ indexes for performance)
- JSON columns (for days_of_week, options, etc.)
- Enums (for status fields)
- Timestamps (created_at, updated_at)

---

## Cache

### Current:
- **Translation Cache**: ترجمات محفوظة
- **Laravel Cache**: يمكن استخدام Redis/Memcached

### Recommended:
- **Redis**: للـ caching والـ sessions
- **Memcached**: بديل لـ Redis

---

## Queue System

### Current:
- **Laravel Queue**: موجود ولكن استخدام محدود
- **Synchronous**: معظم العمليات synchronous

### Recommended:
- **Redis Queue**: للـ background jobs
- **Database Queue**: بديل بسيط

---

## File Storage

### Current:
- **Local Storage**: الملفات محفوظة محلياً
- **Laravel Storage**: استخدام Laravel Storage facade

### Recommended Production:
- **AWS S3**: لتخزين الملفات
- **DigitalOcean Spaces**: بديل أرخص
- **Cloudflare R2**: بديل حديث

---

## Other Tools

### Version Control:
- **Git** - Version control

### Package Managers:
- **Composer** - PHP dependency manager
- **NPM** - JavaScript package manager

### Development:
- **Laravel Sail** (optional) - Docker development environment

---

## Third-Party Services (Not Currently Integrated)

### Payment Gateways:
- **PayPal** - Payment processing
- **Stripe** - Payment processing
- **Paymob** - Payment processing (Egypt)

### Live Streaming:
- **Zoom** - Video conferencing
- **Google Meet** - Video conferencing

### Email Services:
- **Mailgun** - Transactional emails
- **SendGrid** - Transactional emails
- **SMTP** - Standard email

### SMS Services:
- **Twilio** - SMS gateway
- **SMS Gateway** - Local SMS providers

### Analytics:
- **Google Analytics** - Web analytics
- **Custom Analytics** - Built-in analytics module

---

## Development Environment

### Requirements:
- **PHP**: 8.1+
- **Composer**: Latest
- **Node.js**: 18+
- **NPM**: Latest
- **MySQL/MariaDB**: 5.7+ / 10.3+
- **Web Server**: Apache/Nginx (أو Laravel built-in server)

### Recommended:
- **VS Code** / **PhpStorm** - IDE
- **Postman** - API testing
- **Git** - Version control

---

## Production Environment

### Recommended:
- **PHP**: 8.1+ (PHP-FPM)
- **Web Server**: Nginx
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **Cache**: Redis
- **Queue**: Redis Queue
- **File Storage**: S3 / DigitalOcean Spaces
- **CDN**: Cloudflare
- **SSL**: Let's Encrypt

---

## Security Tools

### Implemented:
- **Laravel Sanctum**: Authentication
- **Input Sanitization**: Custom middleware
- **Rate Limiting**: Custom middleware
- **XSS Protection**: HTML escaping
- **SQL Injection Protection**: Query Builder
- **CSRF Protection**: Sanctum

### Recommended:
- **HTTPS**: SSL/TLS certificates
- **Firewall**: Server-level firewall
- **DDoS Protection**: Cloudflare
- **Security Headers**: Helmet.js equivalent

---

## Monitoring & Logging

### Current:
- **Laravel Logging**: File-based logging
- **Activity Logs**: Custom activity logging
- **Application Logs**: Custom application logs
- **Health Check**: Custom health check endpoint

### Recommended:
- **Sentry** - Error tracking
- **New Relic** - Application monitoring
- **Loggly** - Log management
- **Custom Dashboard** - Built-in monitoring

---

## Testing Tools

### Current:
- **PHPUnit**: Unit & Feature tests
- **Laravel Testing**: HTTP testing
- **Mockery**: Mocking

### Recommended:
- **Pest** (optional) - Modern PHP testing
- **Dusk** (optional) - Browser testing

---

## Documentation Tools

### Current:
- **Markdown**: Documentation files
- **Postman Collection**: API documentation
- **Code Comments**: PHPDoc comments

### Recommended:
- **Swagger/OpenAPI**: API documentation
- **Laravel API Documentation**: Automated docs

---

## Assumptions & Open Questions

### Assumptions:
1. **Database**: MySQL/MariaDB - يمكن تغييره لـ PostgreSQL
2. **Cache**: لا يوجد cache server حالياً - يجب إضافته في Production
3. **Queue**: لا يوجد queue server حالياً - يجب إضافته في Production

### Open Questions:
1. ما هي خطة التوسع المستقبلية؟
2. هل هناك خطط لاستخدام Microservices؟
3. ما هي استراتيجية Backup والـ Disaster Recovery؟

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

