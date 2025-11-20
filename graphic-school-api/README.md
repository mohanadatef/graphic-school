# 🎓 Graphic School API - Laravel 10 Modular Architecture

## 📋 نظرة عامة

نظام إدارة مدرسة تعليم التصميم الجرافيكي مبني على **Laravel 10** مع **Modular Monolith + DDD Architecture**.

---

## 🏗️ البنية المعمارية

### Modular Architecture
- **25 Reusable Modules** - كل Module مستقل تماماً
- **DDD Structure** - Domain, Application, Infrastructure, Presentation
- **Zero Dependencies** - Modules تتواصل عبر Interfaces & Events فقط

### Module Structure
```
Modules/
  DomainName/
    Domain/          # Business logic, Events
    Application/     # UseCases, DTOs
    Infrastructure/  # Models, Repositories, Jobs, Observers
    Presentation/    # Controllers, Requests, Resources, Routes
    Providers/       # ModuleServiceProvider
    Config/          # module.php
```

---

## 📦 Modules (25)

### Core Modules (12)
1. Authentication
2. UserManagement
3. RolePermission
4. Localization
5. Settings
6. FileStorage
7. Notification
8. AuditTrail
9. Backup
10. HealthCheck
11. ExportImport
12. SupportTickets

### LMS Modules (6)
13. Category
14. Course
15. Session
16. Enrollment
17. Attendance
18. Review

### CMS Modules (4)
19. Slider
20. Testimonial
21. Contact
22. PublicSite

### Operations Modules (3)
23. Dashboard
24. Report
25. Analytics

---

## 🚀 البدء السريع

### 1. التثبيت
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### 2. تشغيل الخادم
```bash
php artisan serve
```

### 3. تشغيل Tests
```bash
php artisan test
```

---

## 📮 Postman Collection

### الاستيراد
1. افتح Postman
2. Import → اختر `postman_collection.json`
3. حدّث `base_url` في Collection Variables

### التوثيق
- راجع `POSTMAN_COLLECTION_GUIDE.md` للدليل الكامل

---

## 🧪 Testing

### Unit Tests
```bash
php artisan test --testsuite=Unit
```

### Feature Tests
```bash
php artisan test --testsuite=Feature
```

### All Tests
```bash
php artisan test
```

---

## 📚 التوثيق

### الملفات المهمة:
- `POSTMAN_COLLECTION_GUIDE.md` - دليل Postman
- `DDD_ARCHITECTURE_GUIDE.md` - دليل البنية المعمارية
- `FINAL_AUDIT_AND_TESTS_SUMMARY.md` - ملخص شامل
- `COMPLETE_FINAL_REPORT.md` - التقرير النهائي

---

## 🔧 Features

- ✅ Unified API Response Format
- ✅ Global Error Handling
- ✅ Daily Logging (14 days)
- ✅ Transactions & Locks
- ✅ Pagination, Sort, Filter
- ✅ Excel/PDF Export
- ✅ SoftDeletes + Versioning
- ✅ Audit Trail
- ✅ HealthCheck
- ✅ Cron Jobs

---

## 📝 API Response Format

```json
{
  "success": true,
  "message": "Success message",
  "data": {},
  "errors": null,
  "status": 200,
  "meta": {
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100
    }
  }
}
```

---

## 🔑 Authentication

جميع الـ APIs (عدا Public) تحتاج **Bearer Token**:

```
Authorization: Bearer {token}
```

---

## 📊 Status

- **Modules**: 25/25 (100%) ✅
- **Tests**: Created ✅
- **Postman**: Ready ✅
- **Documentation**: Complete ✅

---

**Ready for Production!** 🚀
