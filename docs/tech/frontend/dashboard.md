# Frontend Dashboard

## Overview

The dashboard provides role-specific interfaces for Admin, Instructor, and Student users. All dashboard pages use `DashboardLayout` with sidebar navigation.

## Dashboard Layout

### Layout Structure

`DashboardLayout.vue` provides:
- Sidebar navigation
- Header with user menu
- Main content area
- Role-based navigation
- Language switcher
- Theme toggle

### Sidebar Navigation

Navigation links are filtered by role:
- Admin: Full access to all admin features
- Instructor: Instructor-specific features
- Student: Student-specific features

## Admin Dashboard

### Dashboard Overview (`AdminDashboard.vue`)

Main admin dashboard showing:
- System statistics
- Recent enrollments
- Upcoming sessions
- Quick actions

### Course Management

#### Courses List (`AdminCourses.vue`)
- List all courses
- Filter by category, status
- Search courses
- Create/edit/delete courses

#### Course Form (`CourseForm.vue`)
- Create new course
- Edit existing course
- Multi-language support
- Instructor assignment
- Session template creation

### Group Management

#### Groups List (`AdminGroups.vue`)
- List all groups
- Filter by course, instructor
- Search groups
- Create/edit/delete groups

#### Group Create (`AdminGroupCreate.vue`)
- Create new group
- Select course
- Assign instructor
- Set capacity

#### Group Edit (`AdminGroupEdit.vue`)
- Edit group details
- Manage students
- Update capacity
- Change instructor

#### Group View (`AdminGroupView.vue`)
- View group details
- See enrolled students
- View group sessions
- Attendance overview

### Session Management

#### Sessions List (`AdminSessions.vue`)
- List all sessions
- Filter by group, date
- Search sessions
- Edit/delete sessions

#### Session Form (`SessionForm.vue`)
- Create/edit session
- Select group
- Set date/time
- Add meeting link

### Enrollment Management

#### Enrollments List (`AdminEnrollments.vue`)
- List all enrollments
- Filter by status, course
- Search enrollments
- Approve/reject enrollments

#### Enrollment Form (`EnrollmentForm.vue`)
- Create enrollment
- Edit enrollment
- Assign to group
- Update status

### Attendance Management

#### Attendance Overview (`AdminAttendance.vue`)
- Attendance statistics
- Filter by course, group, date
- Export attendance reports
- View attendance trends

### User Management

#### Users List (`AdminUsers.vue`)
- List all users
- Filter by role
- Search users
- Create/edit/delete users

#### User Form (`UserForm.vue`)
- Create/edit user
- Assign role
- Set permissions
- Update profile

### Role Management

#### Roles List (`AdminRoles.vue`)
- List all roles
- View permissions
- Create/edit/delete roles

#### Role Form (`RoleForm.vue`)
- Create/edit role
- Assign permissions
- Set role description

### CMS Management

#### Pages List (`AdminPages.vue`)
- List all CMS pages
- Create/edit/delete pages
- Preview pages

#### CMS Editor (`CMSEditor.vue`)
- Edit page content
- Manage blocks
- Multi-language content
- Enable/disable blocks

### Settings

#### System Settings (`AdminSettings.vue`)
- General settings
- Email configuration
- Payment settings
- System preferences

#### Languages (`AdminLanguages.vue`)
- Manage languages
- Activate/deactivate
- Set default language
- Configure RTL

#### Currencies (`AdminCurrencies.vue`)
- Manage currencies
- Activate/deactivate
- Set default currency

#### Countries (`AdminCountries.vue`)
- Manage countries
- Activate/deactivate
- Set default country

### Calendar

#### Calendar View (`AdminCalendar.vue`)
- View all events
- Filter by type
- Create/edit events
- Session calendar

## Instructor Dashboard

### My Groups (`InstructorMyGroups.vue`)

- List assigned groups
- View group details
- Navigate to group sessions
- View students

### Group Sessions (`InstructorGroupSessions.vue`)

- View group sessions
- Create new sessions
- Edit sessions
- View attendance

### Take Attendance (`InstructorTakeAttendance.vue`)

- Mark attendance for session
- Bulk attendance update
- Add notes
- View attendance history

### Students List (`InstructorStudentsList.vue`)

- View group students
- Student profiles
- Attendance overview
- Contact students

### Sessions (`InstructorSessions.vue`)

- All instructor sessions
- Filter by group, date
- Session calendar
- Session management

### Calendar (`InstructorCalendar.vue`)

- Calendar view
- All sessions
- Filter by group
- Event management

## Student Dashboard

### My Courses (`StudentMyCourses.vue`)

- Enrolled courses
- Course progress
- Course details
- Enrollment status

### My Group (`StudentMyGroup.vue`)

- Assigned group
- Group details
- Instructor information
- Group schedule

### My Sessions (`StudentMySessions.vue`)

- Upcoming sessions
- Past sessions
- Session details
- Meeting links

### Attendance History (`StudentAttendanceHistory.vue`)

- Attendance records
- Attendance statistics
- Filter by course, date
- Export attendance

### Profile (`StudentProfile.vue`)

- Personal information
- Edit profile
- Change password
- Account settings

### Calendar (`StudentCalendar.vue`)

- Personal calendar
- Upcoming sessions
- Events
- Schedule view

## Dashboard Features

### Navigation

- Sidebar navigation
- Breadcrumbs
- Quick actions
- Search functionality

### Filtering

- Filter by various criteria
- Search across entities
- Date range filters
- Status filters

### Pagination

- Paginated lists
- Page size selection
- Navigation controls
- Total count display

### Actions

- Create new items
- Edit existing items
- Delete items
- Bulk operations

### Notifications

- In-app notifications
- Toast messages
- Error alerts
- Success confirmations

## Responsive Design

### Mobile Support

- Responsive layouts
- Mobile-friendly navigation
- Touch-optimized controls
- Adaptive forms

### Tablet Support

- Optimized layouts
- Touch interactions
- Responsive tables
- Adaptive components

## Accessibility

### Keyboard Navigation

- Tab navigation
- Keyboard shortcuts
- Focus management
- ARIA labels

### Screen Readers

- Semantic HTML
- ARIA attributes
- Alt text for images
- Descriptive labels

## Conclusion

The dashboard provides:
- Role-specific interfaces
- Comprehensive management tools
- Intuitive navigation
- Responsive design
- Accessibility support

All dashboard pages follow consistent patterns and provide a cohesive user experience.

