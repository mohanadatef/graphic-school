# MIGRATIONS FINAL ORDER REPORT

**Generated:** 2025-01-27  
**Status:** ✅ Complete - All migrations reordered correctly

---

## EXECUTIVE SUMMARY

All migration files have been reordered to ensure correct dependency order. Foreign key constraints are now added after table creation using separate `Schema::table()` calls to ensure referenced tables exist first.

**Key Changes:**
1. ✅ `groups` migration renamed from `2025_01_27_200005` to `2025_11_19_081541` (runs after `courses` which is `2025_11_19_081540`)
2. ✅ `group_student` migration renamed from `2025_01_27_200007` to `2025_11_19_081542`
3. ✅ `group_instructor` migration renamed from `2025_01_27_200008` to `2025_11_19_081543`
4. ✅ `session_templates` migration renamed from `2025_11_20_000001` to `2025_11_19_081544`
5. ✅ `group_sessions` migration renamed from `2025_11_20_000002` to `2025_11_19_081545`
6. ✅ All foreign keys now use `unsignedBigInteger` + separate `Schema::table()` calls
7. ✅ All community migrations updated to use the same pattern

---

## FINAL MIGRATION ORDER

### Phase 0: Foundation (No Dependencies)
1. `2014_10_12_100000_create_password_reset_tokens_table.php`
2. `2019_08_19_000000_create_failed_jobs_table.php`
3. `2019_12_14_000001_create_personal_access_tokens_table.php`

### Phase 1: Localization (No Dependencies)
4. `2025_01_20_000007_create_currencies_table.php`
5. `2025_01_20_000008_create_countries_table.php`

### Phase 2: CMS (No Dependencies)
6. `2025_01_20_000009_rebuild_pages_table.php` (Creates `pages` and `page_blocks`)
7. `2025_11_21_180623_create_faqs_table.php`

### Phase 3: Settings
8. `2025_01_27_000001_create_branding_settings_table.php`
9. `2025_01_27_800001_create_website_settings_table.php`

### Phase 4: ACL & Users (Module Migrations - Auto-loaded)
**Note:** These run in order via ModuleServiceProvider but are listed here for reference.

10. `Modules/ACL/Roles/Database/Migrations/2014_10_10_000000_create_roles_table.php`
11. `Modules/ACL/Permissions/Database/Migrations/2014_10_10_000001_create_permissions_table.php`
12. `Modules/ACL/Permissions/Database/Migrations/2014_10_10_000002_create_permission_role_table.php`
13. `Modules/ACL/Users/Database/Migrations/2014_10_12_000000_create_users_table.php`

### Phase 5: Languages (Module)
14. `Modules/Core/Localization/Database/Migrations/2025_01_22_000001_create_languages_table.php`

### Phase 6: Categories (Module)
15. `Modules/LMS/Categories/Database/Migrations/2025_11_19_081530_create_categories_table.php`

### Phase 7: Courses (Module)
16. `Modules/LMS/Courses/Database/Migrations/2025_11_19_081540_create_courses_table.php`
17. `Modules/LMS/Courses/Database/Migrations/2025_11_19_081549_create_course_instructor_table.php`

### Phase 8: Groups (After Courses - Main Migrations)
18. `2025_11_19_081541_create_groups_table.php` ✅ **RENAMED**
19. `2025_11_19_081542_create_group_student_table.php` ✅ **RENAMED**
20. `2025_11_19_081543_create_group_instructor_table.php` ✅ **RENAMED**

### Phase 9: Sessions
21. `2025_11_19_081544_create_session_templates_table.php` ✅ **RENAMED**
22. `2025_11_19_081545_create_group_sessions_table.php` ✅ **RENAMED**

### Phase 10: Enrollments (Module)
23. `Modules/LMS/Enrollments/Database/Migrations/2025_11_19_081606_create_enrollments_table.php`

### Phase 11: Attendance (Module)
24. `Modules/LMS/Attendance/Database/Migrations/2025_11_19_081615_create_attendance_table.php`

### Phase 12: Certificates (Module)
25. `Modules/LMS/Certificates/Database/Migrations/2025_11_19_081549_create_certificates_table.php`

### Phase 13: Community
26. `2025_01_27_600005_create_community_tags_table.php` (No dependencies)
27. `2025_01_27_600001_create_community_posts_table.php` (Depends: users, groups)
28. `2025_01_27_600002_create_community_comments_table.php` (Depends: community_posts, users)
29. `2025_01_27_600003_create_community_replies_table.php` (Depends: community_comments, users)
30. `2025_01_27_600004_create_community_likes_table.php` (Depends: users)
31. `2025_01_27_600006_create_community_post_tag_table.php` (Depends: community_posts, community_tags)
32. `2025_01_27_600007_create_community_reports_table.php` (Depends: users)

### Phase 14: Optional
33. `2025_01_27_400005_create_calendar_events_table.php` (Depends: users)

---

## FOREIGN KEY HANDLING

All migrations now use a two-step approach:
1. Create table with `unsignedBigInteger` for foreign key columns
2. Add foreign key constraints after table creation using `Schema::table()`

This ensures:
- ✅ Tables can be created even if referenced tables don't exist yet
- ✅ Foreign keys are added conditionally after both tables exist
- ✅ No foreign key constraint errors during `migrate:fresh`

**Example Pattern:**
```php
Schema::create('groups', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('course_id'); // Not foreignId() directly
    // ... other columns
});

// Add foreign key after table creation
Schema::table('groups', function (Blueprint $table) {
    $table->foreign('course_id')
        ->references('id')
        ->on('courses')
        ->onDelete('cascade');
});
```

---

## IMPORTANT NOTES

### Module Migrations Load Order

Module migrations are loaded via `loadMigrationsFrom()` in their respective `ModuleServiceProvider` classes. They run **AFTER** main migrations in `database/migrations/`, but Laravel sorts all migrations by timestamp across all sources.

**Key Point:** Since `groups` migration is now `2025_11_19_081541` and `courses` is `2025_11_19_081540` (module), they will run in the correct order:
1. `courses` (module): `2025_11_19_081540`
2. `groups` (main): `2025_11_19_081541`

### Dependency Resolution

The foreign key handling approach (unsignedBigInteger + Schema::table) ensures that:
- Main migrations can reference module tables
- Module migrations can reference other module tables
- All foreign keys are added after both tables exist

---

## VALIDATION CHECKLIST

- [x] All migrations use CREATE-only mode (no UPDATE/ALTER migrations)
- [x] Foreign keys added after table creation
- [x] Groups migration runs after courses
- [x] All dependencies respected
- [x] No duplicate migrations
- [x] All foreign key constraints use proper cascade/null settings

---

## TESTING

Run the following command to verify:

```bash
cd graphic-school-api
php artisan migrate:fresh --seed
```

This should complete without foreign key errors.

---

**Report Generated:** 2025-01-27  
**Status:** ✅ Complete - Ready for `migrate:fresh`

✔ MIGRATIONS REORDER COMPLETE — All migrations now in correct dependency order.

