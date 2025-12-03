# Business Logic

## Overview

Business logic is distributed across models, services, and controllers. This document outlines key business rules and workflows.

## Enrollment Business Logic

### Enrollment Workflow
1. **Student submits enrollment**
   - Status: `pending`
   - No group assignment yet
   - Payment status: `pending`

2. **Admin reviews enrollment**
   - Can approve or reject
   - Can assign to group on approval

3. **Enrollment approved**
   - Status: `approved`
   - Group assigned (if available)
   - `can_attend` set to `true`
   - Attendance slots created for all group sessions

4. **Enrollment rejected**
   - Status: `rejected`
   - No group assignment
   - Student notified

5. **Enrollment withdrawn**
   - Status: `withdrawn`
   - `can_attend` set to `false`
   - Student removed from group

### Enrollment Rules
- One active enrollment per student per course
- Group assignment requires available capacity
- Payment tracked separately from enrollment status
- Enrollment can be withdrawn after approval

## Group Business Logic

### Group Capacity
- Group has maximum capacity
- Capacity cannot be reduced below current enrollments
- Available spots = capacity - current enrollments
- Enrollment checks capacity before assignment

### Instructor Assignment
- One primary instructor per group
- Instructor cannot be assigned to two groups in same course simultaneously
- Instructor can be assigned to groups in different courses

### Group Creation
- Group belongs to exactly one course
- Group code must be unique within course
- Group can be active or inactive
- Inactive groups don't accept new enrollments

## Session Business Logic

### Session Templates
- Templates define session structure
- Templates belong to a course
- Multiple groups can use same template
- Templates define default times and duration

### Group Sessions
- Sessions belong to exactly one group
- Sessions can reference a template (optional)
- Sessions have specific date/time
- Sessions can be scheduled, completed, or cancelled
- Attendance tracked per session

### Session Scheduling
- Sessions scheduled for specific dates/times
- Can be auto-generated from templates
- Can be manually created
- Sessions appear in calendar

## Attendance Business Logic

### Attendance Recording
- One attendance record per student per session
- Attendance can be marked by instructor or admin
- Status: present, absent, late, excused
- Timestamp recorded when marked present
- Notes can be added

### Attendance Workflow
1. **Session scheduled**
   - Attendance slots can be pre-created
   - Default status: `absent`

2. **Instructor takes attendance**
   - Marks each student
   - Can update later
   - Notes can be added

3. **Attendance finalized**
   - Recorded in database
   - Visible to students
   - Used for reports

### QR Attendance
- QR code generated for session
- Time-limited token
- Student scans QR to check in
- Automatic attendance marking
- Token expires after session time

## Course Business Logic

### Course Creation
- Course must have category
- Course can have multiple instructors
- Course can have multiple groups
- Course can have session templates
- Course can be published or hidden

### Course Publishing
- Published courses visible to students
- Hidden courses not visible publicly
- Status: draft, upcoming, running, completed, archived
- Enrollment opens when published

### Course Instructors
- Multiple instructors per course
- One instructor can be supervisor
- Instructors can be assigned to groups
- Instructor assignment tracked

## Page Builder Business Logic

### Page Structure
- Pages have multiple blocks
- Blocks can be enabled/disabled
- Blocks rendered in order
- Multi-language content support

### Block Management
- Blocks have types (hero, features, cta, etc.)
- Blocks have configuration (JSON)
- Blocks can be reordered
- Blocks can be enabled/disabled per language

### Page Rendering
- Only active pages displayed
- Only enabled blocks rendered
- Content retrieved per language
- Fallback to default language

## Website Activation Business Logic

### Activation Workflow
1. **First visit**
   - System checks if activated
   - If not, redirects to setup wizard

2. **Setup wizard**
   - General information
   - Branding
   - Pages configuration
   - Contact information
   - Review and launch

3. **Activation**
   - Settings saved
   - Default pages created
   - Website activated
   - Public access enabled

### Activation Rules
- Website must be activated before public access
- Setup wizard runs on first visit
- Default pages created on activation
- Settings can be updated after activation

## Multi-Language Business Logic

### Language Management
- Languages stored in database
- One default language
- Multiple active languages supported
- RTL support for Arabic, Hebrew, etc.

### Content Translation
- Pages: JSON fields for multi-language
- Blocks: JSON fields for multi-language
- Courses: Can use translations (optional)
- Fallback to default language

### Language Switching
- User can switch language
- Preference saved in localStorage
- API requests include language header
- Frontend updates immediately

## Validation Rules

### Course Validation
- Title required
- Code must be unique
- Slug must be unique
- Category required
- Price must be non-negative

### Group Validation
- Course required
- Code required
- Capacity must be positive
- Capacity cannot be reduced below enrollments
- Instructor cannot be in two groups of same course

### Enrollment Validation
- Student required
- Course required
- One active enrollment per student per course
- Group capacity checked on assignment

### Session Validation
- Group required
- Date/time must be valid
- Status must be valid enum value

### Attendance Validation
- Group session required
- Student required
- Status must be valid enum value
- One record per student per session

## Error Handling

### Business Rule Violations
- Custom exceptions thrown
- Error messages returned to frontend
- Logged for debugging
- User-friendly error messages

### Data Integrity
- Foreign key constraints
- Unique constraints
- Validation rules
- Transaction management

## Conclusion

Business logic ensures:
- Data consistency
- Workflow integrity
- Rule enforcement
- Error prevention
- User experience

All business rules are enforced at multiple levels:
- Database constraints
- Model validation
- Service logic
- Controller validation

