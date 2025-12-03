# Database Migrations

## Overview

Migrations define the database schema and are version-controlled. All migrations are in `database/migrations/`.

## Migration Naming Convention

Migrations follow Laravel's naming convention:
```
YYYY_MM_DD_HHMMSS_description.php
```

Example: `2025_01_20_120000_create_groups_table.php`

## Core Tables

### Authentication & Users

#### users
- Stores all users (admin, instructor, student)
- Foreign key to `roles`
- Indexes on `email` and `role_id`

#### roles
- User roles
- Unique constraint on `slug`

#### permissions
- Fine-grained permissions
- Unique constraint on `slug`

#### permission_role
- Pivot table for role-permission relationships

### LMS Core

#### categories
- Course categories
- Unique constraint on `slug`
- Index on `slug`

#### courses
- Main course table
- Foreign key to `categories`
- Unique constraints on `slug` and `code`
- Indexes on `slug`, `code`, `category_id`

#### course_instructor
- Pivot table for course-instructor relationships
- Includes `is_supervisor` flag

#### groups
- Course groups
- Foreign key to `courses` and `users` (instructor)
- Indexes on `course_id` and `instructor_id`

#### group_student
- Pivot table for group-student relationships
- Includes `enrolled_at` timestamp

#### session_templates
- Reusable session structures
- Foreign key to `courses`
- Indexes on `course_id` and `(course_id, session_order)`

#### group_sessions
- Actual scheduled sessions
- Foreign key to `groups` and `session_templates`
- Indexes on `group_id`, `session_date`, `session_template_id`

#### enrollments
- Student enrollments
- Foreign key to `users` (student), `courses`, `groups`, `users` (approved_by)
- Indexes on `student_id`, `course_id`, `group_id`, `status`

#### attendance
- Attendance records
- Foreign key to `group_sessions`, `users` (student), `users` (marked_by)
- Unique constraint on `(group_session_id, student_id)`
- Indexes on `group_session_id`, `student_id`

### CMS

#### pages
- CMS pages
- Unique constraint on `slug`
- Indexes on `slug`, `is_active`

#### page_blocks
- Page content blocks
- Foreign key to `pages`
- Indexes on `page_id`, `(is_enabled, sort_order)`

### Settings

#### website_settings
- System-wide settings (singleton)
- Index on `is_activated`

#### languages
- Supported languages
- Unique constraint on `code`
- Indexes on `code`, `is_active`, `is_default`

#### currencies
- Supported currencies
- Unique constraint on `code`
- Indexes on `code`, `is_active`, `is_default`

#### countries
- Supported countries
- Unique constraint on `code`
- Indexes on `code`, `is_active`, `is_default`

### Calendar

#### calendar_events
- User calendar events
- Foreign key to `users`
- Indexes on `user_id`, `start_datetime`

### Optional Tables

#### notifications
- In-app notifications
- Foreign key to `users`
- Indexes on `user_id`, `is_read`, `created_at`

#### subscription_plans
- Subscription plans (optional)
- Unique constraint on `slug`
- Index on `slug`

#### subscription_usage_trackers
- Usage tracking (optional)
- Foreign key to `users` and `subscription_plans`
- Indexes on `user_id`, `(period_start, period_end)`

## Migration Structure

### Creating Tables

```php
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('parent_id')->constrained('parents');
    $table->timestamps();
    
    $table->index('name');
});
```

### Adding Columns

```php
Schema::table('table_name', function (Blueprint $table) {
    $table->string('new_column')->nullable();
    $table->index('new_column');
});
```

### Foreign Keys

All foreign keys use:
- `constrained()` for referential integrity
- `onDelete('cascade')` or `onDelete('set null')` as appropriate
- Indexes on foreign key columns

### Indexes

Indexes are created on:
- Foreign key columns
- Frequently queried columns
- Unique constraint columns
- Composite indexes for common query patterns

## Migration Best Practices

1. **Always Use Migrations**
   - Never modify database directly
   - Version control all schema changes

2. **Rollback Support**
   - Always implement `down()` method
   - Test rollbacks

3. **Data Migrations**
   - Use separate migrations for data changes
   - Use seeders for default data

4. **Foreign Keys**
   - Always define foreign keys
   - Use appropriate cascade rules

5. **Indexes**
   - Add indexes for performance
   - Don't over-index (write performance)

6. **Nullable Fields**
   - Use nullable for optional fields
   - Set defaults where appropriate

## Running Migrations

### Fresh Migration
```bash
php artisan migrate:fresh
```

### Run Migrations
```bash
php artisan migrate
```

### Rollback
```bash
php artisan migrate:rollback
```

### Reset
```bash
php artisan migrate:reset
```

## Migration Order

Migrations run in chronological order. Dependencies are handled by:
1. Creating parent tables first
2. Creating child tables with foreign keys
3. Adding indexes after table creation

## Schema Changes

When modifying existing tables:
1. Create new migration
2. Use `Schema::table()` to modify
3. Add columns/indexes as needed
4. Test rollback

## Data Integrity

### Constraints
- Foreign key constraints enforce referential integrity
- Unique constraints prevent duplicates
- Check constraints (via model validation)

### Cascading
- `cascadeOnDelete()` - Delete related records
- `nullOnDelete()` - Set foreign key to null
- `restrictOnDelete()` - Prevent deletion if related records exist

## Performance Considerations

### Indexes
- Index foreign keys
- Index frequently queried columns
- Composite indexes for multi-column queries

### Query Optimization
- Use eager loading to prevent N+1 queries
- Add indexes based on query patterns
- Monitor slow queries

## Migration Checklist

When creating a migration:
- [ ] Define table structure
- [ ] Add foreign keys
- [ ] Add indexes
- [ ] Set nullable/defaults
- [ ] Implement `down()` method
- [ ] Test migration
- [ ] Test rollback
- [ ] Update model if needed

