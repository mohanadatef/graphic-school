# API Routes

## Overview

All API routes are defined in `routes/api.php`. Routes are organized by authentication level and user role. All responses follow a unified API response format.

**Base URL:** `/api`  
**Authentication:** Laravel Sanctum (Token-based)  
**Response Format:** JSON

## Unified API Response Format

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
    "errors": { ... }
}
```

## Route Organization

### Public Routes (No Authentication)
- Setup wizard
- Public content
- Authentication
- Language/locale
- Certificate verification

### Authenticated Routes
- Student routes (`/api/student/*`)
- Instructor routes (`/api/instructor/*`)
- Admin routes (`/api/admin/*`)

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

**Example Request:**
```http
POST /api/setup/step/general
Content-Type: application/json

{
    "academy_name": "Graphic School",
    "country_id": 1,
    "timezone": "Africa/Cairo"
}
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
GET    /api/public/pages/{slug}
GET    /api/public/courses
GET    /api/public/courses/{course}
GET    /api/public/instructors
GET    /api/public/instructors/{instructor}
POST   /api/contact
POST   /api/public/contact
POST   /api/enroll
GET    /api/certificates/verify/{code}
GET    /api/branding/frontend
```

**Example: Get Public Courses**
```http
GET /api/courses?page=1&per_page=12&category_id=1
```

**Example Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Graphic Design Fundamentals",
            "description": "...",
            "price": "5000.00",
            "currency": "EGP",
            "image_path": "/storage/courses/1.jpg",
            "category": { ... },
            "instructors": [ ... ]
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 12,
            "total": 60
        }
    }
}
```

**Example: Public Enrollment**
```http
POST /api/enroll
Content-Type: application/json

{
    "course_id": 1,
    "name": "Ahmed Mohamed",
    "email": "ahmed@example.com",
    "phone": "+201234567890",
    "group_id": null
}
```

**Example: Certificate Verification**
```http
GET /api/certificates/verify/abc123def456ghi789
```

### Authentication

```
POST   /api/register
POST   /api/login
```

**Example: Login**
```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": { ... },
        "token": "1|abc123def456..."
    }
}
```

### Localization

```
GET    /api/locale
GET    /api/locales
POST   /api/locale/{locale}
GET    /api/translations
GET    /api/translations/{group}
```

## Student Routes (Auth Required)

**Base Path:** `/api/student`  
**Middleware:** `auth:api`, `role:student`

### Dashboard

```
GET    /api/student/my-courses
GET    /api/student/my-group
GET    /api/student/my-sessions
GET    /api/student/attendance-history
GET    /api/student/profile
POST   /api/logout
```

**Example: Get My Courses**
```http
GET /api/student/my-courses
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Graphic Design Fundamentals",
            "enrollment": {
                "status": "approved",
                "group": {
                    "id": 1,
                    "name": "Group A - Morning"
                }
            }
        }
    ]
}
```

### Enrollment

```
POST   /api/student/enroll
GET    /api/student/enrollments
```

**Example: Enroll in Course**
```http
POST /api/student/enroll
Authorization: Bearer {token}
Content-Type: application/json

{
    "course_id": 1,
    "group_id": null
}
```

### Attendance

```
GET    /api/student/attendance
```

### Certificates

```
GET    /api/student/certificates
GET    /api/student/certificates/{id}
```

**Example: Get My Certificates**
```http
GET /api/student/certificates
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "course": { ... },
            "group": { ... },
            "certificate_number": "CERT-ABC123-2025",
            "verification_code": "abc123def456",
            "issued_date": "2025-01-15"
        }
    ]
}
```

## Instructor Routes (Auth Required)

**Base Path:** `/api/instructor`  
**Middleware:** `auth:api`, `role:instructor`

### Groups & Sessions

```
GET    /api/instructor/my-groups
GET    /api/instructor/groups/{groupId}/sessions
GET    /api/instructor/groups/{groupId}/students
GET    /api/instructor/sessions
```

**Example: Get My Groups**
```http
GET /api/instructor/my-groups
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Group A - Morning",
            "course": { ... },
            "start_date": "2025-02-01",
            "end_date": "2025-06-30",
            "sessions_count": 24,
            "students_count": 15
        }
    ]
}
```

### Attendance

```
GET    /api/instructor/sessions/{sessionId}/attendance
POST   /api/instructor/sessions/{sessionId}/attendance
POST   /api/instructor/sessions/{sessionId}/attendance/update
```

**Example: Get Session Attendance**
```http
GET /api/instructor/sessions/123/attendance
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "session": { ... },
        "attendance": [
            {
                "id": 1,
                "student": { ... },
                "status": "absent",
                "marked_at": null
            }
        ]
    }
}
```

**Example: Take Attendance**
```http
POST /api/instructor/sessions/123/attendance/update
Authorization: Bearer {token}
Content-Type: application/json

{
    "attendance": [
        {
            "student_id": 1,
            "status": "present"
        },
        {
            "student_id": 2,
            "status": "absent"
        },
        {
            "student_id": 3,
            "status": "late"
        }
    ]
}
```

## Admin Routes (Auth Required)

**Base Path:** `/api/admin`  
**Middleware:** `auth:api`, `role:admin`

### Dashboard & Setup

```
GET    /api/admin/dashboard
GET    /api/admin/setup/status
POST   /api/admin/setup/save-step/{step}
POST   /api/admin/setup/complete
POST   /api/admin/setup/reset
POST   /api/admin/setup/test-email
```

### System Settings

```
GET    /api/admin/system-settings
GET    /api/admin/system-settings/public
PUT    /api/admin/system-settings
GET    /api/admin/settings
POST   /api/admin/settings
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

### Categories

```
GET    /api/admin/categories
POST   /api/admin/categories
GET    /api/admin/categories/{id}
PUT    /api/admin/categories/{id}
DELETE /api/admin/categories/{id}
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

**Example: Create Course**
```http
POST /api/admin/courses
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Graphic Design Fundamentals",
    "description": "...",
    "price": "5000.00",
    "currency_id": 1,
    "language_id": 1,
    "category_id": 1,
    "image": {file}
}
```

**Example: Assign Instructors**
```http
POST /api/admin/courses/1/assign-instructors
Authorization: Bearer {token}
Content-Type: application/json

{
    "instructor_ids": [1, 2, 3]
}
```

### Groups

```
GET    /api/admin/groups
POST   /api/admin/groups
GET    /api/admin/groups/{id}
PUT    /api/admin/groups/{id}
DELETE /api/admin/groups/{id}
```

**Example: Create Group**
```http
POST /api/admin/groups
Authorization: Bearer {token}
Content-Type: application/json

{
    "course_id": 1,
    "name": "Group A - Morning",
    "start_date": "2025-02-01",
    "end_date": "2025-06-30",
    "notes": "Morning batch",
    "capacity": 20,
    "instructor_ids": [1, 2]
}
```

### Sessions

```
GET    /api/admin/sessions
GET    /api/admin/sessions/{id}
PUT    /api/admin/sessions/{id}
DELETE /api/admin/sessions/{id}
```

**Example: Update Session**
```http
PUT /api/admin/sessions/123
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Introduction to Design",
    "session_date": "2025-02-01",
    "start_time": "10:00",
    "end_time": "12:00",
    "instructor_id": 1,
    "notes": "Bring laptops"
}
```

### Enrollments

```
GET    /api/admin/enrollments
POST   /api/admin/enrollments
PUT    /api/admin/enrollments/{enrollment}
POST   /api/admin/enrollments/{id}/approve
POST   /api/admin/enrollments/{id}/reject
POST   /api/admin/enrollments/{id}/withdraw
```

**Example: Approve Enrollment**
```http
POST /api/admin/enrollments/1/approve
Authorization: Bearer {token}
Content-Type: application/json

{
    "group_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "message": "Enrollment approved successfully",
    "data": {
        "id": 1,
        "student": { ... },
        "course": { ... },
        "group": { ... },
        "status": "approved",
        "can_attend": true,
        "approved_at": "2025-01-28T10:00:00Z"
    }
}
```

### Attendance

```
GET    /api/admin/attendance
```

### Certificates

```
GET    /api/admin/certificates
POST   /api/admin/certificates
GET    /api/admin/certificates/{id}
DELETE /api/admin/certificates/{id}
```

**Example: Issue Certificate**
```http
POST /api/admin/certificates
Authorization: Bearer {token}
Content-Type: application/json

{
    "student_id": 1,
    "course_id": 1,
    "group_id": 1,
    "instructor_id": 2
}
```

**Response:**
```json
{
    "success": true,
    "message": "Certificate issued successfully",
    "data": {
        "id": 1,
        "certificate_number": "CERT-ABC123-2025",
        "verification_code": "abc123def456ghi789",
        "qr_code": "/storage/certificates/qr/abc123def456ghi789.png",
        "issued_date": "2025-01-28"
    }
}
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

### Localization

#### Languages

```
GET    /api/admin/languages
POST   /api/admin/languages
GET    /api/admin/languages/{id}
PUT    /api/admin/languages/{id}
DELETE /api/admin/languages/{id}
GET    /api/admin/languages/active
```

#### Currencies

```
GET    /api/admin/currencies
POST   /api/admin/currencies
GET    /api/admin/currencies/{id}
PUT    /api/admin/currencies/{id}
DELETE /api/admin/currencies/{id}
GET    /api/admin/currencies/active
```

#### Countries

```
GET    /api/admin/countries
POST   /api/admin/countries
GET    /api/admin/countries/{id}
PUT    /api/admin/countries/{id}
DELETE /api/admin/countries/{id}
GET    /api/admin/countries/active
```

## Removed Routes

The following routes were removed as part of cleanup:

### Assignments
- ❌ All `/api/student/assignments/*` routes
- ❌ All `/api/instructor/assignments/*` routes

### QR Attendance
- ❌ `/api/qr/{token}`
- ❌ `/api/instructor/sessions/{sessionId}/qr-generate`
- ❌ `/api/student/qr-checkin`

### Subscriptions
- ❌ All subscription-related routes

### Payment Gateways
- ❌ All payment gateway routes

## Route Protection

### Authentication
- Protected routes require `Authorization: Bearer {token}` header
- Token obtained via `/api/login`
- Token validated on each request via Laravel Sanctum middleware

### Authorization
- Role-based middleware: `role:admin`, `role:instructor`, `role:student`
- Role checked after authentication
- Unauthorized requests return 403 Forbidden

### Rate Limiting
- Authentication endpoints: 5 attempts per minute
- API endpoints: Configurable rate limits

## API Versioning

Currently, no API versioning is implemented. Future versions can add `/api/v1/`, `/api/v2/` prefixes if needed.

## Error Handling

### HTTP Status Codes
- `200 OK` - Success
- `201 Created` - Resource created
- `400 Bad Request` - Validation error
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed
- `500 Internal Server Error` - Server error

### Error Response Format
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "course_id": ["The course id field is required."],
        "price": ["The price must be a number."]
    }
}
```

## Pagination

List endpoints support pagination:

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15)

**Response Meta:**
```json
{
    "data": [ ... ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 75,
            "from": 1,
            "to": 15
        }
    }
}
```

## Filtering

Many list endpoints support filtering via query parameters:

**Example:**
```http
GET /api/admin/enrollments?status=pending&course_id=1&page=1&per_page=20
```

**Common Filters:**
- `status` - Filter by status
- `course_id` - Filter by course
- `group_id` - Filter by group
- `search` - Text search
- `date_from` - Date range start
- `date_to` - Date range end

## Sorting

Sorting via query parameters (implementation varies):

**Example:**
```http
GET /api/admin/courses?sort=created_at&order=desc
```

---

**API Status:** ✅ All routes aligned with business specification

