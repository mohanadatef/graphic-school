# 🗄️ Database ERD Notes - Graphic School

## ملاحظات عن قاعدة البيانات

لا يمكن رسم ERD فعلي، ولكن هذا الملف يصف الجداول والعلاقات بشكل نصي.

---

## الجداول الرئيسية

### 1. `users`
**الوصف**: جدول المستخدمين (طلاب، مدربين، admins)

**الأعمدة المهمة**:
- `id` (PK)
- `name`
- `email` (unique)
- `password` (hashed)
- `role_id` (FK → roles.id)
- `phone`
- `avatar_path`
- `address`
- `bio`
- `is_active` (boolean)
- `email_verified_at`
- `timestamps`

**العلاقات**:
- `users` N - 1 `roles` (Many users have one role)
- `users` 1 - N `enrollments` (One user has many enrollments as student)
- `users` N - M `courses` (Many users teach many courses) - through `course_instructor`
- `users` 1 - N `attendance` (One user has many attendance records)

---

### 2. `roles`
**الوصف**: جدول الأدوار (admin, instructor, student)

**الأعمدة المهمة**:
- `id` (PK)
- `name` (unique)
- `description`
- `is_system` (boolean)
- `timestamps`

**العلاقات**:
- `roles` 1 - N `users` (One role has many users)
- `roles` N - M `permissions` (Many roles have many permissions) - through `permission_role`

---

### 3. `permissions`
**الوصف**: جدول الصلاحيات

**الأعمدة المهمة**:
- `id` (PK)
- `name`
- `slug` (unique)
- `description`
- `timestamps`

**العلاقات**:
- `permissions` N - M `roles` (Many permissions belong to many roles) - through `permission_role`

---

### 4. `categories`
**الوصف**: جدول تصنيفات الكورسات

**الأعمدة المهمة**:
- `id` (PK)
- `is_active` (boolean)
- `timestamps`

**ملاحظة**: الاسم (name) موجود في `category_translations` لدعم متعدد اللغات

**العلاقات**:
- `categories` 1 - N `courses` (One category has many courses)
- `categories` 1 - N `category_translations` (One category has many translations)

---

### 5. `category_translations`
**الوصف**: ترجمات التصنيفات (عربي/إنجليزي)

**الأعمدة المهمة**:
- `id` (PK)
- `category_id` (FK → categories.id)
- `locale` (ar, en)
- `name`
- `timestamps`

**العلاقات**:
- `category_translations` N - 1 `categories` (Many translations belong to one category)

---

### 6. `courses`
**الوصف**: جدول الكورسات

**الأعمدة المهمة**:
- `id` (PK)
- `title`
- `slug` (unique)
- `code` (unique)
- `category_id` (FK → categories.id)
- `description` (text)
- `image_path`
- `price` (decimal)
- `start_date`
- `end_date`
- `session_count` (integer)
- `days_of_week` (JSON array)
- `duration_weeks` (integer)
- `max_students` (integer)
- `auto_generate_sessions` (boolean)
- `is_published` (boolean)
- `is_hidden` (boolean)
- `status` (enum: draft, upcoming, running, completed, archived)
- `delivery_type` (enum: on-site, online, hybrid)
- `default_start_time`
- `default_end_time`
- `timestamps`

**العلاقات**:
- `courses` N - 1 `categories` (Many courses belong to one category)
- `courses` N - M `users` (Many courses taught by many instructors) - through `course_instructor`
- `courses` 1 - N `sessions` (One course has many sessions)
- `courses` 1 - N `enrollments` (One course has many enrollments)
- `courses` 1 - N `course_modules` (One course has many modules)
- `courses` 1 - N `quizzes` (One course has many quizzes)
- `courses` 1 - N `certificates` (One course has many certificates)
- `courses` 1 - N `course_reviews` (One course has many reviews)

---

### 7. `course_instructor`
**الوصف**: جدول pivot بين الكورسات والمدربين

**الأعمدة المهمة**:
- `course_id` (FK → courses.id)
- `instructor_id` (FK → users.id)
- `is_supervisor` (boolean)
- `timestamps`

**العلاقات**:
- `course_instructor` N - 1 `courses`
- `course_instructor` N - 1 `users`

---

### 8. `sessions`
**الوصف**: جدول الجلسات التعليمية

**الأعمدة المهمة**:
- `id` (PK)
- `course_id` (FK → courses.id)
- `title`
- `description` (text)
- `session_date` (date)
- `start_time` (time)
- `end_time` (time)
- `status` (enum: scheduled, completed, cancelled)
- `note` (text)
- `timestamps`

**العلاقات**:
- `sessions` N - 1 `courses` (Many sessions belong to one course)
- `sessions` 1 - N `attendance` (One session has many attendance records)

---

### 9. `enrollments`
**الوصف**: جدول التسجيلات في الكورسات

**الأعمدة المهمة**:
- `id` (PK)
- `student_id` (FK → users.id)
- `course_id` (FK → courses.id)
- `payment_status` (enum: not_paid, partial, partially_paid, paid, refunded, rejected)
- `paid_amount` (decimal)
- `total_amount` (decimal)
- `status` (enum: pending, approved, rejected, cancelled)
- `can_attend` (boolean)
- `approved_by` (FK → users.id, nullable)
- `approved_at` (timestamp, nullable)
- `note` (text)
- `timestamps`
- Unique: `(student_id, course_id)`

**العلاقات**:
- `enrollments` N - 1 `users` (Many enrollments belong to one student)
- `enrollments` N - 1 `courses` (Many enrollments belong to one course)
- `enrollments` 1 - N `student_progress` (One enrollment has many progress records)
- `enrollments` 1 - N `certificates` (One enrollment can have certificates)

---

### 10. `attendance`
**الوصف**: جدول الحضور

**الأعمدة المهمة**:
- `id` (PK)
- `student_id` (FK → users.id)
- `session_id` (FK → sessions.id)
- `status` (enum: present, absent, late, excused)
- `attended_at` (timestamp)
- `note` (text)
- `timestamps`

**العلاقات**:
- `attendance` N - 1 `users` (Many attendance records belong to one student)
- `attendance` N - 1 `sessions` (Many attendance records belong to one session)

---

### 11. `course_modules`
**الوصف**: جدول Modules (الوحدات) في المنهج الدراسي

**الأعمدة المهمة**:
- `id` (PK)
- `course_id` (FK → courses.id)
- `title`
- `description` (text)
- `order` (integer)
- `is_published` (boolean)
- `is_preview` (boolean)
- `timestamps`

**العلاقات**:
- `course_modules` N - 1 `courses` (Many modules belong to one course)
- `course_modules` 1 - N `lessons` (One module has many lessons)
- `course_modules` 1 - N `quizzes` (One module can have quizzes)

---

### 12. `lessons`
**الوصف**: جدول Lessons (الدروس) في Modules

**الأعمدة المهمة**:
- `id` (PK)
- `module_id` (FK → course_modules.id)
- `title`
- `description` (text)
- `content` (text, HTML)
- `video_url`
- `video_duration` (integer, seconds)
- `video_provider` (string)
- `order` (integer)
- `lesson_type` (string)
- `is_preview` (boolean)
- `is_published` (boolean)
- `timestamps`

**العلاقات**:
- `lessons` N - 1 `course_modules` (Many lessons belong to one module)
- `lessons` 1 - N `lesson_resources` (One lesson has many resources)
- `lessons` 1 - N `quizzes` (One lesson can have quizzes)
- `lessons` 1 - N `student_progress` (One lesson has many progress records)

---

### 13. `lesson_resources`
**الوصف**: جدول Resources (الموارد) للدروس

**الأعمدة المهمة**:
- `id` (PK)
- `lesson_id` (FK → lessons.id)
- `title`
- `type` (enum: file, link)
- `file_path` (nullable)
- `url` (nullable)
- `order` (integer)
- `timestamps`

**العلاقات**:
- `lesson_resources` N - 1 `lessons` (Many resources belong to one lesson)

---

### 14. `quizzes`
**الوصف**: جدول الاختبارات (Quizzes)

**الأعمدة المهمة**:
- `id` (PK)
- `course_id` (FK → courses.id)
- `module_id` (FK → course_modules.id, nullable)
- `lesson_id` (FK → lessons.id, nullable)
- `title`
- `description` (text)
- `time_limit` (integer, minutes)
- `passing_score` (integer, percentage)
- `max_attempts` (integer)
- `show_results` (boolean)
- `is_published` (boolean)
- `timestamps`

**العلاقات**:
- `quizzes` N - 1 `courses` (Many quizzes belong to one course)
- `quizzes` N - 1 `course_modules` (Many quizzes belong to one module, nullable)
- `quizzes` N - 1 `lessons` (Many quizzes belong to one lesson, nullable)
- `quizzes` 1 - N `quiz_questions` (One quiz has many questions)
- `quizzes` 1 - N `quiz_attempts` (One quiz has many attempts)

---

### 15. `quiz_questions`
**الوصف**: جدول أسئلة الاختبارات

**الأعمدة المهمة**:
- `id` (PK)
- `quiz_id` (FK → quizzes.id)
- `question` (text)
- `type` (enum: multiple_choice, true_false, short_answer)
- `options` (JSON array, for multiple choice)
- `correct_answer` (text/JSON)
- `points` (integer)
- `order` (integer)
- `timestamps`

**العلاقات**:
- `quiz_questions` N - 1 `quizzes` (Many questions belong to one quiz)

---

### 16. `quiz_attempts`
**الوصف**: جدول محاولات الاختبارات

**الأعمدة المهمة**:
- `id` (PK)
- `quiz_id` (FK → quizzes.id)
- `student_id` (FK → users.id)
- `answers` (JSON)
- `score` (integer, percentage)
- `is_passed` (boolean)
- `started_at` (timestamp)
- `submitted_at` (timestamp)
- `timestamps`

**العلاقات**:
- `quiz_attempts` N - 1 `quizzes` (Many attempts belong to one quiz)
- `quiz_attempts` N - 1 `users` (Many attempts belong to one student)

---

### 17. `student_projects`
**الوصف**: جدول مشاريع الطلاب

**الأعمدة المهمة**:
- `id` (PK)
- `course_id` (FK → courses.id)
- `student_id` (FK → users.id)
- `title`
- `description` (text)
- `file_path`
- `status` (enum: pending, submitted, reviewed)
- `score` (integer, nullable)
- `feedback` (text, nullable)
- `submitted_at` (timestamp)
- `timestamps`

**العلاقات**:
- `student_projects` N - 1 `courses` (Many projects belong to one course)
- `student_projects` N - 1 `users` (Many projects belong to one student)

---

### 18. `student_progress`
**الوصف**: جدول تقدم الطلاب

**الأعمدة المهمة**:
- `id` (PK)
- `student_id` (FK → users.id)
- `enrollment_id` (FK → enrollments.id)
- `course_id` (FK → courses.id)
- `module_id` (FK → course_modules.id, nullable)
- `lesson_id` (FK → lessons.id, nullable)
- `type` (enum: lesson, quiz, project)
- `is_completed` (boolean)
- `progress_percentage` (integer)
- `time_spent` (integer, seconds)
- `started_at` (timestamp)
- `completed_at` (timestamp, nullable)
- `last_accessed_at` (timestamp)
- `timestamps`

**العلاقات**:
- `student_progress` N - 1 `users` (Many progress records belong to one student)
- `student_progress` N - 1 `enrollments` (Many progress records belong to one enrollment)
- `student_progress` N - 1 `courses` (Many progress records belong to one course)
- `student_progress` N - 1 `course_modules` (Many progress records belong to one module, nullable)
- `student_progress` N - 1 `lessons` (Many progress records belong to one lesson, nullable)

---

### 19. `certificates`
**الوصف**: جدول الشهادات

**الأعمدة المهمة**:
- `id` (PK)
- `course_id` (FK → courses.id)
- `student_id` (FK → users.id)
- `enrollment_id` (FK → enrollments.id)
- `certificate_number` (string, unique)
- `template_path`
- `pdf_path`
- `issued_date` (date)
- `expiry_date` (date, nullable)
- `is_verified` (boolean)
- `verification_code` (string, unique)
- `timestamps`

**العلاقات**:
- `certificates` N - 1 `courses` (Many certificates belong to one course)
- `certificates` N - 1 `users` (Many certificates belong to one student)
- `certificates` N - 1 `enrollments` (Many certificates belong to one enrollment)

---

### 20. `course_reviews`
**الوصف**: جدول تقييمات الكورسات

**الأعمدة المهمة**:
- `id` (PK)
- `course_id` (FK → courses.id)
- `student_id` (FK → users.id)
- `instructor_id` (FK → users.id, nullable)
- `rating_course` (integer, 1-5)
- `rating_instructor` (integer, 1-5, nullable)
- `comment` (text, nullable)
- `timestamps`

**العلاقات**:
- `course_reviews` N - 1 `courses` (Many reviews belong to one course)
- `course_reviews` N - 1 `users` (Many reviews belong to one student)
- `course_reviews` N - 1 `users` (Many reviews rate one instructor, nullable)

---

### 21. `sliders`
**الوصف**: جدول البنرات (Sliders) في الصفحة الرئيسية

**الأعمدة المهمة**:
- `id` (PK)
- `title`
- `subtitle`
- `description` (text)
- `image_path`
- `link` (nullable)
- `order` (integer)
- `is_active` (boolean)
- `timestamps`

---

### 22. `testimonials`
**الوصف**: جدول شهادات الطلاب

**الأعمدة المهمة**:
- `id` (PK)
- `student_name`
- `student_image` (nullable)
- `rating` (integer, 1-5)
- `comment` (text)
- `is_active` (boolean)
- `timestamps`

---

### 23. `contact_messages`
**الوصف**: جدول رسائل التواصل

**الأعمدة المهمة**:
- `id` (PK)
- `name`
- `email`
- `phone` (nullable)
- `subject`
- `message` (text)
- `is_resolved` (boolean)
- `resolved_at` (timestamp, nullable)
- `resolved_by` (FK → users.id, nullable)
- `timestamps`

**العلاقات**:
- `contact_messages` N - 1 `users` (Many messages resolved by one admin, nullable)

---

### 24. `settings`
**الوصف**: جدول الإعدادات العامة

**الأعمدة المهمة**:
- `id` (PK)
- `key` (unique)
- `value` (text)
- `type` (string)
- `timestamps`

---

### 25. `system_settings`
**الوصف**: جدول إعدادات النظام

**الأعمدة المهمة**:
- `id` (PK)
- `key` (unique)
- `value` (text)
- `type` (string)
- `group` (string)
- `timestamps`

---

### 26. `translations`
**الوصف**: جدول الترجمات

**الأعمدة المهمة**:
- `id` (PK)
- `group` (string)
- `key` (string)
- `locale` (ar, en)
- `value` (text)
- `timestamps`

---

### 27. `languages`
**الوصف**: جدول اللغات المدعومة

**الأعمدة المهمة**:
- `id` (PK)
- `code` (unique, ar, en)
- `name`
- `native_name`
- `is_active` (boolean)
- `is_default` (boolean)
- `timestamps`

---

## العلاقات الرئيسية

### One-to-Many (1 - N):

1. `roles` 1 - N `users`
2. `categories` 1 - N `courses`
3. `courses` 1 - N `sessions`
4. `courses` 1 - N `enrollments`
5. `courses` 1 - N `course_modules`
6. `course_modules` 1 - N `lessons`
7. `lessons` 1 - N `lesson_resources`
8. `courses` 1 - N `quizzes`
9. `quizzes` 1 - N `quiz_questions`
10. `quizzes` 1 - N `quiz_attempts`
11. `courses` 1 - N `student_projects`
12. `courses` 1 - N `certificates`
13. `courses` 1 - N `course_reviews`
14. `sessions` 1 - N `attendance`
15. `users` 1 - N `enrollments` (as student)
16. `users` 1 - N `attendance`
17. `enrollments` 1 - N `student_progress`
18. `enrollments` 1 - N `certificates`

### Many-to-Many (N - M):

1. `users` N - M `courses` (instructors) - through `course_instructor`
2. `roles` N - M `permissions` - through `permission_role`

### Polymorphic (إن وجد):

لا يوجد علاقات Polymorphic حالياً.

---

## Indexes

### Performance Indexes (تم إضافتها):
- `courses`: category_id, status, is_published, start_date
- `enrollments`: student_id, course_id, status, payment_status
- `sessions`: course_id, session_date, status
- `users`: role_id, email, is_active

---

## ملاحظات إضافية

### Soft Deletes:
- بعض الجداول قد تستخدم Soft Deletes (deleted_at column)

### Timestamps:
- جميع الجداول تحتوي على `created_at` و `updated_at`

### Foreign Keys:
- جميع Foreign Keys محمية بـ `cascadeOnDelete` أو `nullOnDelete` حسب الحاجة

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

