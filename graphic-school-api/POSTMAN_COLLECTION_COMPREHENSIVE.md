# 📮 Graphic School API - Comprehensive Postman Collection Guide

## 🎯 Overview
This document provides a comprehensive guide for testing the Graphic School API using Postman. The collection is organized by modules and includes all endpoints with examples, edge cases, and testing scenarios.

## 📦 Import Instructions

1. Open Postman
2. Click **Import** button
3. Select `postman_collection.json` file
4. Update Collection Variables:
   - `base_url`: `http://graphic-school.test/api` (or your local URL)
   - `auth_token`: Will be auto-populated after login

## 🔐 Authentication Flow

### 1. Register (Public)
```
POST {{base_url}}/register
Body:
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "1234567890",
  "address": "Test Address"
}
```

### 2. Login (Public)
```
POST {{base_url}}/login
Body:
{
  "email": "test@example.com",
  "password": "password123"
}
```

**Auto-save token**: Use Postman's Test script to save token:
```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.collectionVariables.set("auth_token", jsonData.data.token);
    }
}
```

### 3. Logout (Authenticated)
```
POST {{base_url}}/logout
Headers:
  Authorization: Bearer {{auth_token}}
```

## 📋 API Endpoints by Module

### 🌐 Public Endpoints (No Auth Required)

#### Home & Summary
- `GET /home` - Homepage summary
- `GET /courses` - List all published courses
- `GET /courses/:id` - Course details
- `GET /categories` - List categories
- `GET /instructors` - List instructors
- `GET /instructors/:id` - Instructor details
- `GET /settings` - Public settings
- `GET /sliders` - Homepage sliders
- `GET /testimonials` - Testimonials
- `POST /contact` - Send contact message

#### Localization
- `GET /locale` - Get current locale
- `GET /locales` - Get available locales
- `POST /locale/:locale` - Set locale
- `GET /translations` - Get translations
- `GET /translations/:group` - Get translation group

### 👤 Student Endpoints (Role: student)

#### Courses
- `GET /student/courses` - My enrolled courses
- `POST /student/courses/:id/enroll` - Enroll in course
- `GET /student/courses/:id/sessions` - Course sessions
- `GET /student/courses/:id/attendance` - Course attendance
- `POST /student/courses/:id/review` - Review course

#### Profile
- `GET /student/profile` - Get profile
- `POST /student/profile` - Update profile

#### Sessions
- `GET /student/sessions` - My sessions

### 👨‍🏫 Instructor Endpoints (Role: instructor)

#### Courses
- `GET /instructor/courses` - My courses
- `GET /instructor/courses/:id/sessions` - Course sessions

#### Sessions
- `GET /instructor/sessions` - My sessions
- `POST /instructor/sessions/:id/note` - Add session note

#### Attendance
- `POST /instructor/attendance` - Store attendance
- `GET /instructor/attendance/:session` - Session attendance

### 🔧 Admin Endpoints (Role: admin)

#### Dashboard
- `GET /admin/dashboard` - Dashboard statistics

#### Users Management
- `GET /admin/users` - List users (with pagination, search, filters)
- `POST /admin/users` - Create user
- `GET /admin/users/:id` - Get user
- `PUT /admin/users/:id` - Update user
- `DELETE /admin/users/:id` - Delete user

#### Roles Management
- `GET /admin/roles` - List roles
- `POST /admin/roles` - Create role
- `GET /admin/roles/:id` - Get role
- `PUT /admin/roles/:id` - Update role
- `DELETE /admin/roles/:id` - Delete role

#### Categories Management
- `GET /admin/categories` - List categories
- `POST /admin/categories` - Create category
- `GET /admin/categories/:id` - Get category
- `PUT /admin/categories/:id` - Update category
- `DELETE /admin/categories/:id` - Delete category

**Category with translations**:
```json
{
  "translations": {
    "en": "Web Design",
    "ar": "تصميم المواقع"
  },
  "is_active": true
}
```

#### Courses Management
- `GET /admin/courses` - List courses (with pagination, search, filters)
- `POST /admin/courses` - Create course
- `GET /admin/courses/:id` - Get course
- `PUT /admin/courses/:id` - Update course
- `DELETE /admin/courses/:id` - Delete course
- `POST /admin/courses/:id/assign-instructors` - Assign instructors
- `POST /admin/courses/:id/sessions/generate` - Generate sessions

**Create Course Example**:
```json
{
  "title": "Advanced Web Design",
  "code": "AWD001",
  "category_id": 1,
  "description": "Learn advanced web design techniques",
  "price": 1500,
  "session_count": 12,
  "days_of_week": ["mon", "wed", "fri"],
  "start_date": "2025-02-01",
  "default_start_time": "10:00",
  "default_end_time": "12:00",
  "delivery_type": "online",
  "auto_generate_sessions": true,
  "max_students": 30
}
```

#### Sessions Management
- `GET /admin/sessions` - List sessions
- `GET /admin/sessions/:id` - Get session
- `PUT /admin/sessions/:id` - Update session
- `DELETE /admin/sessions/:id` - Delete session

#### Enrollments Management
- `GET /admin/enrollments` - List enrollments
- `POST /admin/enrollments` - Create enrollment
- `PUT /admin/enrollments/:id` - Update enrollment

#### Attendance Management
- `GET /admin/attendance` - List attendance records

#### Settings Management
- `GET /admin/settings` - Get settings
- `POST /admin/settings` - Update settings

#### Contacts Management
- `GET /admin/contacts` - List contact messages
- `POST /admin/contacts/:id/resolve` - Resolve contact message

#### Testimonials Management
- `GET /admin/testimonials` - List testimonials
- `PUT /admin/testimonials/:id` - Update testimonial
- `DELETE /admin/testimonials/:id` - Delete testimonial

#### Translations Management
- `GET /admin/translations` - List translations
- `POST /admin/translations` - Create translation
- `GET /admin/translations/:id` - Get translation
- `PUT /admin/translations/:id` - Update translation
- `DELETE /admin/translations/:id` - Delete translation
- `GET /admin/translations/groups` - Get translation groups
- `GET /admin/translations/locales` - Get locales
- `POST /admin/translations/clear-cache` - Clear translation cache

#### Reports
- `GET /admin/reports/courses` - Courses report
- `GET /admin/reports/instructors` - Instructors report
- `GET /admin/reports/financial` - Financial report

#### Strategic Reports
- `GET /admin/reports/strategic/performance` - Performance report
- `GET /admin/reports/strategic/profitability` - Profitability report
- `GET /admin/reports/strategic/student-analytics` - Student analytics
- `GET /admin/reports/strategic/instructor-performance` - Instructor performance
- `GET /admin/reports/strategic/forecasting` - Forecasting report

**Strategic Reports with Filters**:
```
GET /admin/reports/strategic/performance?start_date=2025-01-01&end_date=2025-12-31&category_id=1&course_id=1&period=monthly
```

### 🛠️ System Endpoints

#### Health Check
- `GET /health` - System health check

#### File Upload
- `POST /files/upload` - Upload file (multipart/form-data)
  - `file`: File to upload
  - `disk`: Storage disk (default: public)
  - `directory`: Directory path (default: uploads)

#### Export Data
- `POST /export` - Export data to Excel/PDF/CSV
```json
{
  "type": "excel",
  "data": [
    {"name": "John", "email": "john@example.com"}
  ],
  "headings": ["Name", "Email"],
  "file_name": "export"
}
```

## 🧪 Testing Scenarios

### 1. Authentication Tests
- ✅ Register with valid data
- ✅ Register with invalid email
- ✅ Register with weak password
- ✅ Register with mismatched passwords
- ✅ Login with valid credentials
- ✅ Login with invalid credentials
- ✅ Login with nonexistent user
- ✅ Logout with valid token
- ✅ Logout without token
- ✅ Logout with invalid token

### 2. Authorization Tests
- ✅ Student cannot access admin routes
- ✅ Instructor cannot access admin routes
- ✅ Admin can access admin routes
- ✅ Student can access student routes

### 3. CRUD Operations Tests
- ✅ Create, Read, Update, Delete for all resources
- ✅ Pagination and filtering
- ✅ Search functionality
- ✅ Sorting

### 4. Edge Cases Tests
- ✅ SQL injection attempts
- ✅ XSS attempts
- ✅ Rate limiting
- ✅ Pagination limits
- ✅ Invalid JSON payloads
- ✅ Large dataset performance

### 5. Validation Tests
- ✅ Required fields
- ✅ Email format
- ✅ Password strength
- ✅ Unique constraints
- ✅ Foreign key constraints

## 📊 Response Format

All API responses follow a unified format:

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  },
  "meta": {
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7
    }
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "status": 422
}
```

## 🔍 Query Parameters

### Pagination
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)

### Filtering
- `search`: Search term
- `status`: Filter by status
- `category_id`: Filter by category
- `role_id`: Filter by role
- `course_id`: Filter by course

### Sorting
- `sort_by`: Field to sort by
- `sort_order`: `asc` or `desc` (default: `asc`)

### Date Range (for Reports)
- `start_date`: Start date (YYYY-MM-DD)
- `end_date`: End date (YYYY-MM-DD)
- `period`: `daily`, `weekly`, `monthly`, `yearly`

## 🚨 Common Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Server Error

## 💡 Tips for Testing

1. **Use Environment Variables**: Create different environments for dev, staging, production
2. **Save Responses**: Use Postman's "Save Response" feature to create examples
3. **Use Pre-request Scripts**: Auto-generate test data
4. **Use Test Scripts**: Validate responses and save tokens automatically
5. **Use Collections**: Organize requests by feature/module
6. **Use Variables**: Store reusable values like IDs, tokens, etc.

## 📝 Example Test Scripts

### Auto-save Token After Login
```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.collectionVariables.set("auth_token", jsonData.data.token);
        console.log("Token saved successfully");
    }
}
```

### Validate Response Structure
```javascript
pm.test("Response has correct structure", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('success');
    pm.expect(jsonData).to.have.property('data');
});
```

### Check Pagination
```javascript
pm.test("Pagination metadata exists", function () {
    const jsonData = pm.response.json();
    if (jsonData.meta && jsonData.meta.pagination) {
        pm.expect(jsonData.meta.pagination).to.have.property('current_page');
        pm.expect(jsonData.meta.pagination).to.have.property('total');
    }
});
```

## 🎯 Next Steps

1. Import the Postman collection
2. Update base_url variable
3. Run authentication flow
4. Test all endpoints systematically
5. Create test scenarios for edge cases
6. Document any issues found

---

**Last Updated**: 2025-11-21
**Version**: 2.0.0

