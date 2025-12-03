# Backend Models

## Overview

This document describes all models in the system, their relationships, and business logic.

## Core Models

### User (`Modules/ACL/Users/Models/User`)

**Purpose:** Represents all users in the system (admin, instructor, student).

**Key Attributes:**
- `name` - User full name
- `email` - Unique email address
- `password` - Hashed password
- `role_id` - User role (admin, instructor, student)
- `phone` - Phone number
- `avatar_path` - Profile picture path
- `address` - Physical address
- `bio` - Biography
- `is_active` - Active status

**Relationships:**
- `belongsTo` Role
- `hasMany` Enrollments (as student)
- `belongsToMany` Courses (as instructor)
- `hasMany` Attendance (as student)

**Business Rules:**
- Email must be unique
- Password is hashed using bcrypt
- Role determines access level
- Active status controls login ability

### Role (`Modules/ACL/Roles/Models/Role`)

**Purpose:** Defines user roles and permissions.

**Key Attributes:**
- `name` - Role name (e.g., "Admin", "Instructor", "Student")
- `slug` - URL-friendly identifier
- `description` - Role description
- `is_active` - Active status

**Relationships:**
- `hasMany` Users
- `belongsToMany` Permissions

**Business Rules:**
- Slug must be unique
- Default roles: admin, instructor, student

### Permission (`Modules/ACL/Permissions/Models/Permission`)

**Purpose:** Fine-grained access control.

**Key Attributes:**
- `name` - Permission name
- `slug` - Unique identifier
- `description` - Permission description

**Relationships:**
- `belongsToMany` Roles

### Category (`Modules/LMS/Categories/Models/Category`)

**Purpose:** Organizes courses into categories.

**Key Attributes:**
- `name` - Category name
- `slug` - URL-friendly identifier
- `description` - Category description
- `image_path` - Category image
- `is_active` - Active status
- `sort_order` - Display order

**Relationships:**
- `hasMany` Courses

### Course (`Modules/LMS/Courses/Models/Course`)

**Purpose:** Main learning program.

**Key Attributes:**
- `title` - Course title
- `slug` - URL-friendly identifier
- `code` - Unique course code
- `category_id` - Category
- `description` - Course description
- `image_path` - Course image
- `price` - Course price
- `start_date` - Start date
- `end_date` - End date
- `session_count` - Number of sessions
- `days_of_week` - Days when sessions occur (JSON)
- `duration_weeks` - Course duration
- `max_students` - Maximum students
- `auto_generate_sessions` - Auto-generate sessions flag
- `is_published` - Published status
- `is_hidden` - Hidden from public
- `status` - draft, upcoming, running, completed, archived
- `delivery_type` - on-site, online, hybrid
- `default_start_time` - Default session start time
- `default_end_time` - Default session end time

**Relationships:**
- `belongsTo` Category
- `belongsToMany` Users (instructors)
- `hasMany` Groups
- `hasMany` SessionTemplates
- `hasManyThrough` GroupSessions (via Groups)
- `hasMany` Enrollments

**Business Rules:**
- Course code must be unique
- Course slug must be unique
- Must belong to a category
- Can have multiple instructors
- Can have multiple groups

### Group (`App/Models/Group`)

**Purpose:** Specific class within a course.

**Key Attributes:**
- `course_id` - Parent course
- `code` - Group identifier
- `name` - Group name
- `capacity` - Maximum students
- `room` - Physical/virtual room
- `instructor_id` - Primary instructor
- `is_active` - Active status
- `extras` - Additional data (JSON)

**Relationships:**
- `belongsTo` Course
- `belongsTo` User (instructor)
- `belongsToMany` Users (students)
- `hasMany` GroupSessions

**Business Rules:**
- Belongs to exactly one course
- Has one primary instructor
- Capacity cannot be reduced below current enrollments
- Instructor cannot be in two groups of same course simultaneously
- Students count cannot exceed capacity

**Methods:**
- `hasCapacity()` - Check if group has available spots
- `getAvailableSpotsAttribute()` - Get available spots count

### SessionTemplate (`App/Models/SessionTemplate`)

**Purpose:** Reusable session structure.

**Key Attributes:**
- `course_id` - Parent course
- `title` - Session title
- `session_order` - Order in sequence
- `description` - Session description
- `duration_minutes` - Session duration
- `default_start_time` - Default start time
- `default_end_time` - Default end time
- `is_required` - Required session flag
- `materials` - Session materials (JSON)

**Relationships:**
- `belongsTo` Course
- `hasMany` GroupSessions

**Business Rules:**
- Belongs to exactly one course
- Order determines sequence
- Used to generate group sessions

### GroupSession (`Modules/LMS/Sessions/Models/GroupSession`)

**Purpose:** Actual scheduled session.

**Key Attributes:**
- `group_id` - Parent group
- `session_template_id` - Template used (optional)
- `title` - Session title
- `session_order` - Order in sequence
- `session_date` - Actual date
- `start_time` - Start time
- `end_time` - End time
- `meeting_link` - Online meeting link
- `note` - Session notes
- `status` - scheduled, completed, cancelled
- `student_comment` - Student feedback
- `student_file_path` - Student submission file
- `instructor_comment` - Instructor notes
- `supervisor_comment` - Supervisor notes

**Relationships:**
- `belongsTo` Group
- `belongsTo` SessionTemplate (optional)
- `hasMany` Attendance

**Business Rules:**
- Belongs to exactly one group
- Can optionally reference a template
- Date/time must be valid
- Status transitions are controlled

### Enrollment (`Modules/LMS/Enrollments/Models/Enrollment`)

**Purpose:** Student registration in a course.

**Key Attributes:**
- `student_id` - Student (User)
- `course_id` - Course
- `group_id` - Assigned group (nullable until approved)
- `payment_status` - pending, partial, paid, refunded
- `paid_amount` - Amount paid
- `status` - pending, approved, rejected, withdrawn
- `can_attend` - Whether student can attend
- `approved_by` - Admin who approved
- `approved_at` - Approval timestamp
- `note` - Enrollment notes

**Relationships:**
- `belongsTo` User (student)
- `belongsTo` Course
- `belongsTo` Group

**Business Rules:**
- One active enrollment per student per course
- Must be approved before group assignment
- Group assignment requires available capacity
- Payment status tracked separately

### Attendance (`App/Models/Attendance`)

**Purpose:** Attendance record for a group session.

**Key Attributes:**
- `group_session_id` - Group session
- `student_id` - Student
- `status` - present, absent, late, excused
- `note` - Attendance notes
- `marked_by` - User who marked attendance

**Relationships:**
- `belongsTo` GroupSession
- `belongsTo` User (student)
- `belongsTo` User (marked_by)

**Business Rules:**
- One attendance record per student per session
- Status values: present, absent, late, excused
- Can be marked by instructor or admin

### Page (`App/Models/Page`)

**Purpose:** CMS page for public website.

**Key Attributes:**
- `slug` - URL-friendly identifier
- `title` - Page title (JSON - multi-language)
- `content` - Page content (JSON - multi-language)
- `meta_description` - SEO description (JSON)
- `is_active` - Active status
- `sort_order` - Display order

**Relationships:**
- `hasMany` PageBlocks

**Business Rules:**
- Slug must be unique
- Title and content are multi-language (JSON)
- Only active pages are displayed

**Methods:**
- `getTitle($locale)` - Get title for specific language
- `getContent($locale)` - Get content for specific language
- `getMetaDescription($locale)` - Get meta description for language
- `scopeActive()` - Query scope for active pages

### PageBlock (`App/Models/PageBlock`)

**Purpose:** Content block within a page.

**Key Attributes:**
- `page_id` - Parent page
- `type` - Block type (hero, features, cta, etc.)
- `title` - Block title (JSON - multi-language)
- `content` - Block content (JSON - multi-language)
- `config` - Block configuration (JSON)
- `is_enabled` - Enabled status
- `sort_order` - Display order

**Relationships:**
- `belongsTo` Page

**Business Rules:**
- Belongs to exactly one page
- Type determines block rendering
- Config contains block-specific settings
- Only enabled blocks are displayed

**Methods:**
- `getTitle($locale)` - Get title for specific language
- `getContent($locale)` - Get content for specific language
- `scopeEnabled()` - Query scope for enabled blocks

### WebsiteSetting (`App/Models/WebsiteSetting`)

**Purpose:** System-wide website settings (singleton).

**Key Attributes:**
- `academy_id` - Academy identifier (nullable for single-tenant)
- `is_activated` - Website activation status
- `branding` - Branding settings (JSON)
- `default_language` - Default language code
- `default_currency` - Default currency code
- `default_country` - Default country code
- `timezone` - System timezone
- `homepage_id` - Homepage page ID
- `enabled_pages` - Enabled page types (JSON)
- `general_info` - General information (JSON)
- `email_settings` - Email configuration (JSON)
- `payment_settings` - Payment configuration (JSON)
- `contact_settings` - Contact information (JSON)
- `activated_at` - Activation timestamp

**Relationships:**
- None (singleton pattern)

**Business Rules:**
- Only one default settings record
- Website must be activated before public access
- Branding settings stored as JSON
- Settings are retrieved via `getDefault()` static method

**Methods:**
- `getDefault()` - Get or create default settings
- `isActivated()` - Check activation status
- `activate()` - Activate website
- `getPublicSettings()` - Get public-facing settings

### Language (`Modules/Core/Localization/Models/Language`)

**Purpose:** Supported languages.

**Key Attributes:**
- `code` - Language code (e.g., "en", "ar")
- `name` - Language name
- `native_name` - Native language name
- `image_path` - Language flag image
- `is_active` - Active status
- `is_default` - Default language flag
- `is_rtl` - Right-to-left language flag
- `sort_order` - Display order

**Business Rules:**
- Code must be unique
- Only one default language
- RTL flag for Arabic, Hebrew, etc.

### Currency (`App/Models/Currency`)

**Purpose:** Supported currencies.

**Key Attributes:**
- `code` - Currency code (e.g., "USD", "EGP")
- `name` - Currency name
- `symbol` - Currency symbol
- `is_active` - Active status
- `is_default` - Default currency flag
- `sort_order` - Display order

**Business Rules:**
- Code must be unique
- Only one default currency

**Methods:**
- `formatAmount($amount, $showCode)` - Format amount with symbol

### Country (`App/Models/Country`)

**Purpose:** Supported countries.

**Key Attributes:**
- `code` - Country code (e.g., "EG", "US")
- `name` - Country name
- `is_active` - Active status
- `is_default` - Default country flag
- `sort_order` - Display order

**Business Rules:**
- Code must be unique
- Only one default country

### CalendarEvent (`App/Models/CalendarEvent`)

**Purpose:** Calendar events for users.

**Key Attributes:**
- `user_id` - User
- `event_type` - Event type (session, etc.)
- `reference_id` - Reference to related entity
- `title` - Event title
- `description` - Event description
- `start_datetime` - Start date/time
- `end_datetime` - End date/time
- `color` - Event color
- `is_all_day` - All-day event flag

**Relationships:**
- `belongsTo` User

**Business Rules:**
- Belongs to a user
- Can reference other entities (sessions, etc.)

## Relationship Summary

```
User
├── Role (belongsTo)
├── Enrollments (hasMany - as student)
├── InstructorCourses (belongsToMany - as instructor)
├── Attendance (hasMany - as student)
└── CalendarEvents (hasMany)

Course
├── Category (belongsTo)
├── Instructors (belongsToMany - Users)
├── Groups (hasMany)
├── SessionTemplates (hasMany)
├── Enrollments (hasMany)
└── GroupSessions (hasManyThrough Groups)

Group
├── Course (belongsTo)
├── Instructor (belongsTo - User)
├── Students (belongsToMany - Users)
└── GroupSessions (hasMany)

GroupSession
├── Group (belongsTo)
├── SessionTemplate (belongsTo - optional)
└── Attendance (hasMany)

Enrollment
├── Student (belongsTo - User)
├── Course (belongsTo)
└── Group (belongsTo)

Attendance
├── GroupSession (belongsTo)
├── Student (belongsTo - User)
└── MarkedBy (belongsTo - User)

Page
└── PageBlocks (hasMany)

PageBlock
└── Page (belongsTo)
```

## Model Conventions

### Naming
- Models use PascalCase
- Table names use snake_case (plural)
- Foreign keys use `{model}_id` format

### Relationships
- Use Eloquent relationship methods
- Define inverse relationships
- Use eager loading to prevent N+1 queries

### Validation
- Model-level validation in `boot()` method
- Business rules enforced in model events
- Custom exceptions for business rule violations

### Scopes
- Query scopes for common filters
- Active/inactive scopes
- Ordered scopes

### Accessors/Mutators
- Use for computed attributes
- Format data for display
- Transform data on save

## Best Practices

1. **Keep Models Focused**
   - Models represent data and relationships
   - Business logic in services
   - Validation in form requests

2. **Use Relationships**
   - Define all relationships
   - Use eager loading
   - Avoid manual joins when possible

3. **Enforce Business Rules**
   - Use model events
   - Validate in `boot()` method
   - Throw exceptions for violations

4. **Optimize Queries**
   - Use indexes on foreign keys
   - Eager load relationships
   - Use query scopes

5. **Maintain Data Integrity**
   - Foreign key constraints
   - Unique constraints
   - Validation rules

