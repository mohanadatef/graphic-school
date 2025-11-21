# Database Seeders - Graphic School

## نظرة عامة

هذا المجلد يحتوي على Seeders شاملة تغطي **5 سنوات** من البيانات الواقعية للنظام.

## البيانات المولدة

### المستخدمون:
- **1 Admin**
- **25 Instructors** (موزعين على 5 سنوات)
- **500 Students** (موزعين على 5 سنوات: 50, 80, 100, 120, 150)

### الكورسات:
- **~75-100 كورس** موزعين على 5 سنوات
- حالات مختلفة: Completed, Running, Upcoming, Draft
- كل كورس له Modules, Lessons, Resources

### التسجيلات:
- **آلاف التسجيلات** موزعة على جميع الكورسات
- حالات مختلفة: Approved, Pending, Partially Paid, Rejected

### الجلسات:
- **آلاف الجلسات** لكل كورس
- حالات: Completed (للماضي), Scheduled (للمستقبل)

### البيانات الشاملة:
- **Attendance**: سجلات حضور لكل الجلسات المكتملة
- **Course Reviews**: تقييمات للكورسات المكتملة
- **Quizzes & Attempts**: اختبارات ومحاولات الطلاب
- **Student Projects**: مشاريع الطلاب
- **Student Progress**: تقدم الطلاب في الدروس
- **Certificates**: شهادات للطلاب المكملين
- **Testimonials**: شهادات من الطلاب
- **Sliders**: 3 سلايدرز للصفحة الرئيسية
- **Contact Messages**: 200 رسالة اتصال على مدى 5 سنوات

## كيفية التشغيل

### 1. تنظيف قاعدة البيانات (اختياري):
```bash
php artisan migrate:fresh
```

### 2. تشغيل جميع الـ Seeders:
```bash
php artisan db:seed
```

### 3. تشغيل Seeder محدد:
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CourseSeeder
php artisan db:seed --class=ComprehensiveDataSeeder
```

## ترتيب الـ Seeders

الـ Seeders تعمل بالترتيب التالي:

1. **PermissionSeeder** - الصلاحيات
2. **RoleSeeder** - الأدوار
3. **UserSeeder** - المستخدمون (Admin, Instructors, Students)
4. **CategorySeeder** - الفئات
5. **CourseSeeder** - الكورسات (75-100 كورس على 5 سنوات)
6. **EnrollmentSeeder** - التسجيلات
7. **SessionSeeder** - الجلسات
8. **SettingsSeeder** - الإعدادات
9. **TranslationSeeder** - الترجمات
10. **LanguageSeeder** - اللغات
11. **CourseModuleSeeder** - Modules, Lessons, Resources
12. **ComprehensiveDataSeeder** - جميع البيانات الأخرى

## ملاحظات مهمة

### الوقت المطلوب:
- قد يستغرق Seeding البيانات الكاملة **5-15 دقيقة** حسب سرعة الخادم

### حجم البيانات:
- **~500 مستخدم**
- **~100 كورس**
- **~1000+ تسجيل**
- **~1000+ جلسة**
- **~5000+ سجل حضور**
- **~500+ تقييم**
- **~200+ اختبار**
- **~1000+ محاولة اختبار**
- **~500+ مشروع طالب**
- **~5000+ سجل تقدم**
- **~200+ شهادة**

### التواريخ:
- جميع التواريخ موزعة على **5 سنوات** من الآن
- البيانات التاريخية (الماضي) لها تواريخ واقعية
- البيانات المستقبلية (القادمة) لها تواريخ في المستقبل

## استكشاف الأخطاء

### خطأ: "No published courses found"
- تأكد من تشغيل `CourseSeeder` قبل `ComprehensiveDataSeeder`

### خطأ: "No students found"
- تأكد من تشغيل `UserSeeder` قبل `EnrollmentSeeder`

### خطأ: "No enrollments found"
- تأكد من تشغيل `EnrollmentSeeder` قبل `ComprehensiveDataSeeder`

## إعادة التشغيل

لإعادة تشغيل Seeders مع بيانات جديدة:

```bash
php artisan migrate:fresh --seed
```

**تحذير**: هذا سيمسح جميع البيانات الموجودة!

---

**آخر تحديث**: 2025-11-21

