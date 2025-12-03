# Business Logic

## Overview

This document describes the core business logic and workflows of Graphic School 2.0 LMC system. All business rules are implemented in services and models, following Domain-Driven Design principles.

**Core Flow:** Course → Group → Session → Enrollment → Attendance → Certificate

## Core Business Rules

### 1. Course Management

#### Course Creation
- Course **must** have a price (required field)
- Course can have **multiple instructors** (via pivot table)
- Course has one primary category
- Course can have optional tags
- Course is the **TOP learning entity** (NO programs, tracks, or batches)

#### Course Pricing
- Price is **required** (cannot be null/zero)
- Price uses selected currency (from currencies table)
- Default currency: EGP
- Price displayed on public course pages

#### Course Instructors
- Multiple instructors per course
- One instructor can be marked as supervisor
- Instructors can be assigned via course management interface

---

### 2. Group Management

#### Group Creation
- Group **must** belong to a Course
- Group has **start_date** and **end_date**
- Group has **notes** field (optional)
- Group has **capacity** (maximum students)
- Group can have **multiple instructors** (from course instructors)
- Group name must be unique within the course

#### Group Capacity
- Capacity cannot be reduced below current enrollments
- Available spots = capacity - current enrollments
- Enrollment checks capacity before assignment

#### Group Dates
- `start_date` - When group starts
- `end_date` - When group ends
- Used for scheduling and filtering

---

### 3. Session Management

#### Session Creation
- Session **must** belong to a Group
- Session has: **title**, **date**, **start_time**, **end_time**, **notes**
- Session can have **optional instructor_id**
- Session can optionally link to SessionTemplate

#### Session Fields
- `title` - Session title
- `session_date` - Date of session
- `start_time` - Start time (HH:MM format)
- `end_time` - End time (HH:MM format)
- `instructor_id` - Optional instructor assignment
- `notes` - Optional session notes

#### Session Status
- `scheduled` - Future session
- `completed` - Past session
- `cancelled` - Cancelled session

---

### 4. Enrollment Workflow

#### Enrollment Flow

```
1. Student Submits Enrollment
   ↓
   Status: pending
   Payment Status: not_paid
   Group: null
   
2. Admin Reviews Enrollment
   ↓
   Admin can:
   - Approve → Assign to Group
   - Reject → Status: rejected
   
3. Enrollment Approved
   ↓
   Status: approved
   Group assigned (if provided)
   can_attend: true
   Student added to group_student pivot
   Attendance slots created for all group sessions
   
4. Student Can Now Access
   ↓
   - View courses
   - View assigned group
   - View sessions
   - View attendance history
   - Receive certificates
```

#### Enrollment Rules

1. **One Active Enrollment Per Course:**
   - Student cannot have multiple active enrollments in the same course
   - Previous enrollment must be rejected/withdrawn before new one

2. **Group Assignment:**
   - Happens on approval (not on submission)
   - Admin must specify group_id when approving
   - Group capacity is checked before assignment

3. **Attendance Slots:**
   - Created automatically when enrollment is approved
   - One slot per student per session in the group
   - Initial status: `absent`
   - Updated when instructor marks attendance

4. **Enrollment Statuses:**
   - `pending` - Waiting for admin approval
   - `approved` - Approved and assigned to group
   - `rejected` - Rejected by admin
   - `withdrawn` - Withdrawn (by student or admin)

5. **Payment Status:**
   - Tracked separately from enrollment status
   - Values: `not_paid`, `paid`, `partial`
   - Does not affect enrollment approval

#### Public Enrollment

**Flow:**
1. Student fills public enrollment form (no login required)
2. System creates new User account (role: student)
3. System creates Enrollment (status: pending)
4. Student receives confirmation
5. Admin approves and assigns to group

**Validation:**
- Email must be unique
- Phone required
- Course must exist and be published
- Optional group_id (if provided, must exist)

---

### 5. Attendance Management

#### Attendance Flow

```
1. Enrollment Approved
   ↓
   Attendance slots created for all group sessions
   Initial status: absent
   
2. Session Scheduled
   ↓
   Instructor can view session attendance list
   
3. Instructor Takes Attendance
   ↓
   Instructor opens session → sees student list
   Instructor marks: present / absent / late
   
4. Attendance Recorded
   ↓
   Status updated
   marked_by: instructor_id
   marked_at: current timestamp
```

#### Attendance Rules

1. **Manual Attendance (NO QR):**
   - Instructor manually marks attendance
   - No QR code scanning
   - No automatic check-in

2. **Attendance Status:**
   - `present` - Student attended
   - `absent` - Student did not attend
   - `late` - Student attended but was late

3. **One Record Per Student Per Session:**
   - Each student has one attendance record per session
   - Record created when enrollment approved
   - Status updated when instructor marks attendance

4. **Who Can Mark Attendance:**
   - Instructors assigned to the group
   - Admins (can mark any attendance)

5. **Attendance Slots:**
   - Created automatically on enrollment approval
   - For all existing and future sessions in the group
   - Can be marked at any time (before/after session)

#### Attendance Tracking

**Instructor View:**
- See all sessions for their groups
- Select session → see student list
- Mark attendance (present/absent/late)
- View attendance history

**Student View:**
- View attendance history
- See status per session
- Filter by course/group

**Admin View:**
- Overview of all attendance
- Filter by course/group/session
- Reports and analytics

---

### 6. Certificate Issuance

#### Certificate Flow

```
1. Admin/Instructor Issues Certificate
   ↓
   Select: Student, Course, Group, Instructor
   
2. System Generates Certificate
   ↓
   - Unique certificate_number
   - Unique verification_code
   - QR code generated
   - PDF generated (with branding)
   
3. Certificate Created
   ↓
   Includes:
   - Student name
   - Course name
   - Group name
   - Instructor name
   - Issue date
   - Verification code
   - QR code
   
4. Public Verification
   ↓
   Anyone can verify at: /certificates/verify/{code}
```

#### Certificate Rules

1. **Required Fields:**
   - Student (required)
   - Course (required)
   - Group (optional but recommended)
   - Instructor (optional)
   - Issue date (defaults to today)

2. **Unique Identifiers:**
   - `certificate_number` - Unique format: CERT-{CODE}-{YEAR}
   - `verification_code` - Random 16-character code (unique)

3. **QR Code:**
   - Generated for each certificate
   - Links to public verification page
   - Format: `/certificates/verify/{verification_code}`

4. **Certificate Template:**
   - One default design
   - Uses academy branding:
     - Logo
     - Main color
     - Fonts
   - Includes all required fields

5. **Who Can Issue:**
   - Admins (can issue any certificate)
   - Instructors (can issue for their courses/groups)

6. **Public Verification:**
   - No authentication required
   - Anyone can verify using verification code or QR code
   - Shows certificate details (course, student, date, etc.)

---

### 7. Community Management

#### Community Structure

**Posts → Comments → Replies**

#### Post Rules

1. **Who Can Post:**
   - Students (in their groups)
   - Instructors (in their groups)
   - Admins (anywhere)

2. **Post Association:**
   - Can be associated with a Group (optional)
   - NO program/batch references

3. **Post Features:**
   - Title, body, attachments
   - Can be pinned (by admin)
   - Can be locked (by admin)
   - Likes (polymorphic)

4. **NO Gamification:**
   - NO XP/points
   - NO badges
   - NO leaderboards
   - NO rewards

#### Comment Rules

1. **Comments on Posts:**
   - Anyone in the group can comment
   - Comments have body text
   - Comments can be liked

2. **Replies to Comments:**
   - Anyone can reply to comments
   - Nested structure

3. **Moderation:**
   - Admin can delete posts/comments
   - Reports system for moderation
   - Admin can ban users from posting

---

### 8. Website Setup Wizard

#### Setup Flow

```
First Time Opening → Redirect to Setup Wizard

Step 1: General Information
   - Academy name
   - Country
   - Timezone

Step 2: Branding
   - Logo upload
   - Main color
   - Fonts

Step 3: Languages
   - Select default language (default: en)
   - Add additional languages (e.g., ar)
   - RTL support for Arabic

Step 4: Currency
   - Select default currency (default: EGP)
   - Add additional currencies

Step 5: Pages Activation
   - Activate/deactivate pages:
     - Home
     - About
     - Courses
     - Instructors
     - FAQ
     - Contact

Step 6: Launch
   - Activate website
   - System goes live
```

#### Setup Rules

1. **One-Time Setup:**
   - Setup wizard runs once
   - Cannot be re-run unless reset
   - Setup status stored in `website_settings`

2. **Activation:**
   - Website is inactive until setup is completed
   - Public routes redirect to setup wizard if not activated
   - Dashboard accessible after activation

3. **Branding:**
   - Logo used in public site and certificates
   - Colors used throughout UI
   - Fonts applied to public site

---

### 9. Multi-Language Support

#### Language Rules

1. **Default Language:**
   - Default: English (en)
   - Can be changed by admin

2. **Additional Languages:**
   - Admin can add languages
   - Each language has: code, name, is_rtl flag
   - RTL support for Arabic (ar)

3. **Translation Storage:**
   - UI texts: JSON translation files
   - Course/Group/Page content: JSON fields or translation tables
   - Frontend: Vue I18n

4. **RTL Support:**
   - Arabic (ar) has RTL enabled
   - HTML `dir="rtl"` attribute
   - CSS RTL overrides

---

### 10. Multi-Currency Support

#### Currency Rules

1. **Default Currency:**
   - Default: EGP (Egyptian Pound)
   - Can be changed by admin

2. **Course Pricing:**
   - Course price uses selected currency
   - Price displayed with currency symbol
   - No advanced FX conversion (simple currency selection)

3. **Currency Management:**
   - Admin can add currencies
   - Each currency has: code, name, symbol
   - One default currency at a time

---

### 11. CMS Page Builder

#### Page Rules

1. **Page Structure:**
   - Pages have: slug, title, content, template
   - Multi-language support (JSON fields)
   - SEO fields (meta title, description, keywords)

2. **Page Blocks:**
   - Pages can have multiple blocks
   - Block types: Hero, Features, FAQ, Contact, CTA, etc.
   - Blocks have order and data (JSON)

3. **Public Pages:**
   - Home (CMS-driven)
   - About (CMS page)
   - Courses (dynamic listing)
   - Instructors (dynamic listing)
   - FAQ (CMS page)
   - Contact (CMS page with form)

---

## Business Flow Examples

### Example 1: Complete Student Journey

```
1. Student visits public website
   ↓
2. Student browses courses
   ↓
3. Student enrolls in Course (public form)
   ↓
   - New user account created
   - Enrollment created (status: pending)
   
4. Admin reviews enrollment
   ↓
   - Admin approves enrollment
   - Admin assigns to Group "Group A - Morning"
   - Student added to group
   - Attendance slots created
   
5. Student logs in
   ↓
   - Dashboard shows enrolled courses
   - Shows assigned group
   - Shows upcoming sessions
   
6. Instructor marks attendance for sessions
   ↓
   - Student can view attendance history
   
7. Admin issues certificate
   ↓
   - Certificate generated with QR code
   - Student can view/download certificate
   - Public can verify certificate
```

### Example 2: Instructor Workflow

```
1. Instructor logs in
   ↓
   - Dashboard shows assigned groups
   
2. Instructor selects a group
   ↓
   - Sees all sessions in group
   - Sees all students in group
   
3. Session scheduled
   ↓
   - Instructor opens session
   - Sees student list with attendance status
   
4. Instructor marks attendance
   ↓
   - Selects: present / absent / late
   - Saves attendance
   - All students marked
   
5. Instructor can post in community
   ↓
   - Creates post for group
   - Students can comment/reply
```

### Example 3: Admin Workflow

```
1. Admin creates Course
   ↓
   - Sets price, description, image
   - Assigns instructors
   
2. Admin creates Groups
   ↓
   - Multiple groups per course
   - Sets dates, capacity, notes
   - Assigns instructors
   
3. Admin creates Sessions
   ↓
   - Creates sessions for groups
   - Sets date, time, optional instructor
   
4. Admin reviews enrollments
   ↓
   - Views pending enrollments
   - Approves/rejects
   - Assigns to groups on approval
   
5. Admin issues certificates
   ↓
   - Selects student, course, group
   - Issues certificate
   - Certificate available for verification
   
6. Admin moderates community
   ↓
   - Views reported posts
   - Deletes inappropriate content
   - Manages community
```

---

## Validation Rules

### Course Validation
- Title: required, max 255 characters
- Price: required, numeric, min 0
- Currency: required, must exist
- Language: required, must exist
- Category: optional, must exist if provided

### Group Validation
- Course: required, must exist
- Name: required, max 255 characters
- Start date: required, date format
- End date: required, date format, after start_date
- Capacity: required, integer, min 1

### Session Validation
- Group: required, must exist
- Title: required, max 255 characters
- Date: required, date format
- Start time: required, time format
- End time: required, time format, after start_time
- Instructor: optional, must exist if provided

### Enrollment Validation
- Student: required, must exist, role must be student
- Course: required, must exist
- Group: optional, must exist if provided, must belong to course

### Attendance Validation
- Session: required, must exist
- Student: required, must exist
- Status: required, must be: present, absent, late
- One record per student per session

### Certificate Validation
- Student: required, must exist
- Course: required, must exist
- Group: optional, must exist if provided
- Instructor: optional, must exist if provided
- Student must be enrolled in course

---

## State Management

### Enrollment States
- `pending` → Waiting for approval
- `approved` → Approved and active
- `rejected` → Rejected by admin
- `withdrawn` → Withdrawn (cancelled)

### Attendance States
- `absent` → Default state, not marked
- `present` → Marked as present
- `late` → Marked as late
- `absent` (explicit) → Marked as absent

### Session States
- `scheduled` → Future session
- `completed` → Past session
- `cancelled` → Cancelled session

---

## Data Integrity Rules

### Cascading Deletes
- Deleting Course → Cascades to Groups, Sessions, Enrollments, Certificates
- Deleting Group → Cascades to Sessions, Enrollments
- Deleting Session → Cascades to Attendance
- Deleting Enrollment → Cascades to Attendance (if applicable)

### Unique Constraints
- Course slug: unique
- Certificate verification_code: unique
- Certificate certificate_number: unique
- User email: unique

### Foreign Key Constraints
- All foreign keys properly defined
- On delete: cascade where appropriate
- On delete: set null for optional relationships

---

**Business Logic Status:** ✅ Aligned with final business specification

