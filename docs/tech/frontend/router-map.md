# Frontend Router Map

## Overview

Vue Router configuration defines all frontend routes. Routes are organized by layout (Public, Dashboard) and role (Admin, Instructor, Student).

## Route Structure

### Public Routes (`/`)

All public routes use `PublicLayout`:

- `/` - Homepage (CMS-driven)
- `/courses` - Courses listing
- `/courses/:id` - Course details
- `/instructors` - Instructors listing
- `/instructors/:id` - Instructor details
- `/trainers` - Redirects to `/instructors`
- `/faq` - FAQ page (CMS-driven)
- `/about` - About page (CMS-driven)
- `/contact` - Contact page (CMS-driven)
- `/login` - Login page (guest only)
- `/register` - Registration page (guest only)
- `/setup` - Setup wizard (no auth required)
- `/enroll` - Public enrollment form
- `/certificate/verify` - Certificate verification

### Dashboard Routes (`/dashboard`)

All dashboard routes use `DashboardLayout` and require authentication.

#### Admin Routes (`/dashboard/admin`)

- `/dashboard/admin` - Admin dashboard
- `/dashboard/admin/users` - Users management
- `/dashboard/admin/users/new` - Create user
- `/dashboard/admin/users/:id/edit` - Edit user
- `/dashboard/admin/roles` - Roles management
- `/dashboard/admin/roles/new` - Create role
- `/dashboard/admin/roles/:id/edit` - Edit role
- `/dashboard/admin/courses` - Courses management
- `/dashboard/admin/courses/new` - Create course
- `/dashboard/admin/courses/:id/edit` - Edit course
- `/dashboard/admin/groups` - Groups management
- `/dashboard/admin/groups/new` - Create group
- `/dashboard/admin/groups/:id/edit` - Edit group
- `/dashboard/admin/groups/:groupId` - View group
- `/dashboard/admin/sessions` - Sessions management
- `/dashboard/admin/sessions/:id/edit` - Edit session
- `/dashboard/admin/enrollments` - Enrollments management
- `/dashboard/admin/enrollments/:id/edit` - Edit enrollment
- `/dashboard/admin/attendance` - Attendance overview
- `/dashboard/admin/attendance/overview` - Attendance overview (alternative)
- `/dashboard/admin/attendance/qr` - QR attendance management
- `/dashboard/admin/pages` - CMS pages management
- `/dashboard/admin/pages/new` - Create page
- `/dashboard/admin/pages/:id/edit` - Edit page
- `/dashboard/admin/cms` - CMS editor
- `/dashboard/admin/languages` - Languages management
- `/dashboard/admin/currencies` - Currencies management
- `/dashboard/admin/countries` - Countries management
- `/dashboard/admin/settings` - System settings
- `/dashboard/admin/calendar` - Calendar view
- `/dashboard/admin/language` - Language settings

#### Instructor Routes (`/dashboard/instructor`)

- `/dashboard/instructor` - Redirects to `/dashboard/instructor/my-groups`
- `/dashboard/instructor/my-groups` - My groups
- `/dashboard/instructor/groups` - Redirects to `/dashboard/instructor/my-groups`
- `/dashboard/instructor/groups/:groupId/sessions` - Group sessions
- `/dashboard/instructor/groups/:groupId/students` - Group students
- `/dashboard/instructor/sessions` - All sessions
- `/dashboard/instructor/sessions/:sessionId/attendance` - Take attendance
- `/dashboard/instructor/calendar` - Calendar view

#### Student Routes (`/dashboard/student`)

- `/dashboard/student` - Redirects to `/dashboard/student/courses`
- `/dashboard/student/courses` - My courses
- `/dashboard/student/my-courses` - My courses (alternative)
- `/dashboard/student/my-group` - My group
- `/dashboard/student/my-sessions` - My sessions
- `/dashboard/student/attendance-history` - Attendance history
- `/dashboard/student/profile` - Profile
- `/dashboard/student/calendar` - Calendar view

## Route Guards

### Authentication Middleware
- `authMiddleware` - Checks if user is authenticated
- `guestMiddleware` - Checks if user is NOT authenticated

### Role Middleware
- `roleMiddleware('admin')` - Requires admin role
- `roleMiddleware('instructor')` - Requires instructor role
- `roleMiddleware('student')` - Requires student role

### Setup Check Middleware
- `setupCheckMiddleware` - Redirects to setup if website not activated

## Route Behavior

### Redirects
- Authenticated users accessing `/login` or `/register` are redirected based on role
- `/dashboard` redirects to role-specific dashboard
- `/dashboard/groups` redirects to `/dashboard/admin/groups` for admins

### 404 Handling
- Unmatched routes show `NotFound.vue`
- Self-healing system attempts to create missing routes (development only)

## Route Meta

Routes include meta information:
- `middleware` - Array of middleware functions
- `requiresAuth` - Boolean flag for authentication requirement
- `props` - Route props configuration

## Navigation

Navigation links are defined in:
- `DashboardLayout.vue` - Sidebar navigation
- `PublicLayout.vue` - Public navigation

Links are filtered by user role and displayed accordingly.

## Route Protection

All dashboard routes are protected:
1. Authentication check
2. Role check
3. Setup check (for public routes)

Unauthorized access redirects to `/login`.

## Dynamic Routes

Dynamic routes use parameters:
- `:id` - Resource ID
- `:slug` - URL-friendly identifier
- `:groupId` - Group ID
- `:sessionId` - Session ID

Parameters are passed as props to components.

## Route Lazy Loading

All routes use lazy loading:
```javascript
component: () => import('../views/dashboard/admin/AdminCourses.vue')
```

This improves initial load time by code-splitting.

## Conclusion

The router provides:
- Clear route organization
- Role-based access control
- Lazy loading for performance
- Consistent navigation
- Proper error handling

