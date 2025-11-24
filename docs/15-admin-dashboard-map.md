# 🗺️ Admin Dashboard Map - Graphic School

## خريطة لوحة تحكم الأدمن

هذا الملف يوثق جميع صفحات وإعدادات لوحة تحكم الأدمن.

---

## 📊 Dashboard Overview

**Route**: `/dashboard/admin`  
**Component**: `AdminDashboard.vue`  
**Access**: Admin, Super Admin

### Main Sections:
1. Statistics Cards
2. Charts (Revenue, Enrollments, Attendance)
3. Recent Activities
4. Quick Actions

---

## 👥 User Management

### Users List
**Route**: `/dashboard/admin/users`  
**Component**: `AdminUsers.vue`

**Features**:
- List all users
- Filter by role, status
- Search users
- Create new user
- Edit user
- Delete user

### Create/Edit User
**Route**: `/dashboard/admin/users/new` or `/dashboard/admin/users/:id/edit`  
**Component**: `UserForm.vue`

**Fields**:
- Name
- Email
- Password
- Phone
- Address
- Role
- Status (Active/Inactive)

---

## 🔐 Roles & Permissions

### Roles List
**Route**: `/dashboard/admin/roles`  
**Component**: `AdminRoles.vue`

**Features**:
- List all roles
- Create role
- Edit role
- Assign permissions

### Create/Edit Role
**Route**: `/dashboard/admin/roles/new` or `/dashboard/admin/roles/:id/edit`  
**Component**: `RoleForm.vue`

---

## 📚 Categories

### Categories List
**Route**: `/dashboard/admin/categories`  
**Component**: `AdminCategories.vue`

**Features**:
- List all categories
- Multi-language support
- Create category
- Edit category
- Delete category

### Create/Edit Category
**Route**: `/dashboard/admin/categories/new` or `/dashboard/admin/categories/:id/edit`  
**Component**: `CategoryForm.vue`

---

## 🎓 Courses

### Courses List
**Route**: `/dashboard/admin/courses`  
**Component**: `AdminCourses.vue`

**Features**:
- List all courses
- Filter by category, status
- Search courses
- Create course
- Edit course
- Delete course
- Publish/Unpublish

### Create/Edit Course
**Route**: `/dashboard/admin/courses/new` or `/dashboard/admin/courses/:id/edit`  
**Component**: `CourseForm.vue`

**Fields**:
- Title
- Category
- Description
- Price
- Start Date
- End Date
- Session Count
- Days of Week
- Instructors
- Auto-generate Sessions

---

## 📅 Sessions

### Sessions List
**Route**: `/dashboard/admin/sessions`  
**Component**: `AdminSessions.vue`

**Features**:
- List all sessions
- Filter by course, date
- Create session
- Edit session
- Delete session

### Edit Session
**Route**: `/dashboard/admin/sessions/:id/edit`  
**Component**: `SessionForm.vue`

---

## 📝 Enrollments

### Enrollments List
**Route**: `/dashboard/admin/enrollments`  
**Component**: `AdminEnrollments.vue`

**Features**:
- List all enrollments
- Filter by status, payment status
- Approve/Reject enrollment
- Update payment status

### Enrollment Review
**Route**: `/dashboard/admin/enrollments/:id`  
**Component**: `AdminEnrollmentReview.vue`

---

## ✅ Attendance

### Attendance Overview
**Route**: `/dashboard/admin/attendance`  
**Component**: `AdminAttendanceOverview.vue`

**Features**:
- View attendance statistics
- Filter by course, date
- Export attendance reports

### QR Attendance
**Route**: `/dashboard/admin/attendance/qr`  
**Component**: `AdminAttendanceQR.vue`

**Features**:
- Generate QR codes for sessions
- Scan QR codes
- Record attendance

---

## 💰 Payments

### Payments List
**Route**: `/dashboard/admin/payments`  
**Component**: `AdminPayments.vue`

**Features**:
- List all payments
- Filter by status
- View payment timeline
- Export reports

---

## 📄 Invoices

### Invoices List
**Route**: `/dashboard/admin/invoices`  
**Component**: `AdminInvoices.vue`

### Invoice View
**Route**: `/dashboard/admin/invoices/:id`  
**Component**: `AdminInvoiceView.vue`

---

## 🎓 Certificates

### Certificates List
**Route**: `/dashboard/admin/certificates`  
**Component**: `AdminCertificates.vue`

**Features**:
- List all certificates
- Issue certificate
- Verify certificate

### Issue Certificate
**Route**: `/dashboard/admin/certificates/issue/:enrollmentId`  
**Component**: `CertificateIssueForm.vue`

---

## 📊 Reports

### Reports Page
**Route**: `/dashboard/admin/reports`  
**Component**: `ReportsPage.vue`

**Report Types**:
- Course Reports
- Instructor Reports
- Financial Reports
- Student Reports

### Strategic Reports
**Route**: `/dashboard/admin/strategic-reports`  
**Component**: `StrategicReportsPage.vue`

**Report Types**:
- Performance Reports
- Profitability Reports
- Forecasting Reports
- Top Students
- Average Grades
- Attendance Rate
- Engagement Metrics

---

## 🎨 CMS Management

### Pages
**Route**: `/dashboard/admin/pages`  
**Component**: `AdminPages.vue`

### Page Builder
**Route**: `/dashboard/admin/page-builder`  
**Component**: `PageBuilderPages.vue`

### Page Builder Editor
**Route**: `/dashboard/admin/page-builder/editor/:id`  
**Component**: `PageBuilderEditor.vue`

### Sliders
**Route**: `/dashboard/admin/sliders`  
**Component**: `AdminSliders.vue`

### FAQs
**Route**: `/dashboard/admin/faqs`  
**Component**: `AdminFAQs.vue`

### Media Library
**Route**: `/dashboard/admin/media`  
**Component**: `AdminMedia.vue`

---

## 📧 Contacts

### Contact Messages
**Route**: `/dashboard/admin/contacts`  
**Component**: `AdminContacts.vue`

**Features**:
- List all messages
- Mark as resolved
- Reply to messages

---

## 🎨 Branding

### Branding Editor
**Route**: `/dashboard/admin/branding`  
**Component**: `BrandingEditor.vue`

**Features**:
- Logo upload
- Color customization
- Font customization
- Layout customization

---

## 🌐 Translations

### Translations List
**Route**: `/dashboard/admin/translations`  
**Component**: `AdminTranslations.vue`

**Features**:
- List all translations
- Filter by group, locale
- Create translation
- Edit translation

### Create/Edit Translation
**Route**: `/dashboard/admin/translations/new` or `/dashboard/admin/translations/:id/edit`  
**Component**: `TranslationForm.vue`

---

## ⚙️ Settings

### System Settings
**Route**: `/dashboard/admin/settings`  
**Component**: `AdminSettings.vue`

**Settings Sections**:
- General Settings
- Email Settings
- Payment Settings
- System Settings

---

## 🎫 Support Tickets

### Tickets List
**Route**: `/dashboard/admin/tickets`  
**Component**: `AdminTickets.vue`

**Features**:
- List all tickets
- Filter by status, type
- Respond to tickets
- Close tickets

---

## 📋 Audit Logs

### Audit Logs
**Route**: `/dashboard/admin/audit-logs`  
**Component**: `AdminAuditLogs.vue`

**Features**:
- View all activity logs
- Filter by user, action, date
- Export logs

---

## 🎮 Programs & Batches

### Programs List
**Route**: `/dashboard/admin/programs`  
**Component**: `AdminPrograms.vue`

### Create Program
**Route**: `/dashboard/admin/programs/new`  
**Component**: `AdminProgramCreate.vue`

### Edit Program
**Route**: `/dashboard/admin/programs/:id/edit`  
**Component**: `AdminProgramEdit.vue`

### Batches List
**Route**: `/dashboard/admin/batches`  
**Component**: `AdminBatches.vue`

### Program Batches
**Route**: `/dashboard/admin/programs/:programId/batches`  
**Component**: `AdminBatches.vue`

---

## 👥 Groups

### Groups List
**Route**: `/dashboard/admin/groups`  
**Component**: `AdminGroups.vue`

### Batch Groups
**Route**: `/dashboard/admin/batches/:batchId/groups`  
**Component**: `AdminGroups.vue`

### Group View
**Route**: `/dashboard/admin/groups/:groupId`  
**Component**: `AdminGroupView.vue`

---

## 📝 Assignments

### Assignments Overview
**Route**: `/dashboard/admin/assignments`  
**Component**: `AdminAssignmentsOverview.vue`

---

## 📊 Gradebook

### Gradebook Overview
**Route**: `/dashboard/admin/gradebook`  
**Component**: `AdminGradebookOverview.vue`

---

## 📅 Calendar

### Calendar
**Route**: `/dashboard/admin/calendar`  
**Component**: `AdminCalendar.vue`

---

## 🎮 Gamification

### Gamification Rules
**Route**: `/dashboard/admin/gamification/rules`  
**Component**: `AdminGamificationRules.vue`

---

## 👥 Community

### Community Posts
**Route**: `/dashboard/admin/community/posts`  
**Component**: `AdminCommunityPosts.vue`

### Community Reports
**Route**: `/dashboard/admin/community/reports`  
**Component**: `AdminCommunityReports.vue`

---

## 🔄 Navigation Structure

### Main Menu Items:
1. Dashboard
2. Users
3. Roles
4. Categories
5. Courses
6. Sessions
7. Enrollments
8. Attendance
9. Payments
10. Tickets
11. Pages
12. FAQs
13. Media
14. Sliders
15. Audit Logs
16. Settings
17. Branding
18. Contacts
19. Translations
20. Programs
21. Batches
22. Groups
23. Assignments
24. Gradebook
25. Calendar
26. Gamification
27. Community
28. Certificates
29. Invoices
30. Reports

---

**آخر تحديث**: 2025-01-27  
**الإصدار**: 1.0.0

