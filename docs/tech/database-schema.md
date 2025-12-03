# Database Schema

## Overview

This document describes the complete database schema for Graphic School 2.0 LMC system. The schema follows the business flow: **Course → Group → Session → Enrollment → Attendance → Certificate**, with additional support for Community, CMS, and Localization.

**Database:** MySQL 8.0+ / MariaDB 10.6+  
**ORM:** Laravel Eloquent  
**Migration System:** Laravel Migrations

## Core Tables

### 1. Users & Authentication

#### `users`
Core user table supporting Admin, Instructor, and Student roles.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `name` | varchar(255) | User full name |
| `email` | varchar(255) | Email (unique) |
| `phone` | varchar(20) | Phone number |
| `password` | varchar(255) | Hashed password |
| `role_id` | bigint | Foreign key to `roles.id` |
| `email_verified_at` | timestamp | Email verification timestamp |
| `remember_token` | varchar(100) | Remember token |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(Role)` - User role
- `hasMany(Enrollment)` - Student enrollments
- `hasMany(CommunityPost)` - Community posts

#### `roles`
Role definitions (admin, instructor, student).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `name` | varchar(255) | Role name (unique) |
| `display_name` | varchar(255) | Display name |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `password_reset_tokens`
Password reset tokens (Laravel default).

#### `personal_access_tokens`
API tokens (Laravel Sanctum).

---

### 2. Courses & Groups

#### `courses`
Course management table. **Course is the TOP learning entity** (NO programs/tracks/batches).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `title` | varchar(255) | Course title |
| `slug` | varchar(255) | URL slug (unique) |
| `code` | varchar(50) | Course code |
| `category_id` | bigint | Foreign key to `categories.id` |
| `description` | text | Course description |
| `image_path` | varchar(255) | Course image |
| `price` | decimal(10,2) | **Required** course price |
| `currency_id` | bigint | Foreign key to `currencies.id` |
| `language_id` | bigint | Foreign key to `languages.id` |
| `status` | varchar(50) | Status (draft, published, etc.) |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(Category)` - Course category
- `belongsToMany(Instructor)` - Multiple instructors via `course_instructor`
- `hasMany(Group)` - Multiple groups per course
- `hasMany(Enrollment)` - Course enrollments
- `hasMany(Certificate)` - Course certificates

#### `course_instructor`
Pivot table: Course ↔ Instructors (many-to-many).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `course_id` | bigint | Foreign key to `courses.id` |
| `instructor_id` | bigint | Foreign key to `users.id` |
| `is_supervisor` | boolean | Is supervisor |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `groups`
Group management table. Belongs to Course, contains Sessions.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `course_id` | bigint | Foreign key to `courses.id` |
| `code` | varchar(50) | Group code |
| `name` | varchar(255) | Group name |
| `start_date` | date | **Group start date** |
| `end_date` | date | **Group end date** |
| `notes` | text | **Group notes** |
| `capacity` | int | Maximum capacity |
| `room` | varchar(100) | Room/location |
| `is_active` | boolean | Is active |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(Course)` - Parent course
- `belongsToMany(Instructor)` - Multiple instructors via `group_instructor`
- `belongsToMany(Student)` - Students via `group_student`
- `hasMany(GroupSession)` - Sessions in group
- `hasMany(Enrollment)` - Group enrollments

#### `group_instructor`
Pivot table: Group ↔ Instructors (many-to-many).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `group_id` | bigint | Foreign key to `groups.id` |
| `instructor_id` | bigint | Foreign key to `users.id` |
| `assigned_at` | timestamp | Assignment timestamp |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `group_student`
Pivot table: Group ↔ Students (many-to-many).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `group_id` | bigint | Foreign key to `groups.id` |
| `student_id` | bigint | Foreign key to `users.id` |
| `enrolled_at` | timestamp | Enrollment timestamp |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

---

### 3. Sessions

#### `session_templates`
Session templates (optional, for course organization).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `course_id` | bigint | Foreign key to `courses.id` |
| `title` | varchar(255) | Session template title |
| `session_order` | int | Order number |
| `description` | text | Description |
| `duration_minutes` | int | Duration in minutes |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `group_sessions`
Actual sessions/lectures in groups.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `group_id` | bigint | Foreign key to `groups.id` |
| `session_template_id` | bigint | Foreign key to `session_templates.id` (nullable) |
| `instructor_id` | bigint | Foreign key to `users.id` (nullable, **optional**) |
| `title` | varchar(255) | Session title |
| `session_date` | date | **Session date** |
| `start_time` | time | **Start time** |
| `end_time` | time | **End time** |
| `notes` | text | **Session notes** |
| `status` | varchar(50) | Status (scheduled, completed, cancelled) |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(Group)` - Parent group
- `belongsTo(SessionTemplate)` - Optional template
- `belongsTo(Instructor)` - Optional instructor
- `hasMany(Attendance)` - Attendance records

---

### 4. Enrollments

#### `enrollments`
Student enrollment management. **Flow:** Student → Course → Admin Approval → Group Assignment.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `student_id` | bigint | Foreign key to `users.id` |
| `course_id` | bigint | Foreign key to `courses.id` |
| `group_id` | bigint | Foreign key to `groups.id` (nullable, assigned on approval) |
| `status` | varchar(50) | Status: pending, approved, rejected, withdrawn |
| `payment_status` | varchar(50) | Payment status: not_paid, paid, partial |
| `can_attend` | boolean | Can attend sessions |
| `approved_by` | bigint | Foreign key to `users.id` (admin) |
| `approved_at` | timestamp | Approval timestamp |
| `note` | text | Admin note |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(Student)` - Student user
- `belongsTo(Course)` - Enrolled course
- `belongsTo(Group)` - Assigned group (after approval)
- `belongsTo(ApprovedBy)` - Admin who approved
- `hasMany(Certificate)` - Certificates issued

**Business Rules:**
- One active enrollment per student per course
- Group assignment happens on approval
- Attendance slots created automatically on approval

---

### 5. Attendance

#### `attendance`
Attendance records. **Status:** present, absent, late (NO QR attendance).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `group_session_id` | bigint | Foreign key to `group_sessions.id` |
| `student_id` | bigint | Foreign key to `users.id` |
| `status` | varchar(50) | Status: present, absent, late |
| `marked_by` | bigint | Foreign key to `users.id` (instructor/admin) |
| `marked_at` | timestamp | Marked timestamp |
| `notes` | text | Optional notes |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(GroupSession)` - Session
- `belongsTo(Student)` - Student
- `belongsTo(MarkedBy)` - Instructor/admin who marked

**Business Rules:**
- One attendance record per student per session
- Attendance slots created when enrollment is approved
- Instructor marks attendance manually (no QR)

---

### 6. Certificates

#### `certificates`
Certificate management with QR verification.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `course_id` | bigint | Foreign key to `courses.id` |
| `group_id` | bigint | Foreign key to `groups.id` |
| `student_id` | bigint | Foreign key to `users.id` |
| `instructor_id` | bigint | Foreign key to `users.id` (nullable) |
| `enrollment_id` | bigint | Foreign key to `enrollments.id` |
| `certificate_number` | varchar(100) | Unique certificate number |
| `verification_code` | varchar(100) | Unique verification code |
| `qr_code` | varchar(255) | QR code path |
| `template_path` | varchar(255) | Certificate template path |
| `pdf_path` | varchar(255) | Generated PDF path |
| `issued_date` | date | Issue date |
| `expiry_date` | date | Expiry date (nullable) |
| `is_verified` | boolean | Is verified |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(Course)` - Course
- `belongsTo(Group)` - Group
- `belongsTo(Student)` - Student
- `belongsTo(Instructor)` - Instructor (optional)
- `belongsTo(Enrollment)` - Related enrollment

**Business Rules:**
- Certificate includes: Student, Course, Group, Instructor, Date, Verification Code, QR Code
- Public verification at `/certificates/verify/{code}`
- QR code opens verification page

---

### 7. Community

#### `community_posts`
Community posts (NO program/batch references, NO gamification).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | Foreign key to `users.id` |
| `group_id` | bigint | Foreign key to `groups.id` (nullable) |
| `title` | varchar(255) | Post title |
| `body` | longtext | Post content |
| `attachments` | json | Attachments (nullable) |
| `is_pinned` | boolean | Is pinned |
| `is_locked` | boolean | Is locked |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(User)` - Post author
- `belongsTo(Group)` - Optional group association
- `hasMany(CommunityComment)` - Comments
- `hasMany(CommunityLike)` - Likes (polymorphic)

#### `community_comments`
Comments on posts.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `post_id` | bigint | Foreign key to `community_posts.id` |
| `user_id` | bigint | Foreign key to `users.id` |
| `body` | text | Comment content |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

**Relationships:**
- `belongsTo(Post)` - Parent post
- `belongsTo(User)` - Comment author
- `hasMany(CommunityReply)` - Replies
- `hasMany(CommunityLike)` - Likes (polymorphic)

#### `community_replies`
Replies to comments.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `comment_id` | bigint | Foreign key to `community_comments.id` |
| `user_id` | bigint | Foreign key to `users.id` |
| `body` | text | Reply content |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `community_likes`
Polymorphic likes (posts, comments).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | Foreign key to `users.id` |
| `likeable_type` | varchar(255) | Model type (CommunityPost, CommunityComment) |
| `likeable_id` | bigint | Model ID |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `community_reports`
Reports for moderation (Admin only).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | Foreign key to `users.id` (reporter) |
| `reportable_type` | varchar(255) | Model type (CommunityPost, CommunityComment) |
| `reportable_id` | bigint | Model ID |
| `reason` | text | Report reason |
| `status` | varchar(50) | Status: pending, resolved, dismissed |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

---

### 8. Website & CMS

#### `website_settings`
Website settings (branding, activation, localization).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `is_activated` | boolean | Website activated |
| `branding` | json | Branding settings (logo, colors, fonts) |
| `default_language_id` | bigint | Foreign key to `languages.id` |
| `default_currency_id` | bigint | Foreign key to `currencies.id` |
| `default_country_id` | bigint | Foreign key to `countries.id` |
| `timezone` | varchar(100) | Timezone |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `pages`
CMS pages.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `slug` | varchar(255) | URL slug (unique) |
| `title` | json | Multi-language title |
| `content` | json | Multi-language content |
| `template` | varchar(100) | Template name |
| `seo_fields` | json | SEO metadata |
| `is_active` | boolean | Is active |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `page_blocks`
Page builder blocks.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `page_id` | bigint | Foreign key to `pages.id` |
| `block_type` | varchar(100) | Block type (hero, features, etc.) |
| `block_data` | json | Block data |
| `order` | int | Display order |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

---

### 9. Localization

#### `languages`
Languages (default: en, supports RTL for Arabic).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `code` | varchar(10) | Language code (en, ar, etc.) |
| `name` | varchar(100) | Language name |
| `is_active` | boolean | Is active |
| `is_default` | boolean | Is default |
| `is_rtl` | boolean | Right-to-left support |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `currencies`
Currencies (default: EGP).

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `code` | varchar(10) | Currency code (EGP, USD, etc.) |
| `name` | varchar(100) | Currency name |
| `symbol` | varchar(10) | Currency symbol |
| `is_active` | boolean | Is active |
| `is_default` | boolean | Is default |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

#### `countries`
Countries.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `code` | varchar(10) | Country code |
| `name` | varchar(100) | Country name |
| `is_active` | boolean | Is active |
| `is_default` | boolean | Is default |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

---

### 10. Categories

#### `categories`
Course categories.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `slug` | varchar(255) | URL slug |
| `name` | json | Multi-language name |
| `description` | json | Multi-language description |
| `image_path` | varchar(255) | Category image |
| `is_active` | boolean | Is active |
| `created_at` | timestamp | Created timestamp |
| `updated_at` | timestamp | Updated timestamp |

---

## Entity Relationship Diagram (Text-Based)

```
Users (Admin, Instructor, Student)
  ├── hasMany(Enrollment)
  ├── hasMany(CommunityPost)
  └── hasMany(Certificate)

Courses (TOP Learning Entity)
  ├── belongsToMany(Instructors) via course_instructor
  ├── hasMany(Groups)
  ├── hasMany(Enrollment)
  ├── hasMany(Certificate)
  └── belongsTo(Category)

Groups
  ├── belongsTo(Course)
  ├── belongsToMany(Instructors) via group_instructor
  ├── belongsToMany(Students) via group_student
  ├── hasMany(GroupSession)
  ├── hasMany(Enrollment)
  └── hasMany(CommunityPost)

GroupSessions
  ├── belongsTo(Group)
  ├── belongsTo(SessionTemplate) [optional]
  ├── belongsTo(Instructor) [optional]
  └── hasMany(Attendance)

Enrollments
  ├── belongsTo(Student)
  ├── belongsTo(Course)
  ├── belongsTo(Group) [assigned on approval]
  └── hasMany(Certificate)

Attendance
  ├── belongsTo(GroupSession)
  ├── belongsTo(Student)
  └── belongsTo(MarkedBy) [Instructor/Admin]

Certificates
  ├── belongsTo(Course)
  ├── belongsTo(Group)
  ├── belongsTo(Student)
  ├── belongsTo(Instructor) [optional]
  └── belongsTo(Enrollment)

CommunityPosts
  ├── belongsTo(User)
  ├── belongsTo(Group) [optional]
  ├── hasMany(CommunityComment)
  └── hasMany(CommunityLike) [polymorphic]

CommunityComments
  ├── belongsTo(Post)
  ├── belongsTo(User)
  ├── hasMany(CommunityReply)
  └── hasMany(CommunityLike) [polymorphic]
```

## Indexes

### Primary Indexes
- All tables have `id` as primary key
- All foreign keys are indexed

### Key Indexes
- `users.email` - Unique index
- `courses.slug` - Unique index
- `enrollments.student_id, course_id` - Composite index
- `attendance.group_session_id, student_id` - Composite index
- `certificates.verification_code` - Unique index
- `community_posts.user_id, group_id` - Indexes
- `community_likes.user_id, likeable_type, likeable_id` - Composite index

## Business Rules Summary

### Enrollment Rules
1. One active enrollment per student per course
2. Enrollment status: pending → approved/rejected → withdrawn
3. Group assignment happens on approval
4. Attendance slots created automatically on approval

### Attendance Rules
1. Manual attendance marking (NO QR)
2. Status: present, absent, late
3. One attendance record per student per session
4. Instructor/admin marks attendance

### Certificate Rules
1. Certificate includes: Student, Course, Group, Instructor, Date, Verification Code, QR Code
2. Unique verification code for public verification
3. QR code opens verification page

### Community Rules
1. Posts, Comments, Replies structure
2. NO gamification (XP, points, badges)
3. Admin moderation via reports

## Migration Strategy

All schema changes are managed via Laravel migrations:

- **Create tables:** `YYYY_MM_DD_HHMMSS_create_{table}_table.php`
- **Alter tables:** `YYYY_MM_DD_HHMMSS_alter_{table}_add_{columns}.php`
- **Drop tables:** `YYYY_MM_DD_HHMMSS_drop_{table}_table.php`

**Key Migrations:**
- `2025_01_28_200000_update_certificates_table_for_business_spec.php` - Added group_id, instructor_id, qr_code
- `2025_01_28_200001_remove_program_batch_from_community_posts.php` - Cleaned community posts
- `2025_01_28_200002_update_groups_table_add_dates_notes.php` - Added start_date, end_date, notes
- `2025_01_28_200003_add_instructor_id_to_group_sessions.php` - Added optional instructor_id

## Removed Tables

The following tables were removed as part of cleanup:
- ❌ `assignments` - Assignment management
- ❌ `assignment_submissions` - Assignment submissions
- ❌ `subscription_plans` - Subscription plans
- ❌ `subscription_usage_trackers` - Usage tracking
- ❌ `subscription_invoices` - Subscription invoices
- ❌ All gamification tables (XP, points, badges, levels)

---

**Schema Status:** ✅ Aligned with final business specification

