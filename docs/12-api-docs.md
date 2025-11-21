# 📡 API Documentation - Graphic School

## نظرة عامة

النظام يستخدم **RESTful API** مع **Laravel Sanctum** للمصادقة. جميع الردود تتبع تنسيق موحد.

---

## Base URL

```
http://graphic-school.test/api
```

أو في Production:
```
https://your-domain.com/api
```

---

## Authentication

### طريقة المصادقة:
**Bearer Token** (Laravel Sanctum)

### الحصول على Token:
1. تسجيل الدخول: `POST /api/login`
2. التسجيل: `POST /api/register`

### استخدام Token:
```http
Authorization: Bearer {token}
```

---

## Response Format

### Success Response:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  },
  "status": 200,
  "meta": {
    // Pagination or additional metadata
  }
}
```

### Error Response:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Error message"]
  },
  "status": 422
}
```

---

## Public Endpoints (No Auth Required)

### 1. Register
```http
POST /api/register
```

**Body**:
```json
{
  "name": "Ahmed Mohamed",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "01234567890",
  "address": "Cairo, Egypt"
}
```

**Response**: `201 Created`
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com",
      "role": "student"
    },
    "token": "1|xxxxxxxxxxxx"
  }
}
```

---

### 2. Login
```http
POST /api/login
```

**Body**:
```json
{
  "email": "ahmed@example.com",
  "password": "password123"
}
```

**Response**: `200 OK`
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com",
      "role": "student"
    },
    "token": "1|xxxxxxxxxxxx"
  }
}
```

---

### 3. Get Courses (Public)
```http
GET /api/courses
```

**Query Parameters**:
- `page`: رقم الصفحة (default: 1)
- `per_page`: عدد النتائج (default: 15)
- `search`: البحث في العنوان
- `category_id`: تصفية حسب التصنيف
- `delivery_type`: تصفية حسب نوع التسليم (on-site, online, hybrid)

**Response**: `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Advanced Web Design",
      "code": "AWD001",
      "category": {
        "id": 1,
        "name": "Web Design"
      },
      "price": 1500,
      "image_path": "/storage/courses/image.jpg",
      "rating": 4.5,
      "students_count": 25
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 50
  }
}
```

---

### 4. Get Course Details
```http
GET /api/courses/{id}
```

**Response**: `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Advanced Web Design",
    "description": "Full course description...",
    "price": 1500,
    "start_date": "2025-02-01",
    "session_count": 12,
    "delivery_type": "online",
    "instructors": [
      {
        "id": 2,
        "name": "Sara Ahmed",
        "bio": "Expert in web design"
      }
    ],
    "reviews": [
      {
        "id": 1,
        "student": {
          "name": "Mahmoud Ali"
        },
        "rating_course": 5,
        "comment": "Great course!"
      }
    ]
  }
}
```

---

## Authenticated Endpoints

### Student Endpoints

#### 1. My Courses
```http
GET /api/student/courses
Headers: Authorization: Bearer {token}
```

**Response**: `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Advanced Web Design",
      "progress": 65,
      "enrollment": {
        "status": "approved",
        "payment_status": "paid"
      }
    }
  ]
}
```

---

#### 2. Enroll in Course
```http
POST /api/student/courses/{courseId}/enroll
Headers: Authorization: Bearer {token}
```

**Response**: `201 Created`
```json
{
  "success": true,
  "message": "Enrolled successfully",
  "data": {
    "id": 1,
    "status": "pending",
    "course": {
      "id": 1,
      "title": "Advanced Web Design"
    }
  }
}
```

---

#### 3. Get Course Sessions
```http
GET /api/student/courses/{courseId}/sessions
Headers: Authorization: Bearer {token}
```

**Response**: `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Session 1: Introduction",
      "session_date": "2025-02-01",
      "start_time": "10:00",
      "end_time": "12:00",
      "status": "scheduled"
    }
  ]
}
```

---

#### 4. Get Course Attendance
```http
GET /api/student/courses/{courseId}/attendance
Headers: Authorization: Bearer {token}
```

**Response**: `200 OK`
```json
{
  "success": true,
  "data": [
    {
      "session": {
        "id": 1,
        "title": "Session 1"
      },
      "status": "present",
      "attended_at": "2025-02-01 10:00:00"
    }
  ]
}
```

---

#### 5. Review Course
```http
POST /api/student/courses/{courseId}/review
Headers: Authorization: Bearer {token}
```

**Body**:
```json
{
  "rating_course": 5,
  "rating_instructor": 4,
  "comment": "Great course!",
  "instructor_id": 2
}
```

**Response**: `201 Created`

---

### Instructor Endpoints

#### 1. My Courses
```http
GET /api/instructor/courses
Headers: Authorization: Bearer {token}
```

**Response**: `200 OK`

---

#### 2. Store Attendance
```http
POST /api/instructor/attendance
Headers: Authorization: Bearer {token}
```

**Body**:
```json
{
  "session_id": 1,
  "attendance": [
    {
      "student_id": 1,
      "status": "present"
    },
    {
      "student_id": 2,
      "status": "absent"
    }
  ]
}
```

**Response**: `201 Created`

---

#### 3. Add Session Note
```http
POST /api/instructor/sessions/{sessionId}/note
Headers: Authorization: Bearer {token}
```

**Body**:
```json
{
  "note": "Session notes here..."
}
```

**Response**: `200 OK`

---

### Admin Endpoints

#### 1. Dashboard
```http
GET /api/admin/dashboard
Headers: Authorization: Bearer {token}
```

**Response**: `200 OK`
```json
{
  "success": true,
  "data": {
    "stats": {
      "total_students": 150,
      "total_courses": 25,
      "total_sessions": 300,
      "total_revenue": 50000
    }
  }
}
```

---

#### 2. List Users
```http
GET /api/admin/users
Headers: Authorization: Bearer {token}
```

**Query Parameters**:
- `page`, `per_page`, `search`, `role_id`, `is_active`

**Response**: `200 OK`

---

#### 3. Create User
```http
POST /api/admin/users
Headers: Authorization: Bearer {token}
```

**Body**:
```json
{
  "name": "New User",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role_id": 3,
  "phone": "01234567890"
}
```

**Response**: `201 Created`

---

#### 4. List Courses
```http
GET /api/admin/courses
Headers: Authorization: Bearer {token}
```

**Query Parameters**:
- `page`, `per_page`, `search`, `category_id`, `status`, `is_published`

**Response**: `200 OK`

---

#### 5. Create Course
```http
POST /api/admin/courses
Headers: Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body** (Form Data):
```
title: Advanced Web Design
code: AWD001
category_id: 1
description: Course description
price: 1500
start_date: 2025-02-01
session_count: 12
days_of_week: ["mon", "wed", "fri"]
default_start_time: 10:00
default_end_time: 12:00
delivery_type: online
max_students: 30
auto_generate_sessions: true
image: (file)
```

**Response**: `201 Created`

---

#### 6. Assign Instructors
```http
POST /api/admin/courses/{courseId}/assign-instructors
Headers: Authorization: Bearer {token}
```

**Body**:
```json
{
  "instructors": [
    {
      "instructor_id": 2,
      "is_supervisor": true
    },
    {
      "instructor_id": 3,
      "is_supervisor": false
    }
  ]
}
```

**Response**: `200 OK`

---

#### 7. Generate Sessions
```http
POST /api/admin/courses/{courseId}/sessions/generate
Headers: Authorization: Bearer {token}
```

**Response**: `201 Created`
```json
{
  "success": true,
  "message": "Sessions generated successfully",
  "data": {
    "sessions_created": 12
  }
}
```

---

#### 8. List Enrollments
```http
GET /api/admin/enrollments
Headers: Authorization: Bearer {token}
```

**Query Parameters**:
- `page`, `per_page`, `status`, `payment_status`, `course_id`, `student_id`

**Response**: `200 OK`

---

#### 9. Update Enrollment
```http
PUT /api/admin/enrollments/{enrollmentId}
Headers: Authorization: Bearer {token}
```

**Body**:
```json
{
  "status": "approved",
  "payment_status": "paid",
  "paid_amount": 1500,
  "can_attend": true,
  "note": "Approved by admin"
}
```

**Response**: `200 OK`

---

#### 10. Reports

##### Courses Report
```http
GET /api/admin/reports/courses?start_date=2025-01-01&end_date=2025-12-31
Headers: Authorization: Bearer {token}
```

##### Instructors Report
```http
GET /api/admin/reports/instructors?start_date=2025-01-01&end_date=2025-12-31
Headers: Authorization: Bearer {token}
```

##### Financial Report
```http
GET /api/admin/reports/financial?start_date=2025-01-01&end_date=2025-12-31
Headers: Authorization: Bearer {token}
```

##### Strategic Reports
```http
GET /api/admin/reports/strategic/performance?start_date=2025-01-01&end_date=2025-12-31&period=monthly
GET /api/admin/reports/strategic/profitability?start_date=2025-01-01&end_date=2025-12-31
GET /api/admin/reports/strategic/student-analytics
GET /api/admin/reports/strategic/instructor-performance
GET /api/admin/reports/strategic/forecasting
Headers: Authorization: Bearer {token}
```

---

## Error Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Authentication required or invalid token |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Validation Error | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Server Error | Internal server error |

---

## Pagination

جميع الردود التي تعيد قوائم تدعم Pagination:

**Query Parameters**:
- `page`: رقم الصفحة (default: 1)
- `per_page`: عدد النتائج (default: 15, max: 100)

**Response Meta**:
```json
{
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

---

## Filtering & Sorting

### Filtering:
```http
GET /api/admin/courses?filters[status]=running&filters[category_id]=1
```

### Sorting:
```http
GET /api/admin/courses?sort_by=created_at&sort_order=desc
```

### Search:
```http
GET /api/admin/courses?search=web design
```

---

## File Upload

### Upload File
```http
POST /api/files/upload
Headers: 
  Authorization: Bearer {token}
  Content-Type: multipart/form-data
```

**Body** (Form Data):
```
file: (file)
disk: public
directory: uploads
```

**Response**: `200 OK`
```json
{
  "success": true,
  "data": {
    "path": "/storage/uploads/file.jpg",
    "url": "http://graphic-school.test/storage/uploads/file.jpg"
  }
}
```

---

## Health Check

```http
GET /api/health
```

**Response**: `200 OK`
```json
{
  "status": "healthy",
  "database": "connected",
  "timestamp": "2025-11-21T12:00:00Z"
}
```

---

## Postman Collection

يوجد ملف Postman Collection كامل في:
- `graphic-school-api/postman_collection.json`
- `graphic-school-api/POSTMAN_COLLECTION_COMPREHENSIVE.md`

---

---

## CHANGE-003: In-App Notifications API

### GET /api/notifications
Get all notifications for authenticated user

**Query Parameters:**
- `read` (boolean): Filter by read/unread
- `type` (string): Filter by type
- `category` (string): Filter by category
- `per_page` (integer): Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "type": "enrollment_approved",
        "category": "success",
        "title": "تم قبول طلب التسجيل",
        "message": "تم قبول طلب التسجيل في الكورس: ...",
        "read_at": null,
        "created_at": "2025-11-21T10:00:00Z"
      }
    ]
  }
}
```

### GET /api/notifications/unread-count
Get unread notifications count

**Response:**
```json
{
  "success": true,
  "data": {
    "count": 5
  }
}
```

### PUT /api/notifications/{id}/read
Mark notification as read

### PUT /api/notifications/read-all
Mark all notifications as read

### DELETE /api/notifications/{id}
Delete notification

---

## CHANGE-004: Payment Timeline API

### GET /api/student/payments
Get payments for authenticated student

**Query Parameters:**
- `course_id` (integer): Filter by course
- `enrollment_id` (integer): Filter by enrollment
- `per_page` (integer): Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "amount": 500.00,
        "remaining_amount": 500.00,
        "payment_date": "2025-11-21",
        "status": "completed"
      }
    ],
    "meta": {
      "totals": {
        "total_paid": 1000.00,
        "total_remaining": 500.00
      }
    }
  }
}
```

### GET /api/admin/payments
Get all payments (Admin)

**Query Parameters:**
- `student_id` (integer): Filter by student
- `course_id` (integer): Filter by course
- `status` (string): Filter by status
- `from_date` (date): Start date
- `to_date` (date): End date

### POST /api/admin/payments
Create payment (Admin)

**Body:**
```json
{
  "enrollment_id": 1,
  "amount": 500.00,
  "payment_method": "cash",
  "payment_date": "2025-11-21",
  "status": "completed"
}
```

### PUT /api/admin/payments/{id}
Update payment (Admin)

### GET /api/admin/payments/reports
Get payment reports (Admin)

---

## CHANGE-005: Messaging API

### GET /api/messaging/conversations
Get all conversations for authenticated user

**Query Parameters:**
- `course_id` (integer): Filter by course
- `archived` (boolean): Filter archived conversations
- `per_page` (integer): Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "student_id": 1,
        "instructor_id": 2,
        "course_id": 1,
        "subject": "Question about assignment",
        "unread_count": 2,
        "last_message_at": "2025-11-21T10:00:00Z"
      }
    ]
  }
}
```

### POST /api/messaging/conversations
Get or create conversation

**Body:**
```json
{
  "course_id": 1,
  "instructor_id": 2,
  "session_id": 1,
  "subject": "Question"
}
```

### GET /api/messaging/conversations/{id}/messages
Get messages for a conversation

**Query Parameters:**
- `per_page` (integer): Items per page

### POST /api/messaging/messages
Send a message

**Body:**
```json
{
  "conversation_id": 1,
  "message": "Hello instructor!",
  "attachments": ["file1.pdf"]
}
```

### PUT /api/messaging/conversations/{id}/archive
Archive conversation

---

## CHANGE-002: CMS API

### Pages

#### GET /api/pages/{slug}
Get page by slug (Public)

#### GET /api/admin/pages
Get all pages (Admin)

#### POST /api/admin/pages
Create page (Admin)

**Body:**
```json
{
  "slug": "about",
  "title": "About Us",
  "content": "<p>Content here</p>",
  "sections": {"hero": true, "features": false},
  "seo": {
    "title": "About Us",
    "description": "About our school"
  },
  "is_active": true
}
```

#### PUT /api/admin/pages/{id}
Update page (Admin)

#### DELETE /api/admin/pages/{id}
Delete page (Admin)

### FAQ

#### GET /api/faqs
Get all active FAQs (Public)

**Query Parameters:**
- `category` (string): Filter by category

#### GET /api/admin/faqs
Get all FAQs (Admin)

#### POST /api/admin/faqs
Create FAQ (Admin)

**Body:**
```json
{
  "question": "What is this?",
  "answer": "This is an answer",
  "category": "general",
  "is_active": true
}
```

#### PUT /api/admin/faqs/{id}
Update FAQ (Admin)

#### DELETE /api/admin/faqs/{id}
Delete FAQ (Admin)

### Media

#### GET /api/admin/media
Get all media (Admin)

**Query Parameters:**
- `type` (string): Filter by type
- `images_only` (boolean): Images only
- `search` (string): Search by name

#### POST /api/admin/media
Upload media (Admin)

**Body:** FormData
- `file` (file): File to upload
- `alt_text` (string): Alt text
- `description` (string): Description

#### PUT /api/admin/media/{id}
Update media (Admin)

#### DELETE /api/admin/media/{id}
Delete media (Admin)

---

## CHANGE-006: Ticketing System API

### GET /api/admin/tickets
Get all tickets (Admin)

**Query Parameters:**
- `status` (string): Filter by status
- `type` (string): Filter by type (bug, change_request, new_feature)
- `priority` (string): Filter by priority
- `user_id` (integer): Filter by user

### POST /api/admin/tickets
Create ticket (Admin)

**Body:**
```json
{
  "type": "bug",
  "title": "Bug Title",
  "description": "Bug description",
  "priority": "high"
}
```

### GET /api/admin/tickets/{id}
Get ticket details (Admin)

### PUT /api/admin/tickets/{id}
Update ticket (Admin)

**Body:**
```json
{
  "status": "in_progress",
  "priority": "urgent",
  "assigned_to": 2
}
```

### POST /api/admin/tickets/{id}/attachments
Upload attachment to ticket (Admin)

**Body:** FormData
- `file` (file): File to upload

### GET /api/admin/tickets/reports
Get ticket reports (Admin)

---

## CHANGE-007: Advanced Reports API

### GET /api/admin/reports/advanced/top-students/grades
Get top students by grades

**Query Parameters:**
- `course_id` (integer): Filter by course
- `limit` (integer): Number of results (default: 10)

### GET /api/admin/reports/advanced/top-students/attendance
Get top students by attendance

### GET /api/admin/reports/advanced/top-students/engagement
Get top students by engagement

### GET /api/admin/reports/advanced/average-grades/course
Get average grades by course

### GET /api/admin/reports/advanced/average-grades/batch
Get average grades by batch

### GET /api/admin/reports/advanced/average-grades/instructor
Get average grades by instructor

### GET /api/admin/reports/advanced/attendance-rate/course
Get attendance rate by course

### GET /api/admin/reports/advanced/attendance-rate/student
Get attendance rate by student

### GET /api/admin/reports/advanced/engagement-quality
Get engagement quality metrics

### GET /api/instructor/reports/performance
Get instructor performance (Instructor)

### GET /api/admin/reports/advanced/instructor-performance/{instructorId}
Get instructor performance by ID (Admin)

---

## CHANGE-008: Audit Log API

### GET /api/admin/audit-logs
Get all audit logs (Admin)

**Query Parameters:**
- `user_id` (integer): Filter by user
- `action` (string): Filter by action (created, updated, deleted)
- `model_type` (string): Filter by model type
- `model_id` (integer): Filter by model ID
- `from_date` (date): Start date
- `to_date` (date): End date
- `per_page` (integer): Items per page

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "action": "created",
        "model_type": "Modules\\LMS\\Courses\\Models\\Course",
        "model_id": 1,
        "user_id": 1,
        "old_values": null,
        "new_values": {"title": "New Course"},
        "description": "Created Course: 1",
        "created_at": "2025-11-21T10:00:00Z"
      }
    ]
  }
}
```

### GET /api/admin/audit-logs/{id}
Get audit log details (Admin)

### GET /api/admin/audit-logs/entity/{modelType}/{modelId}
Get audit logs for a specific entity (Admin)

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 2.0.0

