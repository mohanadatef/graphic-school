# 📦 Product Scope - Graphic School

## قائمة بجميع الـ Modules / Domains / Features

---

## 1. ACL (Access Control Layer)

### الوصف:
نظام إدارة الوصول والصلاحيات الكامل. يتضمن Authentication، إدارة المستخدمين، والأدوار والصلاحيات.

### أهم Use Cases:
- تسجيل الدخول (Login)
- التسجيل (Register)
- تسجيل الخروج (Logout)
- إدارة المستخدمين (CRUD)
- إدارة الأدوار (Roles)
- إدارة الصلاحيات (Permissions)
- تعيين الأدوار للمستخدمين
- تعيين الصلاحيات للأدوار

### الملفات الأساسية:
- **Controllers**: 
  - `Modules/ACL/Auth/Http/Controllers/AuthController.php`
  - `Modules/ACL/Users/Http/Controllers/UserController.php`
  - `Modules/ACL/Roles/Http/Controllers/RoleController.php`
- **Models**: 
  - `Modules/ACL/Users/Models/User.php`
  - `Modules/ACL/Roles/Models/Role.php`
  - `Modules/ACL/Permissions/Models/Permission.php`
- **Use Cases**: 
  - `Modules/ACL/Auth/Application/UseCases/LoginUserUseCase.php`
  - `Modules/ACL/Auth/Application/UseCases/RegisterUserUseCase.php`
- **Routes**: `Modules/ACL/Auth/Routes/api.php`

---

## 2. LMS - Categories

### الوصف:
إدارة تصنيفات الكورسات مع دعم متعدد اللغات.

### أهم Use Cases:
- إنشاء تصنيف جديد
- تعديل تصنيف
- حذف تصنيف
- عرض قائمة التصنيفات
- إدارة الترجمات (عربي/إنجليزي)

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Categories/Http/Controllers/CategoryController.php`
- **Model**: `Modules/LMS/Categories/Models/Category.php`
- **Translation Model**: `Modules/LMS/Categories/Models/CategoryTranslation.php`
- **Service**: `Modules/LMS/Categories/Services/CategoryService.php`
- **Request**: `Modules/LMS/Categories/Http/Requests/StoreCategoryRequest.php`

---

## 3. LMS - Courses

### الوصف:
إدارة الكورسات الكاملة. يتضمن إنشاء، تعديل، نشر، تعيين مدربين، وتوليد الجلسات.

### أهم Use Cases:
- إنشاء كورس جديد
- تعديل كورس
- حذف كورس
- نشر/إخفاء كورس
- تعيين مدربين لكورس
- توليد جلسات تلقائياً
- عرض قائمة الكورسات (مع pagination, search, filters)
- عرض تفاصيل كورس

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Courses/Http/Controllers/CourseController.php`
- **Model**: `Modules/LMS/Courses/Models/Course.php`
- **Use Cases**: 
  - `Modules/LMS/Courses/Application/UseCases/CreateCourseUseCase.php`
  - `Modules/LMS/Courses/Application/UseCases/UpdateCourseUseCase.php`
  - `Modules/LMS/Courses/Application/UseCases/GenerateSessionsUseCase.php`
- **DTOs**: `Modules/LMS/Courses/Application/DTOs/`
- **Events**: `Modules/LMS/Courses/Domain/Events/`
- **Service**: `Modules/LMS/Courses/Services/CourseService.php`

---

## 4. LMS - Curriculum

### الوصف:
إدارة المنهج الدراسي. يتضمن Modules، Lessons، و Resources.

### أهم Use Cases:
- إنشاء Module جديد
- تعديل Module
- حذف Module
- إضافة Lesson لـ Module
- تعديل Lesson
- حذف Lesson
- إضافة Resource لـ Lesson (ملف، رابط)
- ترتيب Modules و Lessons

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Curriculum/Http/Controllers/CurriculumController.php`
- **Models**: 
  - `Modules/LMS/Curriculum/Models/CourseModule.php`
  - `Modules/LMS/Curriculum/Models/Lesson.php`
  - `Modules/LMS/Curriculum/Models/LessonResource.php`
- **Service**: `Modules/LMS/Curriculum/Services/CurriculumService.php`
- **Requests**: `Modules/LMS/Curriculum/Http/Requests/`

---

## 5. LMS - Sessions

### الوصف:
إدارة الجلسات التعليمية. يتضمن إنشاء، تعديل، وحذف الجلسات.

### أهم Use Cases:
- عرض قائمة الجلسات
- عرض تفاصيل جلسة
- تعديل جلسة
- حذف جلسة
- توليد جلسات تلقائياً (من Course)
- تصفية الجلسات حسب الكورس، التاريخ، الحالة

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Sessions/Http/Controllers/SessionController.php`
- **Model**: `Modules/LMS/Sessions/Models/Session.php`
- **Service**: `Modules/LMS/Sessions/Services/SessionService.php`
- **Repository**: `Modules/LMS/Sessions/Repositories/Eloquent/SessionRepository.php`

---

## 6. LMS - Enrollments

### الوصف:
إدارة التسجيلات في الكورسات. يتضمن إنشاء، تعديل، ومتابعة حالات التسجيلات.

### أهم Use Cases:
- تسجيل طالب في كورس
- عرض قائمة التسجيلات
- تعديل حالة التسجيل (pending, approved, rejected)
- تحديث حالة الدفع
- متابعة التسجيلات حسب الحالة

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Enrollments/Http/Controllers/EnrollmentController.php`
- **Model**: `Modules/LMS/Enrollments/Models/Enrollment.php`
- **Service**: `Modules/LMS/Enrollments/Services/EnrollmentService.php`
- **Enums**: 
  - `Modules/LMS/Enrollments/Enums/EnrollmentStatus.php`
  - `Modules/LMS/Enrollments/Enums/EnrollmentPaymentStatus.php`

---

## 7. LMS - Attendance

### الوصف:
تسجيل ومتابعة حضور الطلاب في الجلسات.

### أهم Use Cases:
- تسجيل حضور طالب في جلسة
- عرض قائمة الحضور
- تصفية الحضور حسب الجلسة، الطالب، الكورس
- إحصائيات الحضور

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Attendance/Http/Controllers/AttendanceController.php`
- **Model**: `Modules/LMS/Attendance/Models/Attendance.php`
- **Service**: `Modules/LMS/Attendance/Services/AttendanceService.php`
- **Enum**: `Modules/LMS/Attendance/Enums/AttendanceStatus.php`

---

## 8. LMS - Assessments

### الوصف:
نظام التقييم الشامل. يتضمن Quizzes و Projects.

### أهم Use Cases:
- إنشاء Quiz جديد
- إضافة أسئلة لـ Quiz
- تعديل Quiz
- حذف Quiz
- إجراء Quiz (Student)
- تقديم Quiz (Submit answers)
- عرض نتائج Quiz
- إنشاء Project جديد
- إرسال Project (Student)
- تقييم Project (Instructor)

### الملفات الأساسية:
- **Controllers**: 
  - `Modules/LMS/Assessments/Http/Controllers/QuizController.php`
  - `Modules/LMS/Assessments/Http/Controllers/ProjectController.php`
- **Models**: 
  - `Modules/LMS/Assessments/Models/Quiz.php`
  - `Modules/LMS/Assessments/Models/QuizQuestion.php`
  - `Modules/LMS/Assessments/Models/QuizAttempt.php`
  - `Modules/LMS/Assessments/Models/StudentProject.php`
- **Service**: `Modules/LMS/Assessments/Services/QuizService.php`

---

## 9. LMS - Progress

### الوصف:
تتبع تقدم الطلاب في الكورسات. يتضمن متابعة إتمام Lessons، Quizzes، Projects.

### أهم Use Cases:
- تتبع تقدم طالب في كورس
- تحديث حالة إتمام Lesson
- تحديث حالة إتمام Quiz
- تحديث حالة إتمام Project
- عرض إحصائيات التقدم
- حساب نسبة الإتمام

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Progress/Http/Controllers/ProgressController.php`
- **Model**: `Modules/LMS/Progress/Models/StudentProgress.php`
- **Service**: `Modules/LMS/Progress/Services/ProgressService.php`

---

## 10. LMS - Certificates

### الوصف:
إصدار ومتابعة شهادات إتمام الكورسات.

### أهم Use Cases:
- إصدار شهادة تلقائياً عند إتمام الكورس
- عرض شهادة
- تحميل شهادة (PDF)
- التحقق من صحة شهادة (Verification)

### الملفات الأساسية:
- **Controller**: `Modules/LMS/Certificates/Http/Controllers/CertificateController.php`
- **Model**: `Modules/LMS/Certificates/Models/Certificate.php`
- **Service**: `Modules/LMS/Certificates/Services/CertificateService.php`

---

## 11. LMS - Course Reviews

### الوصف:
تقييمات الطلاب للكورسات والمدربين.

### أهم Use Cases:
- إضافة تقييم لكورس
- تعديل تقييم
- عرض تقييمات كورس
- تصفية التقييمات

### الملفات الأساسية:
- **Model**: `Modules/LMS/CourseReviews/Models/CourseReview.php`
- **Repository**: `Modules/LMS/CourseReviews/Repositories/`

---

## 12. CMS - Sliders

### الوصف:
إدارة البنرات (Sliders) في الصفحة الرئيسية.

### أهم Use Cases:
- إنشاء بانر جديد
- تعديل بانر
- حذف بانر
- ترتيب البنرات
- تفعيل/تعطيل بانر

### الملفات الأساسية:
- **Controller**: `Modules/CMS/Sliders/Http/Controllers/SliderController.php`
- **Model**: `Modules/CMS/Sliders/Models/Slider.php`

---

## 13. CMS - Testimonials

### الوصف:
إدارة شهادات الطلاب (Testimonials).

### أهم Use Cases:
- عرض شهادات الطلاب
- تعديل شهادة
- حذف شهادة
- تفعيل/تعطيل شهادة

### الملفات الأساسية:
- **Controller**: `Modules/CMS/Testimonials/Http/Controllers/TestimonialController.php`
- **Model**: `Modules/CMS/Testimonials/Models/Testimonial.php`

---

## 14. CMS - Contacts

### الوصف:
إدارة رسائل التواصل من الموقع.

### أهم Use Cases:
- عرض قائمة الرسائل
- عرض تفاصيل رسالة
- حل/إغلاق رسالة (Resolve)
- تصفية الرسائل

### الملفات الأساسية:
- **Controller**: `Modules/CMS/Contacts/Http/Controllers/ContactController.php`
- **Model**: `Modules/CMS/Contacts/Models/ContactMessage.php`

---

## 15. CMS - Settings

### الوصف:
إدارة إعدادات النظام العامة.

### أهم Use Cases:
- عرض الإعدادات
- تحديث الإعدادات
- إدارة إعدادات النظام (System Settings)

### الملفات الأساسية:
- **Controller**: `Modules/CMS/Settings/Http/Controllers/SettingController.php`
- **Models**: 
  - `Modules/CMS/Settings/Models/Setting.php`
  - `Modules/CMS/Settings/Models/SystemSetting.php`

---

## 16. CMS - Public Site

### الوصف:
واجهة الموقع العامة. يتضمن عرض الكورسات، المدربين، إلخ.

### أهم Use Cases:
- عرض الصفحة الرئيسية (Home)
- عرض قائمة الكورسات
- عرض تفاصيل كورس
- عرض قائمة المدربين
- عرض تفاصيل مدرب
- إرسال رسالة تواصل

### الملفات الأساسية:
- **Controller**: `Modules/CMS/PublicSite/Http/Controllers/PublicController.php`

---

## 17. Core - Localization

### الوصف:
دعم متعدد اللغات (عربي/إنجليزي).

### أهم Use Cases:
- تغيير اللغة
- عرض الترجمات
- إدارة الترجمات (Admin)
- عرض اللغات المتاحة

### الملفات الأساسية:
- **Controllers**: 
  - `Modules/Core/Localization/Http/Controllers/LanguageController.php`
  - `Modules/Core/Localization/Http/Controllers/TranslationController.php`
- **Models**: 
  - `Modules/Core/Localization/Models/Language.php`
  - `Modules/Core/Localization/Models/Translation.php`

---

## 18. Core - File Storage

### الوصف:
إدارة الملفات والصور.

### أهم Use Cases:
- رفع ملف
- حذف ملف
- عرض ملف

### الملفات الأساسية:
- **Controller**: `Modules/Core/FileStorage/Presentation/Http/Controllers/FileStorageController.php`
- **Service**: `App/Services/FileStorageService.php`

---

## 19. Core - Notification

### الوصف:
نظام الإشعارات.

### أهم Use Cases:
- إرسال إشعار
- عرض الإشعارات
- تحديث حالة الإشعار (مقروء/غير مقروء)

### الملفات الأساسية:
- **Controller**: `Modules/Core/Notification/Presentation/Http/Controllers/NotificationController.php`

---

## 20. Core - Export/Import

### الوصف:
تصدير واستيراد البيانات.

### أهم Use Cases:
- تصدير بيانات (Excel, CSV)
- استيراد بيانات

### الملفات الأساسية:
- **Controller**: `Modules/Core/ExportImport/Presentation/Http/Controllers/ExportImportController.php`

---

## 21. Operations - Dashboard

### الوصف:
لوحات تحكم للإحصائيات.

### أهم Use Cases:
- عرض Dashboard Admin
- عرض إحصائيات عامة
- عرض إحصائيات الكورسات
- عرض إحصائيات الطلاب

### الملفات الأساسية:
- **Controller**: `Modules/Operations/Dashboard/Http/Controllers/DashboardController.php`

---

## 22. Operations - Reports

### الوصف:
تقارير تفصيلية.

### أهم Use Cases:
- تقرير الكورسات
- تقرير المدربين
- تقرير مالي
- تقارير استراتيجية (Performance, Profitability, Forecasting)

### الملفات الأساسية:
- **Controllers**: 
  - `Modules/Operations/Reports/Http/Controllers/ReportController.php`
  - `Modules/Operations/Reports/Http/Controllers/StrategicReportController.php`
- **Services**: 
  - `Modules/Operations/Reports/Services/ReportService.php`
  - `Modules/Operations/Reports/Services/StrategicReportService.php`

---

## 23. Operations - Analytics

### الوصف:
تحليلات الزيارات والأداء.

### أهم Use Cases:
- تتبع الزيارات
- تحليل الأداء
- إحصائيات الاستخدام

### الملفات الأساسية:
- **Controller**: `Modules/Operations/Analytics/Http/Controllers/AnalyticsController.php`
- **Model**: `Modules/Operations/Analytics/Models/Visit.php`

---

## 24. Operations - Logging

### الوصف:
سجلات النشاطات.

### أهم Use Cases:
- عرض سجلات النشاطات
- عرض سجلات التطبيق
- تصفية السجلات

### الملفات الأساسية:
- **Controller**: `Modules/Operations/Logging/Http/Controllers/ActivityLogController.php`
- **Models**: 
  - `Modules/Operations/Logging/Models/ActivityLog.php`
  - `Modules/Operations/Logging/Models/ApplicationLog.php`

---

## 25. Operations - Backup

### الوصف:
نسخ احتياطي للبيانات.

### أهم Use Cases:
- إنشاء نسخة احتياطية
- عرض النسخ الاحتياطية
- استعادة نسخة احتياطية

### الملفات الأساسية:
- **Controller**: `Modules/Operations/Backup/Http/Controllers/BackupController.php`
- **Model**: `Modules/Operations/Backup/Models/Backup.php`

---

## 26. Support - Tickets

### الوصف:
نظام تذاكر الدعم الفني.

### أهم Use Cases:
- إنشاء تذكرة
- عرض التذاكر
- تحديث حالة التذكرة
- إضافة رد على تذكرة

### الملفات الأساسية:
- **Controllers**: 
  - `Modules/Support/Tickets/Http/Controllers/TicketController.php`
  - `Modules/Support/Tickets/Http/Controllers/ExternalTicketController.php`
- **Model**: `Modules/Support/Tickets/Models/SupportTicket.php`

---

## 27. Support - System Health

### الوصف:
مراقبة صحة النظام.

### أهم Use Cases:
- فحص صحة النظام
- عرض حالة النظام
- تنبيهات النظام

### الملفات الأساسية:
- **Controller**: `Modules/Support/SystemHealth/Http/Controllers/HealthCheckController.php`
- **Model**: `Modules/Support/SystemHealth/Models/SystemHealth.php`
- **Controller**: `App/Http/Controllers/HealthController.php` (Health check endpoint)

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

