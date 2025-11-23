# 📋 MASTER REQUIREMENTS - Graphic School 2.0

**Version**: 1.0  
**Date**: 2025-01-27  
**Status**: Master Requirements Document

---

## 🎯 PRODUCT VISION

### REQ-001: Product Overview
**Graphic School 2.0** is a **SaaS Learning Management System (LMS)** specifically designed for:
- Graphic design and creative training academies
- Target markets: **Egypt** and **GCC countries** (Saudi Arabia, UAE, Kuwait, Qatar, Bahrain, Oman)
- Multi-tenant architecture: Each academy operates as an isolated tenant with:
  - Custom domain/subdomain support
  - Custom branding (logo, colors, fonts)
  - Isolated data (students, courses, content)
  - Independent configuration

### REQ-002: User Roles & Hierarchy
The system MUST support the following roles:

1. **HQ Super Admin** (REQ-002-1)
   - Manages all academies
   - Creates/manages subscription plans
   - Views system-wide analytics
   - Manages HQ-level settings

2. **Academy Admin** (REQ-002-2)
   - Full control over their academy
   - Manages instructors, students, programs
   - Configures branding, language, currency
   - Views academy-specific reports
   - Manages subscriptions and billing

3. **Instructor** (REQ-002-3)
   - Assigned to groups/courses
   - Marks attendance (manual + QR)
   - Creates/grades assignments
   - Views assigned groups and sessions
   - Posts in community
   - Views calendar

4. **Student** (REQ-002-4)
   - Enrolls in programs/batches
   - Views sessions and curriculum
   - Submits assignments
   - Views grades and certificates
   - Participates in community
   - Views gamification (XP, badges, leaderboard)
   - Scans QR for attendance

5. **Optional Roles** (REQ-002-5)
   - Coordinator
   - Support
   - Sales
   *(If already implemented, must be fully functional)*

---

## 🏗️ CORE DOMAINS

### DOMAIN 1: Authentication & Authorization

#### REQ-003: Authentication System
- User registration (with role assignment)
- Login/Logout
- Password reset
- Email verification (if implemented)
- Token-based authentication (Sanctum)
- Session management

#### REQ-004: Role-Based Access Control (RBAC)
- Role definitions (Super Admin, Admin, Instructor, Student, etc.)
- Permission system (granular permissions per role)
- Middleware for route protection
- UI-level access control (show/hide based on role)

---

### DOMAIN 2: Programs → Batches → Groups → Sessions

#### REQ-005: Programs
- Create/Edit/Delete programs
- Program details (name, description, duration, price)
- Program translations (AR/EN)
- Program status (draft, published, archived)
- Public program listing
- Program enrollment

#### REQ-006: Batches
- Create batches within programs
- Batch scheduling (start date, end date, days of week, times)
- Batch capacity (max students)
- Batch status tracking
- Batch assignments to instructors

#### REQ-007: Groups
- Create groups within batches
- Group assignment to instructors
- Group capacity management
- Group-specific sessions
- Group translations (AR/EN)

#### REQ-008: Sessions
- Create sessions within groups/courses
- Session scheduling (date, start time, end time)
- Session attendance tracking
- Session materials/notes
- Session status (scheduled, completed, cancelled)
- Online meeting links (if applicable)
- Session translations (AR/EN)

---

### DOMAIN 3: Courses & Curriculum

#### REQ-009: Courses
- Create/Edit/Delete courses
- Course details (title, description, image, price)
- Course categories
- Course instructors assignment
- Course status (draft, published, hidden)
- Course translations (AR/EN)
- Public course listing

#### REQ-010: Curriculum (Modules + Lessons)
- Course modules (organized structure)
- Lessons within modules
- Lesson content (text, video, files)
- Lesson order/sequence
- Lesson completion tracking
- Module/Lesson translations (AR/EN)

---

### DOMAIN 4: Enrollments

#### REQ-011: Enrollment System
- Student enrollment in programs/batches
- Enrollment approval workflow (if applicable)
- Enrollment status (pending, approved, rejected, completed)
- Enrollment history/logs
- Enrollment cancellation/refund handling

---

### DOMAIN 5: Attendance

#### REQ-012: Attendance Tracking
- **Manual Attendance** (REQ-012-1)
  - Instructor marks attendance per session
  - Attendance status (present, absent, late, excused)
  - Attendance history per student
  - Attendance reports

- **QR Code Attendance** (REQ-012-2)
  - Generate QR code per session
  - Student scans QR to mark attendance
  - QR token validation
  - QR expiration handling

---

### DOMAIN 6: Assignments & Submissions

#### REQ-013: Assignments
- Create assignments (by instructor/admin)
- Assignment details (title, description, due date, points)
- Assignment attachments/files
- Assignment visibility (per group/course)

#### REQ-014: Submissions
- Student submission of assignments
- File upload support
- Submission status (submitted, graded, late)
- Submission history

#### REQ-015: Gradebook
- Grade assignment submissions
- Grade entry per student
- Grade calculations (weighted averages if applicable)
- Gradebook view (instructor, student, admin)
- Gradebook exports

---

### DOMAIN 7: Quizzes & Assessments

#### REQ-016: Quizzes (Optional/Future)
- Create quizzes (if implemented)
- Quiz questions (multiple choice, true/false, etc.)
- Quiz attempts and results
- Quiz grading

**Status**: Mark as "Future / Not Required Now" if not fully implemented.

---

### DOMAIN 8: Certificates

#### REQ-017: Certificate System
- Certificate templates (design, layout)
- Certificate issuing (manual or automatic)
- Certificate verification (public endpoint)
- Certificate PDF generation
- Certificate data (student name, course, date, etc.)

---

### DOMAIN 9: Calendar & Schedule

#### REQ-018: Calendar System
- Calendar view (monthly, weekly, daily)
- Session scheduling display
- Assignment due dates
- Event management
- Calendar filters (by course, group, instructor)
- Calendar exports (iCal if applicable)

---

### DOMAIN 10: Payments & Invoices

#### REQ-019: Payment System
- Payment methods (cash, bank transfer, online payment gateway if implemented)
- Payment recording
- Payment status tracking
- Payment history

#### REQ-020: Invoices
- Invoice generation
- Invoice details (items, amounts, taxes if applicable)
- Invoice status (draft, sent, paid, overdue)
- Invoice PDF generation
- Invoice email sending (if implemented)
- Invoice currency formatting

---

### DOMAIN 11: Subscriptions & Plans (SaaS Monetization)

#### REQ-021: Subscription Plans
- Create subscription plans (by HQ Super Admin)
- Plan features (max students, max courses, storage, etc.)
- Plan pricing (monthly, yearly)
- Plan status (active, archived)

#### REQ-022: Academy Subscriptions
- Academy subscription to plans
- Subscription status (active, expired, cancelled)
- Subscription renewal
- Usage tracking (students, courses, storage)
- Usage limits enforcement

#### REQ-023: Subscription Invoices
- Automatic invoice generation
- Invoice payment tracking
- Subscription renewal reminders

---

### DOMAIN 12: Notifications

#### REQ-024: Notification System
- **In-App Notifications** (REQ-024-1)
  - Notification center
  - Notification types (enrollment, assignment, attendance, etc.)
  - Notification read/unread status
  - Notification preferences

- **Email Notifications** (REQ-024-2)
  - Email sending (if implemented)
  - Email templates
  - Email preferences per user

- **SMS Notifications** (REQ-024-3)
  - SMS sending (if implemented)
  - SMS preferences

---

### DOMAIN 13: Community

#### REQ-025: Community Features
- **Posts** (REQ-025-1)
  - Create posts (text, images, files)
  - Post visibility (public, group-specific)
  - Post editing/deletion
  - Post moderation (admin can pin, lock, delete)

- **Comments & Replies** (REQ-025-2)
  - Comment on posts
  - Reply to comments
  - Comment editing/deletion

- **Likes** (REQ-025-3)
  - Like posts and comments
  - Like count display

- **Reports** (REQ-025-4)
  - Report inappropriate content
  - Report moderation (admin review)

- **Tags** (REQ-025-5)
  - Tag posts (if implemented)
  - Tag filtering

---

### DOMAIN 14: Gamification

#### REQ-026: Gamification System
- **XP (Experience Points)** (REQ-026-1)
  - Award XP for events (attendance, assignment completion, etc.)
  - XP tracking per student
  - XP history

- **Levels** (REQ-026-2)
  - Level system (based on XP thresholds)
  - Level progression
  - Level display

- **Badges** (REQ-026-3)
  - Badge definitions
  - Badge awarding (automatic or manual)
  - Badge display per user

- **Leaderboards** (REQ-026-4)
  - Global leaderboard
  - Group/course leaderboard
  - Leaderboard rankings

---

### DOMAIN 15: Page Builder

#### REQ-027: Page Builder System
- **Page Creation** (REQ-027-1)
  - Create custom pages
  - Page slug/URL
  - Page status (draft, published)

- **Block System** (REQ-027-2)
  - Add blocks to pages (Hero, Features, CTA, FAQ, Gallery, etc.)
  - Block configuration (content, styling)
  - Block ordering (drag & drop if implemented)
  - Block templates

- **Page Publishing** (REQ-027-3)
  - Publish pages to public website
  - Public page rendering
  - Page preview (before publishing)

- **Page Templates** (REQ-027-4)
  - Pre-built page templates
  - Template customization

---

### DOMAIN 16: Reports & Analytics

#### REQ-028: Reporting System
- **Basic Reports** (REQ-028-1)
  - Student enrollment reports
  - Attendance reports
  - Assignment completion reports
  - Revenue reports (if applicable)

- **Analytics** (REQ-028-2)
  - Dashboard statistics
  - Charts/graphs
  - Data exports (Excel, PDF)

---

### DOMAIN 17: Audit Logs

#### REQ-029: Audit Trail
- Activity logging (if implemented)
- User action tracking
- Audit log viewing (admin only)
- Audit log exports

---

## 🌐 CROSS-CUTTING REQUIREMENTS

### REQ-030: Multi-Language Support (AR + EN)
- **Language Settings** (REQ-030-1)
  - Default language configuration (AR or EN)
  - Available languages list
  - Language switcher in UI

- **Translation System** (REQ-030-2)
  - All user-facing strings translatable
  - Translation keys management
  - RTL (Right-to-Left) support for Arabic
  - LTR (Left-to-Right) for English
  - Language-specific content (programs, courses, etc.)

- **UI Language** (REQ-030-3)
  - Admin dashboard: AR + EN
  - Student dashboard: AR + EN
  - Public website: AR + EN
  - Instructor dashboard: AR + EN

---

### REQ-031: Multi-Currency Support
- **Currency Configuration** (REQ-031-1)
  - Default currency setting (EGP, SAR, AED, etc.)
  - Currency symbol formatting
  - Currency per academy (or global default)

- **Currency Usage** (REQ-031-2)
  - Currency in subscription plans
  - Currency in invoices
  - Currency in payment pages
  - Currency in course/program pricing
  - Consistent currency formatting everywhere

---

### REQ-032: Branding System
- **Logo** (REQ-032-1)
  - Default logo upload
  - Dark mode logo (optional)
  - Favicon upload
  - Logo display in dashboard and public site

- **Colors** (REQ-032-2)
  - Primary color
  - Secondary color
  - Background colors
  - Text colors
  - Color application in UI (buttons, links, etc.)

- **Fonts** (REQ-032-3)
  - Main font (body text)
  - Heading font
  - Custom font upload (if supported)
  - Font application in UI

- **Theme Default** (REQ-032-4)
  - Default theme (light/dark) per academy
  - Theme persistence

---

### REQ-033: Dark/Light Mode
- **Theme Switcher** (REQ-033-1)
  - Global theme toggle (light/dark)
  - Theme persistence (localStorage)
  - System preference detection

- **Theme Consistency** (REQ-033-2)
  - All pages support dark/light mode
  - No hardcoded colors (use theme-aware classes)
  - Text readability in both themes
  - Buttons, cards, modals, sidebar, topbar all theme-aware
  - Page Builder canvas theme-aware
  - Community pages theme-aware
  - Student/Instructor dashboards theme-aware

---

### REQ-034: Responsive Design
- **Device Support** (REQ-034-1)
  - Desktop (1280px+)
  - Tablet (768px - 1279px)
  - Mobile (< 768px)

- **Responsive UI** (REQ-034-2)
  - All pages responsive
  - Navigation adapts to screen size
  - Forms adapt to screen size
  - Tables adapt to screen size (scroll or cards)

---

### REQ-035: Settings & Configuration
- **Admin Settings Page** (REQ-035-1)
  - Clear "Settings / Configuration" area in Admin dashboard
  - Language settings (default language, available languages)
  - Currency settings (default currency, symbol formatting)
  - Branding settings (logo, colors, fonts, dark/light default)
  - Contact & social links
  - Basic academy profile

- **Settings Application** (REQ-035-2)
  - Settings reflected in dashboard
  - Settings reflected in public website
  - Settings reflected in Page Builder
  - Settings reflected in invoices/pricing displays

---

### REQ-036: Clean State for First Client
- **Production Preparation** (REQ-036-1)
  - Command to clean demo data: `php artisan app:prepare-production`
  - Removes demo programs, students, community posts, assignments
  - Keeps: Admin users, roles/permissions, settings, branding, Page Builder templates (not actual pages)

- **Environment-Based Seeding** (REQ-036-2)
  - `APP_ENV=local`: Can seed demo data
  - `APP_ENV=production`: Only production-safe seeding

---

### REQ-037: E2E Testing
- **Cypress Tests** (REQ-037-1)
  - Admin E2E tests
  - Instructor E2E tests
  - Student E2E tests
  - Full multi-user flow tests
  - Screenshots and video recording

- **Test Coverage** (REQ-037-2)
  - Key user flows covered
  - Settings configuration tests
  - Dark/Light mode tests (optional)
  - Clean state scenario tests

---

## 📊 REQUIREMENT SUMMARY

| Domain | Requirements | Status |
|--------|-------------|--------|
| Auth & Authorization | REQ-003, REQ-004 | To be audited |
| Programs → Batches → Groups → Sessions | REQ-005 to REQ-008 | To be audited |
| Courses & Curriculum | REQ-009, REQ-010 | To be audited |
| Enrollments | REQ-011 | To be audited |
| Attendance | REQ-012 | To be audited |
| Assignments & Submissions | REQ-013 to REQ-015 | To be audited |
| Quizzes | REQ-016 | To be audited |
| Certificates | REQ-017 | To be audited |
| Calendar | REQ-018 | To be audited |
| Payments & Invoices | REQ-019, REQ-020 | To be audited |
| Subscriptions & Plans | REQ-021 to REQ-023 | To be audited |
| Notifications | REQ-024 | To be audited |
| Community | REQ-025 | To be audited |
| Gamification | REQ-026 | To be audited |
| Page Builder | REQ-027 | To be audited |
| Reports & Analytics | REQ-028 | To be audited |
| Audit Logs | REQ-029 | To be audited |
| Multi-Language | REQ-030 | To be audited |
| Multi-Currency | REQ-031 | To be audited |
| Branding | REQ-032 | To be audited |
| Dark/Light Mode | REQ-033 | To be audited |
| Responsive Design | REQ-034 | To be audited |
| Settings | REQ-035 | To be audited |
| Clean State | REQ-036 | To be audited |
| E2E Testing | REQ-037 | To be audited |

**Total Requirements**: 37 main requirements (with sub-requirements)

---

## 📝 NOTES

- All requirements marked as "To be audited" will be checked in `MASTER_FEATURE_AUDIT.md`
- Status will be: ✅ FULLY_IMPLEMENTED_AND_WORKING, ⚠️ PARTIALLY_IMPLEMENTED_OR_NOT_FULLY_WIRED, ❌ MISSING_OR_NOT_IMPLEMENTED
- Missing or partial features will be implemented/completed in Part 3

---

**End of MASTER_REQUIREMENTS.md**

