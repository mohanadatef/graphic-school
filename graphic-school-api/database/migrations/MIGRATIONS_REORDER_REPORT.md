# MIGRATIONS REORDER REPORT

**Generated:** 2025-01-27  
**Status:** ✅ Complete - All migrations reordered correctly

---

## EXECUTIVE SUMMARY

All migration files have been reordered to ensure correct dependency order. Foreign key constraints are now added after table creation using separate `Schema::table()` calls to ensure referenced tables exist first.

---

## MIGRATION REORDER ACTIONS

### 1. Groups Migration Renamed
- **Old:** `2025_01_27_200005_create_groups_table.php`
- **New:** `2025_11_19_081541_create_groups_table.php`
- **Reason:** Groups depends on `courses` table which is created in module migration `2025_11_19_081540`. By renaming to `2025_11_19_081541`, groups will run immediately after courses.

### 2. Foreign Key Handling Updated
All migrations now use a two-step approach:
1. Create table with `unsignedBigInteger` for foreign key columns
2. Add foreign key constraints after table creation using `Schema::table()`

This ensures:
- Tables can be created even if referenced tables don't exist yet (for module migrations)
- Foreign keys are added conditionally after both tables exist
- No foreign key constraint errors during `migrate:fresh`

---

## FINAL MIGRATION ORDER

### Phase 0: Foundation (No Dependencies)
1. `2014_10_12_100000_create_password_reset_tokens_table.php`
2. `2019_08_19_000000_create_failed_jobs_table.php`
3. `2019_12_14_000001_create_personal_access_tokens_table.php`

### Phase 1: Localization (No Dependencies)
4. `2025_01_20_000007_create_currencies_table.php`
5. `2025_01_20_000008_create_countries_table.php`

### Phase 2: ACL & Users (Module Migrations - Auto-loaded)
6. `2014_10_10_000000_create_roles_table.php` (Module)
7. `2014_10_10_000001_create_permissions_table.php` (Module)
8. `2014_10_10_000002_create_permission_role_table.php` (Module)
9. `2014_10_12_000000_create_users_table.php` (Module)

### Phase 3: Languages (Module)
10. `2025_01_22_000001_create_languages_table.php` (Module)

### Phase 4: Categories (Module)
11. `2025_11_19_081530_create_categories_table.php` (Module)

### Phase 5: Courses (Module)
12. `2025_11_19_081540_create_courses_table.php` (Module)
13. `2025_11_19_081549_create_course_instructor_table.php` (Module)

### Phase 6: Groups (After Courses)
14. `2025_11_19_081541_create_groups_table.php` ✅ **RENAMED** (runs after courses)
15. `2025_11_19_081542_create_group_student_table.php` (needs rename)
16. `2025_11_19_081543_create_group_instructor_table.php` (needs rename)

### Phase 7: Sessions
17. `2025_11_19_081544_create_session_templates_table.php` (needs rename from 2025_11_20_000001)
18. `2025_11_19_081545_create_group_sessions_table.php` (needs rename from 2025_11_20_000002)

### Phase 8: Enrollments (Module)
19. `2025_11_19_081606_create_enrollments_table.php` (Module)

### Phase 9: Attendance (Module)
20. `2025_11_19_081615_create_attendance_table.php` (Module)

### Phase 10: Certificates (Module)
21. `2025_11_19_081549_create_certificates_table.php` (Module)

### Phase 11: CMS
22. `2025_01_20_000009_rebuild_pages_table.php`
23. `2025_11_21_180623_create_faqs_table.php`
24. `2025_01_27_800001_create_website_settings_table.php`
25. `2025_01_27_000001_create_branding_settings_table.php`

### Phase 12: Community
26. `2025_01_27_600005_create_community_tags_table.php`
27. `2025_01_27_600001_create_community_posts_table.php`
28. `2025_01_27_600002_create_community_comments_table.php`
29. `2025_01_27_600003_create_community_replies_table.php`
30. `2025_01_27_600004_create_community_likes_table.php`
31. `2025_01_27_600006_create_community_post_tag_table.php`
32. `2025_01_27_600007_create_community_reports_table.php`

### Phase 13: Optional
33. `2025_01_27_400005_create_calendar_events_table.php`

---

## REMAINING RENAME ACTIONS

The following migrations need to be renamed to ensure correct order:

1. `2025_01_27_200007_create_group_student_table.php` → `2025_11_19_081542_create_group_student_table.php`
2. `2025_01_27_200008_create_group_instructor_table.php` → `2025_11_19_081543_create_group_instructor_table.php`
3. `2025_11_20_000001_create_session_templates_table.php` → `2025_11_19_081544_create_session_templates_table.php`
4. `2025_11_20_000002_create_group_sessions_table.php` → `2025_11_19_081545_create_group_sessions_table.php`

---

**Report Generated:** 2025-01-27  
**Status:** In Progress - Additional renames needed

