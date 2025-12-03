# Manual Test Cases

## Overview

This document provides comprehensive manual test cases for Graphic School 2.0. These test cases should be executed before each release.

## Authentication Tests

### TC-AUTH-001: User Login

**Preconditions:**
- User account exists
- User is not logged in

**Steps:**
1. Navigate to `/login`
2. Enter valid email
3. Enter valid password
4. Click "Login"

**Expected Result:**
- User redirected to dashboard
- User menu shows user name
- No error messages

### TC-AUTH-002: Invalid Login

**Steps:**
1. Navigate to `/login`
2. Enter invalid email
3. Enter invalid password
4. Click "Login"

**Expected Result:**
- Error message displayed
- User remains on login page
- No redirect

### TC-AUTH-003: User Logout

**Preconditions:**
- User is logged in

**Steps:**
1. Click user menu
2. Click "Logout"

**Expected Result:**
- User redirected to login
- Session cleared
- Cannot access dashboard

## Admin Tests

### TC-ADMIN-001: Create Course

**Preconditions:**
- Admin logged in
- Category exists

**Steps:**
1. Navigate to `/dashboard/admin/courses`
2. Click "Create Course"
3. Fill course form:
   - Title
   - Category
   - Description
   - Price
4. Click "Save"

**Expected Result:**
- Course created successfully
- Redirected to courses list
- Course appears in list

### TC-ADMIN-002: Edit Course

**Preconditions:**
- Course exists
- Admin logged in

**Steps:**
1. Navigate to courses list
2. Click "Edit" on a course
3. Modify course details
4. Click "Save"

**Expected Result:**
- Course updated successfully
- Changes reflected in list
- No errors

### TC-ADMIN-003: Create Group

**Preconditions:**
- Course exists
- Instructor exists
- Admin logged in

**Steps:**
1. Navigate to `/dashboard/admin/groups`
2. Click "Create Group"
3. Fill group form:
   - Course
   - Code
   - Capacity
   - Instructor
4. Click "Save"

**Expected Result:**
- Group created successfully
- Appears in groups list
- Can assign students

### TC-ADMIN-004: Approve Enrollment

**Preconditions:**
- Pending enrollment exists
- Admin logged in

**Steps:**
1. Navigate to `/dashboard/admin/enrollments`
2. Find pending enrollment
3. Click "Approve"
4. Assign to group
5. Confirm approval

**Expected Result:**
- Enrollment approved
- Student assigned to group
- Status updated
- Student can access course

## Instructor Tests

### TC-INSTR-001: View My Groups

**Preconditions:**
- Instructor logged in
- Instructor assigned to groups

**Steps:**
1. Navigate to `/dashboard/instructor/my-groups`
2. View groups list

**Expected Result:**
- Only assigned groups shown
- Group details visible
- Can navigate to sessions

### TC-INSTR-002: Take Attendance

**Preconditions:**
- Group session exists
- Students enrolled
- Instructor logged in

**Steps:**
1. Navigate to session
2. Click "Take Attendance"
3. Mark each student:
   - Present
   - Absent
   - Late
4. Add notes (optional)
5. Click "Save"

**Expected Result:**
- Attendance saved
- Records created
- Visible to students
- Visible in reports

### TC-INSTR-003: View Students

**Preconditions:**
- Group has students
- Instructor logged in

**Steps:**
1. Navigate to group
2. Click "Students"
3. View student list

**Expected Result:**
- All group students shown
- Student details visible
- Can view attendance

## Student Tests

### TC-STUD-001: View My Courses

**Preconditions:**
- Student logged in
- Student enrolled in courses

**Steps:**
1. Navigate to `/dashboard/student/my-courses`
2. View courses list

**Expected Result:**
- Only enrolled courses shown
- Course details visible
- Can view course content

### TC-STUD-002: View Attendance History

**Preconditions:**
- Student logged in
- Attendance records exist

**Steps:**
1. Navigate to `/dashboard/student/attendance-history`
2. View attendance records
3. Filter by course/date

**Expected Result:**
- All attendance shown
- Statistics displayed
- Filters work correctly

### TC-STUD-003: View My Group

**Preconditions:**
- Student enrolled in group
- Student logged in

**Steps:**
1. Navigate to `/dashboard/student/my-group`
2. View group details

**Expected Result:**
- Group information shown
- Instructor details visible
- Schedule displayed

## Public Site Tests

### TC-PUB-001: View Courses

**Preconditions:**
- Website activated
- Courses published

**Steps:**
1. Navigate to `/courses`
2. View courses list
3. Filter by category
4. Search courses

**Expected Result:**
- All published courses shown
- Filters work
- Search works
- Course cards display correctly

### TC-PUB-002: Course Details

**Preconditions:**
- Course published

**Steps:**
1. Navigate to `/courses/{id}`
2. View course details

**Expected Result:**
- Course information shown
- Instructor details visible
- Enrollment button works

### TC-PUB-003: Public Enrollment

**Preconditions:**
- Course published
- Enrollment open

**Steps:**
1. Navigate to course details
2. Click "Enroll"
3. Fill enrollment form
4. Submit enrollment

**Expected Result:**
- Enrollment submitted
- Confirmation shown
- Admin notified

## Setup Wizard Tests

### TC-SETUP-001: First Visit

**Preconditions:**
- Website not activated
- Fresh installation

**Steps:**
1. Visit website
2. Verify redirect to `/setup`
3. Complete setup wizard

**Expected Result:**
- Redirected to setup
- Can complete all steps
- Website activates
- Default pages created

### TC-SETUP-002: Setup Steps

**Steps:**
1. Complete General Information
2. Complete Branding
3. Complete Pages
4. Complete Contact
5. Review and Launch

**Expected Result:**
- Each step saves
- Can go back
- Can skip optional steps
- Final activation works

## Multi-Language Tests

### TC-LANG-001: Language Switching

**Preconditions:**
- Multiple languages active

**Steps:**
1. Click language switcher
2. Select different language
3. Verify content changes

**Expected Result:**
- Language changes
- Content translated
- RTL applied (if Arabic)
- Preference saved

### TC-LANG-002: RTL Support

**Preconditions:**
- Arabic language active

**Steps:**
1. Switch to Arabic
2. Verify RTL layout
3. Check all pages

**Expected Result:**
- Layout RTL
- Text aligned correctly
- Navigation RTL
- Forms RTL

## CMS Tests

### TC-CMS-001: Edit Page

**Preconditions:**
- Admin logged in
- Page exists

**Steps:**
1. Navigate to `/dashboard/admin/pages`
2. Click "Edit" on a page
3. Modify content
4. Enable/disable blocks
5. Save changes

**Expected Result:**
- Changes saved
- Content updated
- Blocks render correctly
- Public page updated

### TC-CMS-002: Create Block

**Preconditions:**
- Admin logged in
- Page exists

**Steps:**
1. Edit page
2. Add new block
3. Configure block
4. Save

**Expected Result:**
- Block created
- Appears on page
- Renders correctly

## Performance Tests

### TC-PERF-001: Page Load Time

**Steps:**
1. Open browser DevTools
2. Navigate to homepage
3. Check load time

**Expected Result:**
- Load time < 2 seconds
- No console errors
- Images optimized

### TC-PERF-002: API Response Time

**Steps:**
1. Open Network tab
2. Navigate through app
3. Check API response times

**Expected Result:**
- API responses < 500ms
- No slow queries
- Efficient caching

## Security Tests

### TC-SEC-001: Unauthorized Access

**Steps:**
1. Logout
2. Try to access `/dashboard/admin/courses`
3. Verify redirect

**Expected Result:**
- Redirected to login
- Cannot access protected routes
- Error message shown

### TC-SEC-002: Role-Based Access

**Preconditions:**
- Student logged in

**Steps:**
1. Try to access `/dashboard/admin/courses`
2. Verify access denied

**Expected Result:**
- Access denied
- Redirected appropriately
- Error message shown

## Conclusion

These manual test cases ensure:
- Functionality works correctly
- User workflows are smooth
- Security is maintained
- Performance is acceptable

Execute these test cases before each release to ensure quality.

