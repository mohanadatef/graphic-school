# LMC Domain Model

## Overview

The Learning Management Core (LMC) is the heart of Graphic School 2.0. This document explains the complete domain model, relationships, and business logic.

## Domain Model Diagram

```
┌─────────────┐
│   Course    │
│             │
│ - title     │
│ - code      │
│ - price     │
│ - status    │
└──────┬──────┘
       │
       │ hasMany
       │
       ├──────────────────┬──────────────────┐
       │                  │                  │
       ▼                  ▼                  ▼
┌─────────────┐   ┌──────────────┐   ┌─────────────┐
│   Group     │   │SessionTemplate│   │ Enrollment │
│             │   │              │   │             │
│ - code      │   │ - title      │   │ - status    │
│ - name      │   │ - order      │   │ - payment   │
│ - capacity  │   │ - duration   │   │             │
│ - instructor│   └──────┬───────┘   └──────┬──────┘
└──────┬──────┘          │                  │
       │                 │                  │
       │ hasMany         │ hasMany          │ belongsTo
       │                 │                  │
       ▼                 ▼                  ▼
┌─────────────┐   ┌──────────────┐   ┌─────────────┐
│GroupSession │   │GroupSession  │   │   Student   │
│             │   │              │   │   (User)     │
│ - date      │   │ - date       │   │             │
│ - time      │   │ - time       │   │             │
│ - status    │   │ - status     │   └─────────────┘
└──────┬──────┘   └──────┬───────┘
       │                 │
       │ hasMany         │
       │                 │
       ▼                 ▼
┌─────────────┐   ┌──────────────┐
│ Attendance  │   │ Attendance   │
│             │   │              │
│ - status    │   │ - status     │
│ - student   │   │ - student    │
└─────────────┘   └──────────────┘
```

## Core Entities

### 1. Course

**Purpose:** The main learning program that contains groups and sessions.

**Key Attributes:**
- `title`: Course name
- `code`: Unique course code
- `slug`: URL-friendly identifier
- `category_id`: Course category
- `description`: Course description
- `price`: Course price
- `start_date`: Course start date
- `end_date`: Course end date
- `session_count`: Number of sessions
- `max_students`: Maximum students per course
- `status`: draft, upcoming, running, completed, archived
- `delivery_type`: on-site, online, hybrid

**Relationships:**
- `belongsTo` Category
- `belongsToMany` Users (instructors)
- `hasMany` Groups
- `hasMany` SessionTemplates
- `hasMany` Enrollments

**Business Rules:**
- A course must have at least one category
- A course can have multiple instructors
- A course can have multiple groups
- Groups are created within a course
- Session templates define the course structure

### 2. Group

**Purpose:** A specific class within a course. Groups allow multiple parallel classes of the same course.

**Key Attributes:**
- `course_id`: Parent course
- `code`: Group identifier (e.g., "A", "B", "Morning")
- `name`: Group name
- `capacity`: Maximum students
- `room`: Physical or virtual room
- `instructor_id`: Primary instructor
- `is_active`: Active status

**Relationships:**
- `belongsTo` Course
- `belongsTo` User (instructor)
- `belongsToMany` Users (students)
- `hasMany` GroupSessions

**Business Rules:**
- A group belongs to exactly one course
- A group has one primary instructor
- A group can have multiple students (up to capacity)
- A group cannot reduce capacity below current enrollments
- An instructor cannot be assigned to two groups in the same course simultaneously

### 3. SessionTemplate

**Purpose:** Reusable session structure that defines what sessions should look like for a course.

**Key Attributes:**
- `course_id`: Parent course
- `title`: Session title
- `session_order`: Order in sequence
- `description`: Session description
- `duration_minutes`: Session duration
- `default_start_time`: Default start time
- `default_end_time`: Default end time
- `is_required`: Whether session is mandatory
- `materials`: JSON array of materials

**Relationships:**
- `belongsTo` Course
- `hasMany` GroupSessions

**Business Rules:**
- Session templates belong to a course
- Templates define the structure, not the actual schedule
- Multiple groups can use the same template
- Templates are used to generate group sessions

### 4. GroupSession

**Purpose:** Actual scheduled session for a specific group on a specific date/time.

**Key Attributes:**
- `group_id`: Parent group
- `session_template_id`: Template used (optional)
- `title`: Session title
- `session_order`: Order in sequence
- `session_date`: Actual date
- `start_time`: Start time
- `end_time`: End time
- `meeting_link`: Online meeting link (if applicable)
- `note`: Session notes
- `status`: scheduled, completed, cancelled
- `student_comment`: Student feedback
- `instructor_comment`: Instructor notes
- `supervisor_comment`: Supervisor notes

**Relationships:**
- `belongsTo` Group
- `belongsTo` SessionTemplate (optional)
- `hasMany` Attendance

**Business Rules:**
- A group session belongs to exactly one group
- A group session can optionally reference a template
- Sessions are scheduled for specific dates/times
- Attendance is tracked per group session
- Sessions can be cancelled or rescheduled

### 5. Enrollment

**Purpose:** Student registration in a course, with assignment to a specific group.

**Key Attributes:**
- `student_id`: Student (User)
- `course_id`: Course
- `group_id`: Assigned group (nullable until approved)
- `payment_status`: pending, partial, paid, refunded
- `paid_amount`: Amount paid
- `status`: pending, approved, rejected, withdrawn
- `can_attend`: Whether student can attend sessions
- `approved_by`: Admin who approved
- `approved_at`: Approval timestamp
- `note`: Enrollment notes

**Relationships:**
- `belongsTo` User (student)
- `belongsTo` Course
- `belongsTo` Group

**Business Rules:**
- A student can enroll in multiple courses
- A student can only have one active enrollment per course
- Enrollment must be approved before group assignment
- Group assignment happens after approval
- Payment status is tracked separately from enrollment status

### 6. Attendance

**Purpose:** Record of student attendance for a specific group session.

**Key Attributes:**
- `group_session_id`: Group session
- `student_id`: Student
- `status`: present, absent, late, excused
- `note`: Attendance notes
- `marked_by`: User who marked attendance

**Relationships:**
- `belongsTo` GroupSession
- `belongsTo` User (student)
- `belongsTo` User (marked_by)

**Business Rules:**
- One attendance record per student per session
- Attendance can be marked by instructor or admin
- Status can be: present, absent, late, or excused
- Attendance history is maintained for reporting

## Domain Workflows

### Course Creation Workflow

1. **Admin creates course**
   - Define course details (title, description, price)
   - Assign category
   - Set course dates and capacity

2. **Admin assigns instructors**
   - Select instructors from user list
   - Assign to course
   - Can assign multiple instructors

3. **Admin creates session templates**
   - Define session structure
   - Set session order
   - Define default times and duration

4. **Admin creates groups**
   - Create groups within course
   - Assign instructor to each group
   - Set group capacity

5. **Admin publishes course**
   - Course becomes visible to students
   - Enrollment opens

### Enrollment Workflow

1. **Student views course**
   - Student browses public courses
   - Views course details

2. **Student submits enrollment**
   - Fills enrollment form
   - Enrollment status: pending

3. **Admin reviews enrollment**
   - Admin sees pending enrollments
   - Reviews student information

4. **Admin approves/rejects**
   - If approved: status → approved
   - If rejected: status → rejected

5. **Group assignment**
   - Admin assigns student to group
   - Group capacity checked
   - Student can now attend sessions

### Session Scheduling Workflow

1. **Instructor/Admin creates group session**
   - Selects group
   - Optionally selects session template
   - Sets date and time
   - Adds meeting link if online

2. **Session appears in calendar**
   - Visible to instructor
   - Visible to students in group
   - Visible to admin

3. **Session execution**
   - Session occurs on scheduled date/time
   - Instructor takes attendance
   - Notes can be added

4. **Session completion**
   - Status updated to completed
   - Attendance finalized
   - Comments can be added

### Attendance Workflow

1. **Session scheduled**
   - Group session created
   - Students enrolled in group

2. **Attendance marking**
   - Instructor opens attendance for session
   - Marks each student: present/absent/late/excused
   - Can add notes

3. **Attendance recording**
   - Attendance records created
   - Linked to group session and student
   - Timestamp recorded

4. **Attendance history**
   - Students can view their attendance
   - Admin can view all attendance
   - Reports generated from attendance data

## Business Rules Summary

### Course Rules
- Course must have category
- Course can have multiple instructors
- Course can have multiple groups
- Groups share course session templates

### Group Rules
- Group belongs to one course
- Group has one primary instructor
- Group capacity cannot be reduced below current enrollments
- Instructor cannot be in two groups of same course simultaneously

### Enrollment Rules
- One active enrollment per student per course
- Enrollment must be approved before group assignment
- Group assignment requires available capacity
- Payment tracked separately from enrollment

### Session Rules
- Group sessions belong to one group
- Sessions can reference templates
- Sessions have specific date/time
- Attendance tracked per session

### Attendance Rules
- One attendance record per student per session
- Attendance can be marked by instructor or admin
- Attendance status: present, absent, late, excused
- Attendance history maintained

## Data Integrity

### Foreign Key Constraints
- Groups require valid course
- Group sessions require valid group
- Enrollments require valid student, course, and group
- Attendance requires valid group session and student

### Unique Constraints
- Course code must be unique
- Course slug must be unique
- One active enrollment per student per course
- One attendance record per student per session

### Validation Rules
- Group capacity must be positive
- Session dates must be valid
- Enrollment status transitions are controlled
- Attendance status values are restricted

## Performance Considerations

### Indexing
- Course: `slug`, `code`, `category_id`
- Group: `course_id`, `instructor_id`
- GroupSession: `group_id`, `session_date`
- Enrollment: `student_id`, `course_id`, `group_id`, `status`
- Attendance: `group_session_id`, `student_id`

### Query Optimization
- Eager loading for relationships
- Pagination for large datasets
- Caching for frequently accessed data
- Database indexes on foreign keys

## Conclusion

The LMC domain model provides a clean, flexible structure for managing courses, groups, sessions, enrollments, and attendance. The model supports:
- Multiple instructors per course
- Multiple groups per course
- Flexible session scheduling
- Comprehensive attendance tracking
- Clear enrollment workflow

All relationships are well-defined, business rules are enforced, and the model scales to support large numbers of courses, groups, and students.

