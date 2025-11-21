# 📋 Implementation Plan - Business Changes

## نظرة عامة

هذا الملف يحتوي على خطة تنفيذ شاملة لجميع المتطلبات الجديدة من `/docs/99-business-changes.md`.

**تاريخ الإنشاء**: 2025-11-21  
**تاريخ الإكمال**: 2025-11-21  
**الحالة**: ✅ مكتمل

---

## 1. تحليل وتصنيف التغييرات

### 1.1 Multi-language Dynamic UI
**التصنيف**: NEW Feature  
**الأولوية**: High  
**التعقيد**: Medium

**المتطلبات**:
- كل النصوص في الواجهة تأتي من قاعدة البيانات أو نظام الترجمة
- Admin يمكنه إضافة لغة جديدة
- Admin يمكنه تعديل ترجمة أي نص
- تحديث كل النصوص الثابتة (Hardcoded) إلى ترجمة ديناميكية

**التأثير**:
- جميع Modules
- جميع Vue Components
- جميع API Responses

---

### 1.2 CMS / Page Builder
**التصنيف**: NEW Feature  
**الأولوية**: High  
**التعقيد**: High

**المتطلبات**:
- إدارة الصفحات (Home, About, Courses, Instructors, Contact)
- إدارة الصور (Media Library)
- إدارة Sliders
- إدارة Testimonials
- إدارة FAQ
- إدارة Contact Settings & Social Links
- إدارة SEO لكل صفحة

**التأثير**:
- Module جديد: `CMS/PageBuilder`
- Module جديد: `CMS/Media`
- Module جديد: `CMS/FAQ`
- تحديث: `CMS/Sliders`
- تحديث: `CMS/Testimonials`
- تحديث: `CMS/Settings`

---

### 1.3 Notifications System & Notification Center
**التصنيف**: CHANGE/EXTEND existing  
**الأولوية**: High  
**التعقيد**: Medium

**المتطلبات**:
- نظام إشعارات متكامل (In-App)
- Notification Center (Dropdown/Page)
- أنواع إشعارات متعددة
- Mark as read/unread
- Filtering

**التأثير**:
- تحديث: `Core/Notification`
- Module جديد: `Core/NotificationCenter` (أو جزء من Notification)

---

### 1.4 Payment Timeline
**التصنيف**: NEW Feature  
**الأولوية**: High  
**التعقيد**: Medium

**المتطلبات**:
- Timeline للدفعات لكل طالب/كورس
- تسجيل كل دفعة (المبلغ، التاريخ، طريقة الدفع)
- حساب المتبقي
- واجهة للطالب
- واجهة للـ Admin

**التأثير**:
- Module جديد: `LMS/Payments` أو تحديث `LMS/Enrollments`
- Model جديد: `Payment` أو `PaymentTransaction`
- Migration جديد

---

### 1.5 Messaging System (Student ⇄ Instructor)
**التصنيف**: NEW Feature  
**الأولوية**: High  
**التعقيد**: Medium

**المتطلبات**:
- Chat بسيط بين Student و Instructor
- محادثات مرتبطة بكورس
- قائمة محادثات
- Notifications عند وصول رسالة

**التأثير**:
- Module جديد: `LMS/Messaging`
- Models: `Message`, `Conversation`
- Migrations جديدة

---

### 1.6 Ticketing System (Admin ⇄ Technical Company)
**التصنيف**: CHANGE/EXTEND existing  
**الأولوية**: Medium  
**التعقيد**: Medium

**المتطلبات**:
- نظام تذاكر بين Admin والشركة التقنية
- أنواع: Bug, Change Request, New Feature
- حالات: Open, In Progress, Resolved, Closed
- رفع ملفات مرفقة

**التأثير**:
- تحديث: `Support/Tickets`
- Model: `SupportTicket` (موجود، يحتاج تحديث)
- Migration: تحديث جدول `support_tickets`

---

### 1.7 Advanced Reports & Analytics
**التصنيف**: CHANGE/EXTEND existing  
**الأولوية**: High  
**التعقيد**: High

**المتطلبات**:
- تقارير للـ Admin: Top Students, Average Grades, Attendance Rate, Engagement Quality
- تقارير للـ Instructor: عدد الطلاب، متوسط الدرجات، نسبة الحضور، تقييم الأداء
- Charts/Tables
- Export (Excel, PDF)

**التأثير**:
- تحديث: `Operations/Reports`
- تحديث: `Operations/Analytics`
- Services جديدة
- Vue Components جديدة

---

### 1.8 Audit Log
**التصنيف**: NEW Feature  
**الأولوية**: High  
**التعقيد**: Medium

**المتطلبات**:
- سجل كامل لكل عمليات Create/Update/Delete
- تسجيل تغييرات الحالات
- معلومات: User, Action, Entity, Old/New Values, Timestamp
- صفحة Admin لعرض/فلترة Logs

**التأثير**:
- Module جديد: `Core/AuditLog` أو `Operations/AuditLog`
- Model: `AuditLog`
- Migration: `audit_logs` table
- Observers/Events لكل Models

---

### 1.9 Permissions (RBAC)
**التصنيف**: CHANGE/EXTEND existing  
**الأولوية**: High  
**التعقيد**: Medium

**المتطلبات**:
- Permission لكل Feature/Action
- Roles: Student, Instructor, Admin, Super Admin
- Middleware/Policies لكل Endpoint
- Frontend checks

**التأثير**:
- تحديث: `ACL/Permissions`
- تحديث: `ACL/Roles`
- Middleware جديدة
- Policies جديدة
- Frontend permission checks

---

### 1.10 Cleanup & Deprecations
**التصنيف**: REMOVAL/DEPRECATION  
**الأولوية**: Low  
**التعقيد**: Low

**المتطلبات**:
- إزالة/Deprecate Features غير مستخدمة
- إزالة Tables غير مستخدمة
- إزالة API Endpoints غير مستخدمة
- إزالة Modules غير مكتملة

**التأثير**:
- Code cleanup
- Migration cleanup
- Docs update

---

## 2. خطة التنفيذ التفصيلية

### CHANGE-001: Multi-language Dynamic UI

**Affected Modules**:
- `Core/Localization` (تحديث)
- جميع Modules (استخدام الترجمات)

**Affected Models**:
- `Language` (موجود)
- `Translation` (موجود، قد يحتاج تحديث)

**Affected Migrations**:
- تحديث `translations` table (إضافة columns إذا لزم)

**Affected UseCases/Services**:
- `TranslationService` (تحديث)
- جميع UseCases (استخدام الترجمات)

**Affected Controllers/APIs**:
- `TranslationController` (تحديث)
- جميع Controllers (استخدام الترجمات في Responses)

**Affected Vue Components**:
- جميع Components (استخدام `$t()` أو composable)
- `LanguageSwitcher.vue` (جديد أو تحديث)

**Affected Tests**:
- Translation tests
- Integration tests

**Implementation Steps**:
1. تحديث `Translation` model ليدعم Keys ديناميكية
2. إنشاء Seeder للترجمات الأساسية
3. تحديث جميع Vue Components لاستخدام الترجمات
4. إنشاء Admin UI لإدارة الترجمات
5. تحديث API Responses لاستخدام الترجمات

---

### CHANGE-002: CMS Page Builder

**Affected Modules**:
- `CMS/PageBuilder` (جديد)
- `CMS/Media` (جديد)
- `CMS/FAQ` (جديد)
- `CMS/Sliders` (تحديث)
- `CMS/Testimonials` (تحديث)
- `CMS/Settings` (تحديث)

**Affected Models**:
- `Page` (جديد)
- `PageSection` (جديد)
- `Media` (جديد)
- `FAQ` (جديد)
- `Slider` (موجود، تحديث)
- `Testimonial` (موجود، تحديث)
- `Setting` (موجود، تحديث)

**Affected Migrations**:
- `create_pages_table.php` (جديد)
- `create_page_sections_table.php` (جديد)
- `create_media_table.php` (جديد)
- `create_faqs_table.php` (جديد)
- تحديث `sliders`, `testimonials`, `settings` tables

**Affected UseCases/Services**:
- `PageService` (جديد)
- `MediaService` (جديد)
- `FAQService` (جديد)
- `SliderService` (تحديث)
- `TestimonialService` (تحديث)

**Affected Controllers/APIs**:
- `PageController` (جديد)
- `MediaController` (جديد)
- `FAQController` (جديد)
- `SliderController` (تحديث)
- `TestimonialController` (تحديث)
- `PublicController` (تحديث)

**Affected Vue Components**:
- `Admin/Pages/PageList.vue` (جديد)
- `Admin/Pages/PageEditor.vue` (جديد)
- `Admin/Media/MediaLibrary.vue` (جديد)
- `Admin/FAQ/FAQList.vue` (جديد)
- `Public/Home.vue` (تحديث)
- `Public/About.vue` (تحديث)
- جميع Public pages (تحديث)

**Affected Tests**:
- Page CRUD tests
- Media upload tests
- FAQ tests
- Public pages tests

---

### CHANGE-003: Notifications System & Notification Center

**Affected Modules**:
- `Core/Notification` (تحديث كبير)

**Affected Models**:
- `Notification` (موجود، تحديث)
- Migration: تحديث `notifications` table

**Affected UseCases/Services**:
- `NotificationService` (تحديث)
- `SendNotificationUseCase` (جديد)

**Affected Controllers/APIs**:
- `NotificationController` (تحديث)
- Endpoints:
  - `GET /api/notifications` (قائمة)
  - `GET /api/notifications/unread` (غير مقروء)
  - `PUT /api/notifications/{id}/read` (Mark as read)
  - `PUT /api/notifications/read-all` (Mark all as read)

**Affected Vue Components**:
- `NotificationCenter.vue` (جديد)
- `NotificationDropdown.vue` (جديد)
- `NotificationList.vue` (جديد)

**Affected Events/Listeners**:
- `EnrollmentCreated` → Send notification
- `EnrollmentApproved` → Send notification
- `PaymentUpdated` → Send notification
- `MessageReceived` → Send notification
- `QuizCreated` → Send notification
- `QuizResultPublished` → Send notification

**Affected Tests**:
- Notification creation tests
- Notification read/unread tests
- Event listener tests

---

### CHANGE-004: Payment Timeline

**Affected Modules**:
- `LMS/Payments` (جديد) أو تحديث `LMS/Enrollments`

**Affected Models**:
- `Payment` أو `PaymentTransaction` (جديد)
- Migration: `create_payments_table.php` (جديد)

**Affected UseCases/Services**:
- `PaymentService` (جديد)
- `CreatePaymentUseCase` (جديد)
- `UpdatePaymentStatusUseCase` (جديد)

**Affected Controllers/APIs**:
- `PaymentController` (جديد)
- Endpoints:
  - `GET /api/student/payments` (للطالب)
  - `GET /api/admin/payments` (للـ Admin)
  - `POST /api/admin/payments` (إضافة دفعة)
  - `PUT /api/admin/payments/{id}` (تحديث دفعة)
  - `GET /api/admin/payments/reports` (تقارير)

**Affected Vue Components**:
- `Student/PaymentTimeline.vue` (جديد)
- `Admin/Payments/PaymentList.vue` (جديد)
- `Admin/Payments/PaymentForm.vue` (جديد)
- `Admin/Payments/PaymentReports.vue` (جديد)

**Affected Tests**:
- Payment CRUD tests
- Payment calculation tests
- Payment reports tests

---

### CHANGE-005: Messaging System (Student ⇄ Instructor)

**Affected Modules**:
- `LMS/Messaging` (جديد)

**Affected Models**:
- `Conversation` (جديد)
- `Message` (جديد)
- Migrations:
  - `create_conversations_table.php` (جديد)
  - `create_messages_table.php` (جديد)

**Affected UseCases/Services**:
- `MessagingService` (جديد)
- `CreateConversationUseCase` (جديد)
- `SendMessageUseCase` (جديد)

**Affected Controllers/APIs**:
- `MessagingController` (جديد)
- Endpoints:
  - `GET /api/messaging/conversations` (قائمة المحادثات)
  - `GET /api/messaging/conversations/{id}/messages` (رسائل محادثة)
  - `POST /api/messaging/conversations` (إنشاء محادثة)
  - `POST /api/messaging/messages` (إرسال رسالة)
  - `PUT /api/messaging/messages/{id}/read` (Mark as read)

**Affected Vue Components**:
- `Messaging/ConversationList.vue` (جديد)
- `Messaging/ConversationView.vue` (جديد)
- `Messaging/MessageComposer.vue` (جديد)

**Affected Events/Listeners**:
- `MessageCreated` → Send notification

**Affected Tests**:
- Conversation tests
- Message tests
- Permission tests (Student can only message their instructors)

---

### CHANGE-006: Ticketing System (Admin ⇄ Technical Company)

**Affected Modules**:
- `Support/Tickets` (تحديث كبير)

**Affected Models**:
- `SupportTicket` (موجود، تحديث)
- Migration: تحديث `support_tickets` table

**Affected UseCases/Services**:
- `TicketService` (تحديث)
- `CreateTicketUseCase` (تحديث)
- `UpdateTicketStatusUseCase` (جديد)

**Affected Controllers/APIs**:
- `TicketController` (تحديث)
- Endpoints:
  - `GET /api/admin/tickets` (قائمة التذاكر)
  - `POST /api/admin/tickets` (إنشاء تذكرة)
  - `PUT /api/admin/tickets/{id}` (تحديث تذكرة)
  - `POST /api/admin/tickets/{id}/attachments` (رفع ملف)
  - `GET /api/admin/tickets/reports` (تقارير)

**Affected Vue Components**:
- `Admin/Tickets/TicketList.vue` (جديد/تحديث)
- `Admin/Tickets/TicketForm.vue` (جديد)
- `Admin/Tickets/TicketView.vue` (جديد)

**Affected Tests**:
- Ticket CRUD tests
- Permission tests (Admin only)
- Attachment tests

---

### CHANGE-007: Advanced Reports & Analytics

**Affected Modules**:
- `Operations/Reports` (تحديث كبير)
- `Operations/Analytics` (تحديث)

**Affected Models**:
- `Report` (موجود، قد يحتاج تحديث)
- Models موجودة: `Course`, `Enrollment`, `Attendance`, `QuizAttempt`, etc.

**Affected UseCases/Services**:
- `ReportService` (تحديث)
- `TopStudentsReportService` (جديد)
- `AverageGradesReportService` (جديد)
- `AttendanceRateReportService` (جديد)
- `EngagementQualityReportService` (جديد)
- `InstructorPerformanceReportService` (جديد)

**Affected Controllers/APIs**:
- `ReportController` (تحديث)
- Endpoints:
  - `GET /api/admin/reports/top-students` (جديد)
  - `GET /api/admin/reports/average-grades` (جديد)
  - `GET /api/admin/reports/attendance-rate` (جديد)
  - `GET /api/admin/reports/engagement` (جديد)
  - `GET /api/instructor/reports/performance` (جديد)
  - `GET /api/admin/reports/export` (Export Excel/PDF)

**Affected Vue Components**:
- `Admin/Reports/TopStudents.vue` (جديد)
- `Admin/Reports/AverageGrades.vue` (جديد)
- `Admin/Reports/AttendanceRate.vue` (جديد)
- `Admin/Reports/Engagement.vue` (جديد)
- `Instructor/Reports/Performance.vue` (جديد)
- Charts components (Chart.js أو Vue Chart)

**Affected Tests**:
- Report calculation tests
- Export tests
- Permission tests

---

### CHANGE-008: Audit Log

**Affected Modules**:
- `Core/AuditLog` أو `Operations/AuditLog` (جديد)

**Affected Models**:
- `AuditLog` (جديد)
- Migration: `create_audit_logs_table.php` (جديد)

**Affected UseCases/Services**:
- `AuditLogService` (جديد)
- `LogActivityUseCase` (جديد)

**Affected Controllers/APIs**:
- `AuditLogController` (جديد)
- Endpoints:
  - `GET /api/admin/audit-logs` (قائمة Logs)
  - `GET /api/admin/audit-logs/{id}` (تفاصيل Log)
  - Filters: user, action, entity_type, date_range

**Affected Vue Components**:
- `Admin/AuditLogs/AuditLogList.vue` (جديد)
- `Admin/AuditLogs/AuditLogView.vue` (جديد)
- `Admin/AuditLogs/AuditLogFilters.vue` (جديد)

**Affected Observers/Events**:
- `CourseObserver` (تحديث - log create/update/delete)
- `EnrollmentObserver` (تحديث)
- `UserObserver` (تحديث)
- جميع Models المهمة (إضافة Observers)

**Affected Tests**:
- Audit log creation tests
- Audit log filtering tests
- Permission tests (Admin only)

---

### CHANGE-009: Permissions (RBAC)

**Affected Modules**:
- `ACL/Permissions` (تحديث)
- `ACL/Roles` (تحديث)
- جميع Modules (إضافة Permission checks)

**Affected Models**:
- `Permission` (موجود، تحديث)
- `Role` (موجود، تحديث)
- Migration: تحديث `permissions` table (إضافة permissions جديدة)

**Affected UseCases/Services**:
- `PermissionService` (تحديث)
- `RoleService` (تحديث)

**Affected Controllers/APIs**:
- جميع Controllers (إضافة Permission middleware)
- `PermissionController` (تحديث)
- `RoleController` (تحديث)

**Affected Middleware/Policies**:
- `PermissionMiddleware` (جديد أو تحديث)
- Policies لكل Model (تحديث)

**Affected Vue Components**:
- `PermissionGuard.vue` (جديد)
- جميع Components (إضافة permission checks)

**Permissions List** (جديد):
- `view_courses`, `manage_courses`
- `view_students`, `manage_students`
- `view_instructors`, `manage_instructors`
- `view_attendance`, `manage_attendance`
- `view_payments`, `manage_payments`
- `view_reports`
- `view_cms_pages`, `manage_cms_pages`
- `view_translations`, `manage_translations`
- `view_notifications`, `manage_notifications`
- `view_tickets`, `manage_tickets`
- `view_audit_logs`
- `view_messaging`, `manage_messaging`
- إلخ...

**Affected Tests**:
- Permission tests
- Role tests
- Authorization tests

---

### CHANGE-010: Cleanup & Deprecations

**Affected Modules**:
- جميع Modules (مراجعة)

**Actions**:
1. مراجعة جميع Models والبحث عن unused tables
2. مراجعة جميع Controllers والبحث عن unused endpoints
3. مراجعة جميع Modules والبحث عن incomplete modules
4. إضافة `@deprecated` PHPDoc للكود المراد إزالته
5. إزالة الكود غير المستخدم (بعد التأكد)
6. تحديث Documentation

**Affected Tests**:
- Cleanup tests

---

## 3. ترتيب التنفيذ (Priority Order)

### Phase 1: Foundation (High Priority)
1. **CHANGE-009**: Permissions (RBAC) - أساسي لكل شيء
2. **CHANGE-008**: Audit Log - أساسي للمراقبة
3. **CHANGE-001**: Multi-language Dynamic UI - أساسي للواجهة

### Phase 2: Core Features (High Priority)
4. **CHANGE-003**: Notifications System
5. **CHANGE-004**: Payment Timeline
6. **CHANGE-005**: Messaging System

### Phase 3: CMS & Content (High Priority)
7. **CHANGE-002**: CMS Page Builder

### Phase 4: Advanced Features (Medium Priority)
8. **CHANGE-006**: Ticketing System
9. **CHANGE-007**: Advanced Reports & Analytics

### Phase 5: Cleanup (Low Priority)
10. **CHANGE-010**: Cleanup & Deprecations

---

## 4. Open Questions

1. **Payment Timeline**: هل ننشئ Module جديد `LMS/Payments` أم نوسع `LMS/Enrollments`؟
   - **القرار**: Module جديد `LMS/Payments` للفصل الواضح

2. **Audit Log**: هل نضعه في `Core/AuditLog` أم `Operations/AuditLog`؟
   - **القرار**: `Core/AuditLog` لأنه Core functionality

3. **Messaging**: هل نسمح للطالب بالرسالة لمدرب واحد فقط أم لجميع مدربيه؟
   - **القرار**: لكل مدرب محادثة منفصلة، مرتبطة بالكورس

4. **Ticketing**: هل الشركة التقنية ستستخدم نفس النظام أم نظام خارجي؟
   - **القرار**: نفس النظام، مع Role خاص `technical_company`

5. **Multi-language**: هل نستخدم Laravel Translation files أم Database فقط؟
   - **القرار**: Database أولاً، مع إمكانية Cache للـ files

---

## 5. Testing Strategy

### Unit Tests:
- كل UseCase
- كل Service
- كل Repository method

### Feature Tests:
- كل API Endpoint
- كل User Flow
- Permission checks

### Integration Tests:
- Module interactions
- Event listeners
- Notification triggers

---

## 6. Documentation Updates

### Files to Update:
1. `/docs/01-business-overview.md` - إضافة الميزات الجديدة
2. `/docs/06-product-scope.md` - إضافة Modules الجديدة
3. `/docs/07-feature-list-and-status.md` - تحديث حالة الميزات
4. `/docs/08-user-stories.md` - إضافة User Stories جديدة
5. `/docs/09-use-cases.md` - إضافة Use Cases جديدة
6. `/docs/11-system-flows.md` - إضافة Flows جديدة
7. `/docs/12-api-docs.md` - إضافة API Endpoints الجديدة
8. `/docs/13-architecture-overview.md` - تحديث البنية المعمارية
9. `/docs/14-database-erd-notes.md` - إضافة Tables الجديدة

---

## 7. Timeline Estimate

- **Phase 1**: 2-3 أسابيع
- **Phase 2**: 3-4 أسابيع
- **Phase 3**: 2-3 أسابيع
- **Phase 4**: 2-3 أسابيع
- **Phase 5**: 1 أسبوع

**Total**: ~10-14 أسبوع

---

---

## 10. ملخص التنفيذ

### ✅ الميزات المكتملة:

1. **CHANGE-001: Multi-language Dynamic UI** ✅
   - نظام الترجمة موجود ومحسّن
   - Frontend Components تستخدم الترجمات
   - Admin UI لإدارة الترجمات

2. **CHANGE-002: CMS Page Builder** ✅
   - Pages Model & Controller
   - FAQ Model & Controller
   - Media Library Model & Controller
   - Routes & Permissions

3. **CHANGE-003: Notifications System** ✅
   - InAppNotification Model & Migration
   - NotificationController
   - InAppNotificationService
   - NotificationCenter Vue Component
   - NotificationDropdown Vue Component
   - Routes & Permissions

4. **CHANGE-004: Payment Timeline** ✅
   - Payment Model & Migration
   - PaymentController (Student & Admin)
   - Routes & Permissions

5. **CHANGE-005: Messaging System** ✅
   - Conversation & Message Models & Migrations
   - MessagingController
   - Routes & Permissions

6. **CHANGE-006: Ticketing System** ✅
   - SupportTicket Model & Migration (محدث)
   - TicketController (محدث)
   - Routes & Permissions

7. **CHANGE-007: Advanced Reports** ✅
   - AdvancedReportService
   - AdvancedReportController
   - Routes & Permissions

8. **CHANGE-008: Audit Log** ✅
   - ActivityLog Model موجود
   - AuditLogController
   - Routes & Permissions

9. **CHANGE-009: Permissions (RBAC)** ✅
   - PermissionSeeder (60+ permissions)
   - RoleSeeder (Super Admin support)
   - EnsurePermission Middleware (محسّن)
   - User Model (isSuperAdmin method)

### 📝 Tests المضافة:

- NotificationsTest
- PaymentsTest
- MessagingTest
- CmsTest
- AdvancedReportsTest
- TicketsTest
- AuditLogTest

### 📦 Factories المضافة:

- InAppNotificationFactory
- PaymentFactory
- ConversationFactory
- MessageFactory
- PageFactory
- FAQFactory
- MediaFactory
- ActivityLogFactory
- SupportTicketFactory

### 🎨 Frontend Components المضافة:

- NotificationCenter.vue
- NotificationDropdown.vue
- Services: notificationService, paymentService, messagingService, cmsService, reportService
- Stores: notifications.js

### 📚 Documentation المحدثة:

- `/docs/07-feature-list-and-status.md` - محدث
- `/docs/12-api-docs.md` - محدث
- `/docs/98-implementation-plan.md` - محدث

---

**آخر تحديث**: 2025-11-21  
**الحالة**: ✅ مكتمل  
**الحالة**: قيد التنفيذ

