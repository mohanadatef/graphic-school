# API Routes

## Overview

All API routes are defined in `routes/api.php`. Routes are organized by authentication level and user role.

## Route Organization

### Public Routes (No Authentication)
- Setup wizard
- Public content
- Authentication
- Language/locale

### Authenticated Routes
- Student routes
- Instructor routes
- Admin routes

## Public Routes

### Setup Wizard
```
GET    /api/setup/status
POST   /api/setup/step/general
POST   /api/setup/step/branding
POST   /api/setup/step/pages
POST   /api/setup/step/contact
POST   /api/setup/activate
```

### Public Content
```
GET    /api/home
GET    /api/courses
GET    /api/courses/{course}
GET    /api/categories
GET    /api/instructors
GET    /api/instructors/{instructor}
GET    /api/settings
POST   /api/contact
GET    /api/public/pages/{slug}
GET    /api/public/courses
GET    /api/public/courses/{course}
GET    /api/public/instructors
GET    /api/public/instructors/{instructor}
POST   /api/public/contact
```

### Authentication
```
POST   /api/register
POST   /api/login
POST   /api/logout (authenticated)
```

### Language/Locale
```
GET    /api/locale
GET    /api/locales
POST   /api/locale/{locale}
GET    /api/translations
GET    /api/translations/{group}
```

### System
```
GET    /api/health
GET    /api/docs
GET    /api/docs-json
GET    /api/docs-yaml
GET    /api/branding/frontend
GET    /api/qr/{token}
```

## Student Routes (`/api/student`)

### Dashboard
```
GET    /api/student/my-courses
GET    /api/student/my-group
GET    /api/student/my-sessions
GET    /api/student/attendance-history
GET    /api/student/profile
```

### Enrollment
```
POST   /api/student/enroll
GET    /api/student/enrollments
```

### Attendance
```
GET    /api/student/attendance
POST   /api/student/qr-checkin
```

## Instructor Routes (`/api/instructor`)

### Dashboard
```
GET    /api/instructor/my-groups
GET    /api/instructor/groups/{groupId}/sessions
GET    /api/instructor/groups/{groupId}/students
GET    /api/instructor/sessions/{sessionId}/attendance
POST   /api/instructor/sessions/{sessionId}/attendance
```

### Sessions
```
GET    /api/instructor/sessions
GET    /api/instructor/sessions/{sessionId}/attendance
POST   /api/instructor/sessions/{sessionId}/attendance/update
POST   /api/instructor/sessions/{sessionId}/qr-generate
```

## Admin Routes (`/api/admin`)

### Dashboard
```
GET    /api/admin/dashboard
```

### Users & Roles
```
GET    /api/admin/users
POST   /api/admin/users
GET    /api/admin/users/{id}
PUT    /api/admin/users/{id}
DELETE /api/admin/users/{id}
GET    /api/admin/roles
POST   /api/admin/roles
GET    /api/admin/roles/{id}
PUT    /api/admin/roles/{id}
DELETE /api/admin/roles/{id}
```

### Courses
```
GET    /api/admin/courses
POST   /api/admin/courses
GET    /api/admin/courses/{id}
PUT    /api/admin/courses/{id}
DELETE /api/admin/courses/{id}
POST   /api/admin/courses/{course}/assign-instructors
POST   /api/admin/courses/{course}/sessions/generate
```

### Groups
```
GET    /api/admin/groups
POST   /api/admin/groups
GET    /api/admin/groups/{id}
PUT    /api/admin/groups/{id}
DELETE /api/admin/groups/{id}
```

### Sessions
```
GET    /api/admin/sessions
GET    /api/admin/sessions/{id}
PUT    /api/admin/sessions/{id}
DELETE /api/admin/sessions/{id}
```

### Enrollments
```
GET    /api/admin/enrollments
POST   /api/admin/enrollments
PUT    /api/admin/enrollments/{id}
POST   /api/admin/enrollments/{id}/approve
POST   /api/admin/enrollments/{id}/reject
POST   /api/admin/enrollments/{id}/withdraw
```

### Attendance
```
GET    /api/admin/attendance
```

### CMS Pages
```
GET    /api/admin/pages
POST   /api/admin/pages
GET    /api/admin/pages/{id}
PUT    /api/admin/pages/{id}
DELETE /api/admin/pages/{id}
GET    /api/admin/pages/slug/{slug}
PUT    /api/admin/pages/{id}/blocks
```

### Languages
```
GET    /api/admin/languages
POST   /api/admin/languages
GET    /api/admin/languages/{id}
PUT    /api/admin/languages/{id}
DELETE /api/admin/languages/{id}
GET    /api/admin/languages/active
```

### Currencies
```
GET    /api/admin/currencies
POST   /api/admin/currencies
GET    /api/admin/currencies/{id}
PUT    /api/admin/currencies/{id}
DELETE /api/admin/currencies/{id}
GET    /api/admin/currencies/active
```

### Countries
```
GET    /api/admin/countries
POST   /api/admin/countries
GET    /api/admin/countries/{id}
PUT    /api/admin/countries/{id}
DELETE /api/admin/countries/{id}
GET    /api/admin/countries/active
```

### Settings
```
GET    /api/admin/settings
POST   /api/admin/settings
```

### Setup Wizard
```
GET    /api/admin/setup/status
POST   /api/admin/setup/save-step/{step}
POST   /api/admin/setup/activate-default
POST   /api/admin/setup/complete
POST   /api/admin/setup/reset
POST   /api/admin/setup/test-email
```

### Categories
```
GET    /api/admin/categories
POST   /api/admin/categories
GET    /api/admin/categories/{id}
PUT    /api/admin/categories/{id}
DELETE /api/admin/categories/{id}
```

## Route Middleware

### Authentication
- `auth:api` - Requires valid API token
- `guest` - Requires no authentication

### Authorization
- `role:admin` - Requires admin role
- `role:instructor` - Requires instructor role
- `role:student` - Requires student role

### Rate Limiting
- Auth routes: `throttle:5,1` (5 attempts per minute)
- API routes: Standard rate limiting

## Route Parameters

### Resource Routes
Standard RESTful resource routes:
- `index` - List resources
- `store` - Create resource
- `show` - Get single resource
- `update` - Update resource
- `destroy` - Delete resource

### Custom Routes
Additional custom routes for specific operations:
- Approval/rejection actions
- Bulk operations
- Reports and analytics
- File operations

## Response Formats

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... },
  "meta": {
    "pagination": { ... }
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error message"]
  }
}
```

## Route Naming

Routes follow RESTful conventions:
- Resource routes: `resource.index`, `resource.store`, etc.
- Custom routes: `resource.action` (e.g., `enrollments.approve`)

## API Versioning

Currently using unversioned API. Future versions can be added:
- `/api/v1/...`
- `/api/v2/...`

## CORS Configuration

CORS is configured in `config/cors.php`:
- Allowed origins
- Allowed methods
- Allowed headers
- Credentials support

## Rate Limiting

Rate limiting configured per route group:
- Auth routes: Strict (5/min)
- API routes: Standard
- Public routes: Lenient

## Route Testing

Routes are tested with:
- Feature tests
- Integration tests
- E2E tests

## Conclusion

API routes provide a clean, RESTful interface for all system operations. Routes are:
- Well-organized by role
- Properly secured
- Consistently formatted
- Fully documented

