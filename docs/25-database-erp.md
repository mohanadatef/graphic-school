# 🗄️ Database ERP Documentation - Graphic School

## توثيق قاعدة البيانات بصيغة ERP

هذا الملف يحتوي على توثيق شامل لقاعدة البيانات مع الجداول والعلاقات بصيغة ERP (Entity Relationship Planning).

---

## 📊 Database Overview

### Database Name:
`graphic_school`

### Database Engine:
MySQL 8.0+ / MariaDB 10.5+

### Character Set:
`utf8mb4`

### Collation:
`utf8mb4_unicode_ci`

### Total Tables:
**27+ Tables**

---

## 🏗️ Database Schema

### 1. ACL (Access Control Layer) Tables

#### `users`
**الوصف**: جدول المستخدمين (طلاب، مدربين، admins)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| name | VARCHAR(255) | NOT NULL | الاسم الكامل |
| email | VARCHAR(255) | UNIQUE, NOT NULL | البريد الإلكتروني |
| email_verified_at | TIMESTAMP | NULL | تاريخ التحقق من البريد |
| password | VARCHAR(255) | NOT NULL | كلمة المرور (hashed) |
| role_id | BIGINT UNSIGNED | FK → roles.id | الدور |
| phone | VARCHAR(20) | NULL | رقم الهاتف |
| avatar_path | VARCHAR(255) | NULL | مسار الصورة الشخصية |
| address | TEXT | NULL | العنوان |
| bio | TEXT | NULL | السيرة الذاتية |
| is_active | BOOLEAN | DEFAULT 1 | حالة النشاط |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Indexes**:
- `idx_users_email` (email)
- `idx_users_role_id` (role_id)
- `idx_users_is_active` (is_active)

**Relationships**:
- `users` N → 1 `roles`
- `users` 1 → N `enrollments` (as student)
- `users` N → M `courses` (as instructor) via `course_instructor`
- `users` 1 → N `attendance`
- `users` 1 → N `quiz_attempts`
- `users` 1 → N `student_projects`
- `users` 1 → N `student_progress`
- `users` 1 → N `certificates`
- `users` 1 → N `course_reviews` (as student)
- `users` 1 → N `course_reviews` (as instructor)

---

#### `roles`
**الوصف**: جدول الأدوار (admin, instructor, student)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| name | VARCHAR(255) | UNIQUE, NOT NULL | اسم الدور |
| description | TEXT | NULL | الوصف |
| is_system | BOOLEAN | DEFAULT 0 | دور نظامي |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `roles` 1 → N `users`
- `roles` N → M `permissions` via `permission_role`

---

#### `permissions`
**الوصف**: جدول الصلاحيات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| name | VARCHAR(255) | NOT NULL | اسم الصلاحية |
| slug | VARCHAR(255) | UNIQUE, NOT NULL | المعرف الفريد |
| description | TEXT | NULL | الوصف |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `permissions` N → M `roles` via `permission_role`

---

#### `permission_role`
**الوصف**: جدول pivot بين الصلاحيات والأدوار

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| permission_id | BIGINT UNSIGNED | FK → permissions.id | معرف الصلاحية |
| role_id | BIGINT UNSIGNED | FK → roles.id | معرف الدور |

**Primary Key**: `(permission_id, role_id)`

---

### 2. LMS (Learning Management System) Tables

#### `categories`
**الوصف**: جدول تصنيفات الكورسات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| is_active | BOOLEAN | DEFAULT 1 | حالة النشاط |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `categories` 1 → N `courses`
- `categories` 1 → N `category_translations`

---

#### `category_translations`
**الوصف**: ترجمات التصنيفات (عربي/إنجليزي)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| category_id | BIGINT UNSIGNED | FK → categories.id | معرف التصنيف |
| locale | VARCHAR(10) | NOT NULL | اللغة (ar, en) |
| name | VARCHAR(255) | NOT NULL | الاسم |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Unique Constraint**: `(category_id, locale)`

---

#### `courses`
**الوصف**: جدول الكورسات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| title | VARCHAR(255) | NOT NULL | العنوان |
| slug | VARCHAR(255) | UNIQUE, NOT NULL | المعرف الفريد |
| code | VARCHAR(50) | UNIQUE, NOT NULL | كود الكورس |
| category_id | BIGINT UNSIGNED | FK → categories.id | معرف التصنيف |
| description | TEXT | NULL | الوصف |
| image_path | VARCHAR(255) | NULL | مسار الصورة |
| price | DECIMAL(10,2) | DEFAULT 0 | السعر |
| start_date | DATE | NULL | تاريخ البدء |
| end_date | DATE | NULL | تاريخ الانتهاء |
| session_count | INTEGER | DEFAULT 0 | عدد الجلسات |
| days_of_week | JSON | NULL | أيام الأسبوع |
| duration_weeks | INTEGER | DEFAULT 0 | المدة بالأسابيع |
| max_students | INTEGER | NULL | الحد الأقصى للطلاب |
| auto_generate_sessions | BOOLEAN | DEFAULT 0 | توليد جلسات تلقائياً |
| is_published | BOOLEAN | DEFAULT 0 | منشور |
| is_hidden | BOOLEAN | DEFAULT 0 | مخفي |
| status | ENUM | DEFAULT 'draft' | الحالة |
| delivery_type | ENUM | DEFAULT 'on-site' | نوع التوصيل |
| default_start_time | TIME | NULL | وقت البدء الافتراضي |
| default_end_time | TIME | NULL | وقت الانتهاء الافتراضي |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Status Values**: `draft`, `upcoming`, `running`, `completed`, `archived`

**Delivery Type Values**: `on-site`, `online`, `hybrid`

**Indexes**:
- `idx_courses_category_id` (category_id)
- `idx_courses_status` (status)
- `idx_courses_is_published` (is_published)
- `idx_courses_start_date` (start_date)

**Relationships**:
- `courses` N → 1 `categories`
- `courses` N → M `users` (instructors) via `course_instructor`
- `courses` 1 → N `sessions`
- `courses` 1 → N `enrollments`
- `courses` 1 → N `course_modules`
- `courses` 1 → N `quizzes`
- `courses` 1 → N `certificates`
- `courses` 1 → N `course_reviews`
- `courses` 1 → N `student_projects`

---

#### `course_instructor`
**الوصف**: جدول pivot بين الكورسات والمدربين

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| instructor_id | BIGINT UNSIGNED | FK → users.id | معرف المدرب |
| is_supervisor | BOOLEAN | DEFAULT 0 | مشرف رئيسي |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Primary Key**: `(course_id, instructor_id)`

---

#### `sessions`
**الوصف**: جدول الجلسات التعليمية

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| title | VARCHAR(255) | NOT NULL | العنوان |
| description | TEXT | NULL | الوصف |
| session_date | DATE | NOT NULL | تاريخ الجلسة |
| start_time | TIME | NOT NULL | وقت البدء |
| end_time | TIME | NOT NULL | وقت الانتهاء |
| status | ENUM | DEFAULT 'scheduled' | الحالة |
| note | TEXT | NULL | ملاحظات |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Status Values**: `scheduled`, `completed`, `cancelled`

**Indexes**:
- `idx_sessions_course_id` (course_id)
- `idx_sessions_session_date` (session_date)
- `idx_sessions_status` (status)

**Relationships**:
- `sessions` N → 1 `courses`
- `sessions` 1 → N `attendance`

---

#### `enrollments`
**الوصف**: جدول التسجيلات في الكورسات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| student_id | BIGINT UNSIGNED | FK → users.id | معرف الطالب |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| payment_status | ENUM | DEFAULT 'not_paid' | حالة الدفع |
| paid_amount | DECIMAL(10,2) | DEFAULT 0 | المبلغ المدفوع |
| total_amount | DECIMAL(10,2) | DEFAULT 0 | المبلغ الإجمالي |
| status | ENUM | DEFAULT 'pending' | الحالة |
| can_attend | BOOLEAN | DEFAULT 0 | يمكن الحضور |
| approved_by | BIGINT UNSIGNED | FK → users.id, NULL | معتمد من |
| approved_at | TIMESTAMP | NULL | تاريخ الاعتماد |
| note | TEXT | NULL | ملاحظات |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Payment Status Values**: `not_paid`, `partial`, `partially_paid`, `paid`, `refunded`, `rejected`

**Status Values**: `pending`, `approved`, `rejected`, `cancelled`

**Unique Constraint**: `(student_id, course_id)`

**Indexes**:
- `idx_enrollments_student_id` (student_id)
- `idx_enrollments_course_id` (course_id)
- `idx_enrollments_status` (status)
- `idx_enrollments_payment_status` (payment_status)

**Relationships**:
- `enrollments` N → 1 `users` (student)
- `enrollments` N → 1 `courses`
- `enrollments` N → 1 `users` (approved_by)
- `enrollments` 1 → N `student_progress`
- `enrollments` 1 → N `certificates`

---

#### `attendance`
**الوصف**: جدول الحضور

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| student_id | BIGINT UNSIGNED | FK → users.id | معرف الطالب |
| session_id | BIGINT UNSIGNED | FK → sessions.id | معرف الجلسة |
| status | ENUM | DEFAULT 'absent' | الحالة |
| attended_at | TIMESTAMP | NULL | تاريخ الحضور |
| note | TEXT | NULL | ملاحظات |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Status Values**: `present`, `absent`, `late`, `excused`

**Unique Constraint**: `(student_id, session_id)`

**Relationships**:
- `attendance` N → 1 `users`
- `attendance` N → 1 `sessions`

---

#### `course_modules`
**الوصف**: جدول Modules (الوحدات) في المنهج الدراسي

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| title | VARCHAR(255) | NOT NULL | العنوان |
| description | TEXT | NULL | الوصف |
| order | INTEGER | DEFAULT 0 | الترتيب |
| is_published | BOOLEAN | DEFAULT 1 | منشور |
| is_preview | BOOLEAN | DEFAULT 0 | معاينة |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `course_modules` N → 1 `courses`
- `course_modules` 1 → N `lessons`
- `course_modules` 1 → N `quizzes`

---

#### `lessons`
**الوصف**: جدول Lessons (الدروس) في Modules

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| module_id | BIGINT UNSIGNED | FK → course_modules.id | معرف الوحدة |
| title | VARCHAR(255) | NOT NULL | العنوان |
| description | TEXT | NULL | الوصف |
| content | TEXT | NULL | المحتوى (HTML) |
| video_url | VARCHAR(500) | NULL | رابط الفيديو |
| video_duration | INTEGER | NULL | مدة الفيديو (ثواني) |
| video_provider | VARCHAR(50) | NULL | مزود الفيديو |
| order | INTEGER | DEFAULT 0 | الترتيب |
| lesson_type | VARCHAR(50) | NULL | نوع الدرس |
| is_preview | BOOLEAN | DEFAULT 0 | معاينة |
| is_published | BOOLEAN | DEFAULT 1 | منشور |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `lessons` N → 1 `course_modules`
- `lessons` 1 → N `lesson_resources`
- `lessons` 1 → N `quizzes`
- `lessons` 1 → N `student_progress`

---

#### `lesson_resources`
**الوصف**: جدول Resources (الموارد) للدروس

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| lesson_id | BIGINT UNSIGNED | FK → lessons.id | معرف الدرس |
| title | VARCHAR(255) | NOT NULL | العنوان |
| type | ENUM | NOT NULL | النوع |
| file_path | VARCHAR(500) | NULL | مسار الملف |
| url | VARCHAR(500) | NULL | الرابط |
| order | INTEGER | DEFAULT 0 | الترتيب |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Type Values**: `file`, `link`

**Relationships**:
- `lesson_resources` N → 1 `lessons`

---

#### `quizzes`
**الوصف**: جدول الاختبارات (Quizzes)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| module_id | BIGINT UNSIGNED | FK → course_modules.id, NULL | معرف الوحدة |
| lesson_id | BIGINT UNSIGNED | FK → lessons.id, NULL | معرف الدرس |
| title | VARCHAR(255) | NOT NULL | العنوان |
| description | TEXT | NULL | الوصف |
| time_limit | INTEGER | NULL | الحد الزمني (دقائق) |
| passing_score | INTEGER | DEFAULT 60 | النسبة المطلوبة للنجاح |
| max_attempts | INTEGER | DEFAULT 1 | الحد الأقصى للمحاولات |
| show_results | BOOLEAN | DEFAULT 1 | عرض النتائج |
| is_published | BOOLEAN | DEFAULT 1 | منشور |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `quizzes` N → 1 `courses`
- `quizzes` N → 1 `course_modules` (nullable)
- `quizzes` N → 1 `lessons` (nullable)
- `quizzes` 1 → N `quiz_questions`
- `quizzes` 1 → N `quiz_attempts`

---

#### `quiz_questions`
**الوصف**: جدول أسئلة الاختبارات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| quiz_id | BIGINT UNSIGNED | FK → quizzes.id | معرف الاختبار |
| question | TEXT | NOT NULL | السؤال |
| type | ENUM | NOT NULL | النوع |
| options | JSON | NULL | الخيارات (للمتعدد) |
| correct_answer | TEXT/JSON | NOT NULL | الإجابة الصحيحة |
| points | INTEGER | DEFAULT 1 | النقاط |
| order | INTEGER | DEFAULT 0 | الترتيب |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Type Values**: `multiple_choice`, `true_false`, `short_answer`

**Relationships**:
- `quiz_questions` N → 1 `quizzes`

---

#### `quiz_attempts`
**الوصف**: جدول محاولات الاختبارات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| quiz_id | BIGINT UNSIGNED | FK → quizzes.id | معرف الاختبار |
| student_id | BIGINT UNSIGNED | FK → users.id | معرف الطالب |
| answers | JSON | NULL | الإجابات |
| score | INTEGER | DEFAULT 0 | النسبة المئوية |
| is_passed | BOOLEAN | DEFAULT 0 | نجح |
| started_at | TIMESTAMP | NULL | تاريخ البدء |
| submitted_at | TIMESTAMP | NULL | تاريخ التقديم |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `quiz_attempts` N → 1 `quizzes`
- `quiz_attempts` N → 1 `users`

---

#### `student_projects`
**الوصف**: جدول مشاريع الطلاب

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| student_id | BIGINT UNSIGNED | FK → users.id | معرف الطالب |
| title | VARCHAR(255) | NOT NULL | العنوان |
| description | TEXT | NULL | الوصف |
| file_path | VARCHAR(500) | NULL | مسار الملف |
| status | ENUM | DEFAULT 'pending' | الحالة |
| score | INTEGER | NULL | النقاط |
| feedback | TEXT | NULL | التعليقات |
| submitted_at | TIMESTAMP | NULL | تاريخ التقديم |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Status Values**: `pending`, `submitted`, `reviewed`

**Relationships**:
- `student_projects` N → 1 `courses`
- `student_projects` N → 1 `users`

---

#### `student_progress`
**الوصف**: جدول تقدم الطلاب

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| student_id | BIGINT UNSIGNED | FK → users.id | معرف الطالب |
| enrollment_id | BIGINT UNSIGNED | FK → enrollments.id | معرف التسجيل |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| module_id | BIGINT UNSIGNED | FK → course_modules.id, NULL | معرف الوحدة |
| lesson_id | BIGINT UNSIGNED | FK → lessons.id, NULL | معرف الدرس |
| type | ENUM | NOT NULL | النوع |
| is_completed | BOOLEAN | DEFAULT 0 | مكتمل |
| progress_percentage | INTEGER | DEFAULT 0 | نسبة التقدم |
| time_spent | INTEGER | DEFAULT 0 | الوقت المستغرق (ثواني) |
| started_at | TIMESTAMP | NULL | تاريخ البدء |
| completed_at | TIMESTAMP | NULL | تاريخ الإتمام |
| last_accessed_at | TIMESTAMP | NULL | آخر وصول |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Type Values**: `lesson`, `quiz`, `project`

**Relationships**:
- `student_progress` N → 1 `users`
- `student_progress` N → 1 `enrollments`
- `student_progress` N → 1 `courses`
- `student_progress` N → 1 `course_modules` (nullable)
- `student_progress` N → 1 `lessons` (nullable)

---

#### `certificates`
**الوصف**: جدول الشهادات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| student_id | BIGINT UNSIGNED | FK → users.id | معرف الطالب |
| enrollment_id | BIGINT UNSIGNED | FK → enrollments.id | معرف التسجيل |
| certificate_number | VARCHAR(100) | UNIQUE, NOT NULL | رقم الشهادة |
| template_path | VARCHAR(500) | NULL | مسار القالب |
| pdf_path | VARCHAR(500) | NULL | مسار PDF |
| issued_date | DATE | NOT NULL | تاريخ الإصدار |
| expiry_date | DATE | NULL | تاريخ الانتهاء |
| is_verified | BOOLEAN | DEFAULT 0 | تم التحقق |
| verification_code | VARCHAR(100) | UNIQUE, NULL | كود التحقق |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `certificates` N → 1 `courses`
- `certificates` N → 1 `users`
- `certificates` N → 1 `enrollments`

---

#### `course_reviews`
**الوصف**: جدول تقييمات الكورسات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| course_id | BIGINT UNSIGNED | FK → courses.id | معرف الكورس |
| student_id | BIGINT UNSIGNED | FK → users.id | معرف الطالب |
| instructor_id | BIGINT UNSIGNED | FK → users.id, NULL | معرف المدرب |
| rating_course | INTEGER | NOT NULL | تقييم الكورس (1-5) |
| rating_instructor | INTEGER | NULL | تقييم المدرب (1-5) |
| comment | TEXT | NULL | التعليق |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `course_reviews` N → 1 `courses`
- `course_reviews` N → 1 `users` (student)
- `course_reviews` N → 1 `users` (instructor, nullable)

---

### 3. CMS (Content Management System) Tables

#### `sliders`
**الوصف**: جدول البنرات (Sliders) في الصفحة الرئيسية

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| title | VARCHAR(255) | NOT NULL | العنوان |
| subtitle | VARCHAR(255) | NULL | العنوان الفرعي |
| description | TEXT | NULL | الوصف |
| image_path | VARCHAR(500) | NULL | مسار الصورة |
| link | VARCHAR(500) | NULL | الرابط |
| order | INTEGER | DEFAULT 0 | الترتيب |
| is_active | BOOLEAN | DEFAULT 1 | نشط |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

---

#### `testimonials`
**الوصف**: جدول شهادات الطلاب

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| student_name | VARCHAR(255) | NOT NULL | اسم الطالب |
| student_image | VARCHAR(500) | NULL | صورة الطالب |
| rating | INTEGER | NOT NULL | التقييم (1-5) |
| comment | TEXT | NOT NULL | التعليق |
| is_active | BOOLEAN | DEFAULT 1 | نشط |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

---

#### `contact_messages`
**الوصف**: جدول رسائل التواصل

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| name | VARCHAR(255) | NOT NULL | الاسم |
| email | VARCHAR(255) | NOT NULL | البريد الإلكتروني |
| phone | VARCHAR(20) | NULL | رقم الهاتف |
| subject | VARCHAR(255) | NOT NULL | الموضوع |
| message | TEXT | NOT NULL | الرسالة |
| is_resolved | BOOLEAN | DEFAULT 0 | تم الحل |
| resolved_at | TIMESTAMP | NULL | تاريخ الحل |
| resolved_by | BIGINT UNSIGNED | FK → users.id, NULL | تم الحل بواسطة |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Relationships**:
- `contact_messages` N → 1 `users` (resolved_by, nullable)

---

#### `settings`
**الوصف**: جدول الإعدادات العامة

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| key | VARCHAR(255) | UNIQUE, NOT NULL | المفتاح |
| value | TEXT | NULL | القيمة |
| type | VARCHAR(50) | NULL | النوع |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

---

#### `system_settings`
**الوصف**: جدول إعدادات النظام

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| key | VARCHAR(255) | UNIQUE, NOT NULL | المفتاح |
| value | TEXT | NULL | القيمة |
| type | VARCHAR(50) | NULL | النوع |
| group | VARCHAR(100) | NULL | المجموعة |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

---

### 4. Core Tables

#### `translations`
**الوصف**: جدول الترجمات

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| group | VARCHAR(100) | NOT NULL | المجموعة |
| key | VARCHAR(255) | NOT NULL | المفتاح |
| locale | VARCHAR(10) | NOT NULL | اللغة (ar, en) |
| value | TEXT | NOT NULL | القيمة |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

**Unique Constraint**: `(group, key, locale)`

---

#### `languages`
**الوصف**: جدول اللغات المدعومة

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AI | المعرف الفريد |
| code | VARCHAR(10) | UNIQUE, NOT NULL | كود اللغة (ar, en) |
| name | VARCHAR(100) | NOT NULL | الاسم |
| native_name | VARCHAR(100) | NOT NULL | الاسم الأصلي |
| is_active | BOOLEAN | DEFAULT 1 | نشط |
| is_default | BOOLEAN | DEFAULT 0 | افتراضي |
| created_at | TIMESTAMP | NULL | تاريخ الإنشاء |
| updated_at | TIMESTAMP | NULL | تاريخ التحديث |

---

## 🔗 Entity Relationships Summary

### One-to-Many (1 → N):
1. `roles` → `users`
2. `categories` → `courses`
3. `courses` → `sessions`
4. `courses` → `enrollments`
5. `courses` → `course_modules`
6. `course_modules` → `lessons`
7. `lessons` → `lesson_resources`
8. `courses` → `quizzes`
9. `quizzes` → `quiz_questions`
10. `quizzes` → `quiz_attempts`
11. `courses` → `student_projects`
12. `courses` → `certificates`
13. `courses` → `course_reviews`
14. `sessions` → `attendance`
15. `users` → `enrollments` (as student)
16. `users` → `attendance`
17. `enrollments` → `student_progress`
18. `enrollments` → `certificates`

### Many-to-Many (N → M):
1. `users` ↔ `courses` (instructors) via `course_instructor`
2. `roles` ↔ `permissions` via `permission_role`

---

## 📊 Database Statistics

### Total Tables: **27+**
### Total Indexes: **15+**
### Total Foreign Keys: **30+**
### Total Relationships: **40+**

---

## 🔧 Database Maintenance

### Backup Strategy:
- Daily backups recommended
- Weekly full backups
- Monthly archive backups

### Index Optimization:
- Regular index analysis
- Remove unused indexes
- Add missing indexes based on query patterns

### Performance Monitoring:
- Monitor slow queries
- Analyze query execution plans
- Optimize frequently used queries

---

**آخر تحديث**: 2025-01-27  
**الإصدار**: 2.0.0  
**Database Version**: MySQL 8.0+ / MariaDB 10.5+

