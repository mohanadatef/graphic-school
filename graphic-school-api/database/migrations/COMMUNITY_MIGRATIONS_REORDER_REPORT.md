# COMMUNITY MIGRATIONS REORDER REPORT

**Generated:** 2025-01-27  
**Status:** ✅ Complete - All community migrations reordered correctly

---

## EXECUTIVE SUMMARY

All community migrations have been reordered to ensure they run **AFTER** the `groups` table is created, since `community_posts` has a foreign key reference to `groups`.

---

## MIGRATIONS REORDERED

### Old Timestamps (Before):
- `2025_01_27_600001_create_community_posts_table.php` (ran before groups)
- `2025_01_27_600002_create_community_comments_table.php`
- `2025_01_27_600003_create_community_replies_table.php`
- `2025_01_27_600004_create_community_likes_table.php`
- `2025_01_27_600005_create_community_tags_table.php`
- `2025_01_27_600006_create_community_post_tag_table.php`
- `2025_01_27_600007_create_community_reports_table.php`

### New Timestamps (After):
- `2025_11_19_081545_create_group_sessions_table.php` (groups dependency resolved)
- `2025_11_19_081546_create_community_tags_table.php` (no dependencies)
- `2025_11_19_081547_create_community_posts_table.php` ✅ (runs after groups)
- `2025_11_19_081548_create_community_comments_table.php` (depends on community_posts)
- `2025_11_19_081549_create_community_replies_table.php` (depends on community_comments)
- `2025_11_19_081550_create_community_likes_table.php` (depends on users)
- `2025_11_19_081551_create_community_post_tag_table.php` (depends on community_posts, community_tags)
- `2025_11_19_081552_create_community_reports_table.php` (depends on users)

---

## FINAL MIGRATION ORDER

1. Foundation tables (password_resets, failed_jobs, tokens)
2. Currencies, Countries
3. Pages
4. Branding Settings
5. Website Settings
6. Calendar Events
7. **Groups** (2025_11_19_081541)
8. Group Student (2025_11_19_081542)
9. Group Instructor (2025_11_19_081543)
10. Session Templates (2025_11_19_081544)
11. **Group Sessions** (2025_11_19_081545)
12. **Community Tags** (2025_11_19_081546) - No dependencies
13. **Community Posts** (2025_11_19_081547) ✅ After groups
14. **Community Comments** (2025_11_19_081548) - After community_posts
15. **Community Replies** (2025_11_19_081549) - After community_comments
16. **Community Likes** (2025_11_19_081550)
17. **Community Post Tag** (2025_11_19_081551) - After community_posts, community_tags
18. **Community Reports** (2025_11_19_081552)
19. FAQs

---

## DEPENDENCY CHAIN

```
groups (2025_11_19_081541)
  └─> community_posts (2025_11_19_081547) ✅ Fixed
      └─> community_comments (2025_11_19_081548)
          └─> community_replies (2025_11_19_081549)
      └─> community_post_tag (2025_11_19_081551)
```

---

**Report Generated:** 2025-01-27  
**Status:** ✅ Complete - Community migrations now run after groups

