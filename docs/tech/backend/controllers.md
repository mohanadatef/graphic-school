# Backend Controllers

## Overview

Controllers handle HTTP requests and coordinate between routes, services, and responses. They are located in `app/Http/Controllers/` and module `Http/Controllers/` directories.

## Controller Pattern

Controllers follow these principles:
- **Thin Controllers:** Minimal logic, delegate to services
- **Request Validation:** Use Form Requests
- **Consistent Responses:** Unified JSON response format
- **Error Handling:** Proper exception handling

## Controller Structure

### Base Controller
All controllers extend `App\Http\Controllers\Controller` which provides:
- Common helper methods
- Response formatting
- Error handling

### Response Format
All API responses follow this format:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... },
  "meta": { ... }
}
```

## Admin Controllers

### GroupController (`app/Http/Controllers/Admin/GroupController.php`)

**Purpose:** Manages course groups.

**Routes:**
- `GET /api/admin/groups` - List groups
- `GET /api/admin/groups/{id}` - Get group
- `POST /api/admin/groups` - Create group
- `PUT /api/admin/groups/{id}` - Update group
- `DELETE /api/admin/groups/{id}` - Delete group

**Key Methods:**
- `index(Request)` - List groups with filters
- `show(Group, Request)` - Get group details
- `store(Request)` - Create new group
- `update(Group, Request)` - Update group
- `destroy(Group)` - Delete group

**Business Logic:**
- Validates group capacity
- Checks instructor availability
- Manages student assignments
- Handles multi-language content

### EnrollmentController (`app/Http/Controllers/Admin/EnrollmentController.php`)

**Purpose:** Manages student enrollments.

**Routes:**
- `GET /api/admin/enrollments` - List enrollments
- `POST /api/admin/enrollments` - Create enrollment
- `PUT /api/admin/enrollments/{id}` - Update enrollment
- `POST /api/admin/enrollments/{id}/approve` - Approve enrollment
- `POST /api/admin/enrollments/{id}/reject` - Reject enrollment
- `POST /api/admin/enrollments/{id}/withdraw` - Withdraw enrollment

**Key Methods:**
- `index(Request)` - List enrollments
- `store(Request)` - Create enrollment
- `update(Enrollment, Request)` - Update enrollment
- `approve($id)` - Approve enrollment
- `reject($id, Request)` - Reject enrollment
- `withdraw($id)` - Withdraw enrollment

**Business Logic:**
- Enrollment workflow management
- Group assignment on approval
- Capacity checking
- Payment tracking

### AttendanceController (`app/Http/Controllers/Admin/AttendanceController.php`)

**Purpose:** Manages attendance overview.

**Routes:**
- `GET /api/admin/attendance` - Attendance overview

**Key Methods:**
- `index(Request)` - Get attendance overview with filters

**Business Logic:**
- Aggregates attendance data
- Provides reports
- Filters by course, group, date range

### LanguageController (`app/Http/Controllers/Admin/LanguageController.php`)

**Purpose:** Manages languages.

**Routes:**
- `GET /api/admin/languages` - List languages
- `POST /api/admin/languages` - Create language
- `PUT /api/admin/languages/{id}` - Update language
- `DELETE /api/admin/languages/{id}` - Delete language
- `GET /api/admin/languages/active` - Get active languages

**Key Methods:**
- `index()` - List all languages
- `store(Request)` - Create language
- `update(Language, Request)` - Update language
- `destroy(Language)` - Delete language
- `active()` - Get active languages

### CurrencyController (`app/Http/Controllers/Admin/CurrencyController.php`)

**Purpose:** Manages currencies.

**Routes:**
- `GET /api/admin/currencies` - List currencies
- `POST /api/admin/currencies` - Create currency
- `PUT /api/admin/currencies/{id}` - Update currency
- `DELETE /api/admin/currencies/{id}` - Delete currency
- `GET /api/admin/currencies/active` - Get active currencies

### CountryController (`app/Http/Controllers/Admin/CountryController.php`)

**Purpose:** Manages countries.

**Routes:**
- `GET /api/admin/countries` - List countries
- `POST /api/admin/countries` - Create country
- `PUT /api/admin/countries/{id}` - Update country
- `DELETE /api/admin/countries/{id}` - Delete country
- `GET /api/admin/countries/active` - Get active countries

### PageController (`app/Http/Controllers/Admin/PageController.php`)

**Purpose:** Manages CMS pages.

**Routes:**
- `GET /api/admin/pages` - List pages
- `POST /api/admin/pages` - Create page
- `PUT /api/admin/pages/{id}` - Update page
- `DELETE /api/admin/pages/{id}` - Delete page
- `GET /api/admin/pages/slug/{slug}` - Get page by slug
- `PUT /api/admin/pages/{id}/blocks` - Update page blocks

**Key Methods:**
- `index()` - List pages
- `store(Request)` - Create page
- `update(Page, Request)` - Update page
- `destroy(Page)` - Delete page
- `showBySlug($slug)` - Get page by slug
- `updateBlocks(Page, Request)` - Update page blocks

### SetupWizardController (`app/Http/Controllers/Admin/SetupWizardController.php`)

**Purpose:** Manages setup wizard.

**Routes:**
- `GET /api/admin/setup/status` - Get setup status
- `POST /api/admin/setup/save-step/{step}` - Save setup step
- `POST /api/admin/setup/activate-default` - Activate with defaults
- `POST /api/admin/setup/complete` - Complete setup
- `POST /api/admin/setup/reset` - Reset to default
- `POST /api/admin/setup/test-email` - Test email configuration

**Key Methods:**
- `status()` - Get setup status
- `saveStep($step, Request)` - Save setup step
- `activateDefault()` - Activate with defaults
- `complete(Request)` - Complete setup wizard
- `resetToDefault()` - Reset settings
- `testEmail(Request)` - Test email

## Instructor Controllers

### InstructorController (`app/Http/Controllers/Instructor/InstructorController.php`)

**Purpose:** Instructor dashboard operations.

**Routes:**
- `GET /api/instructor/my-groups` - Get instructor's groups
- `GET /api/instructor/groups/{groupId}/sessions` - Get group sessions
- `GET /api/instructor/groups/{groupId}/students` - Get group students
- `GET /api/instructor/sessions/{sessionId}/attendance` - Get session attendance
- `POST /api/instructor/sessions/{sessionId}/attendance` - Take attendance

**Key Methods:**
- `myGroups()` - Get assigned groups
- `groupSessions($groupId)` - Get group sessions
- `groupStudents($groupId)` - Get group students
- `sessionAttendance($sessionId)` - Get session attendance
- `takeAttendance($sessionId, Request)` - Mark attendance

### AttendanceController (`app/Http/Controllers/Instructor/AttendanceController.php`)

**Purpose:** Instructor attendance operations.

**Routes:**
- `GET /api/instructor/sessions` - Get instructor sessions
- `GET /api/instructor/sessions/{sessionId}/attendance` - Get attendance
- `POST /api/instructor/sessions/{sessionId}/attendance/update` - Update attendance

**Key Methods:**
- `sessions(Request)` - Get instructor sessions
- `attendance($sessionId)` - Get session attendance
- `updateAttendance($sessionId, Request)` - Update attendance

### QrAttendanceController (`app/Http/Controllers/Instructor/QrAttendanceController.php`)

**Purpose:** QR code attendance generation.

**Routes:**
- `POST /api/instructor/sessions/{sessionId}/qr-generate` - Generate QR code

**Key Methods:**
- `generateQr($sessionId)` - Generate QR code for session

## Student Controllers

### StudentController (`app/Http/Controllers/Student/StudentController.php`)

**Purpose:** Student dashboard operations.

**Routes:**
- `GET /api/student/my-courses` - Get student's courses
- `GET /api/student/my-group` - Get student's group
- `GET /api/student/my-sessions` - Get student's sessions
- `GET /api/student/attendance-history` - Get attendance history
- `GET /api/student/profile` - Get student profile

**Key Methods:**
- `myCourses()` - Get enrolled courses
- `myGroup()` - Get assigned group
- `mySessions()` - Get group sessions
- `attendanceHistory()` - Get attendance history
- `profile()` - Get student profile

### EnrollmentController (`app/Http/Controllers/Student/EnrollmentController.php`)

**Purpose:** Student enrollment requests.

**Routes:**
- `POST /api/student/enroll` - Submit enrollment request
- `GET /api/student/enrollments` - Get student enrollments

**Key Methods:**
- `enroll(Request)` - Submit enrollment
- `index()` - Get student enrollments

### AttendanceController (`app/Http/Controllers/Student/AttendanceController.php`)

**Purpose:** Student attendance viewing.

**Routes:**
- `GET /api/student/attendance` - Get student attendance

**Key Methods:**
- `index(Request)` - Get student attendance with filters

### QrAttendanceController (`app/Http/Controllers/Student/QrAttendanceController.php`)

**Purpose:** Student QR check-in.

**Routes:**
- `POST /api/student/qr-checkin` - Check in via QR code

**Key Methods:**
- `checkIn(Request)` - Process QR check-in

## Public Controllers

### PageController (`app/Http/Controllers/Public/PageController.php`)

**Purpose:** Public CMS page rendering.

**Routes:**
- `GET /api/public/pages/{slug}` - Get public page

**Key Methods:**
- `show($slug)` - Render public page

### EnrollmentController (`app/Http/Controllers/Public/EnrollmentController.php`)

**Purpose:** Public enrollment.

**Routes:**
- `POST /api/enroll` - Public enrollment request

**Key Methods:**
- `enroll(Request)` - Process public enrollment

### ContactController (`app/Http/Controllers/Public/ContactController.php`)

**Purpose:** Contact form handling.

**Routes:**
- `POST /api/public/contact` - Submit contact form

**Key Methods:**
- `send(Request)` - Process contact form

## System Controllers

### CalendarController (`app/Http/Controllers/CalendarController.php`)

**Purpose:** Calendar management.

**Routes:**
- `GET /api/calendar` - Get user calendar
- `POST /api/calendar` - Create calendar event
- `PUT /api/calendar/{id}` - Update event
- `DELETE /api/calendar/{id}` - Delete event

### HealthController (`app/Http/Controllers/HealthController.php`)

**Purpose:** System health checks.

**Routes:**
- `GET /api/health` - Health check

**Key Methods:**
- `check()` - Perform health check

## Controller Best Practices

### 1. Keep Controllers Thin
```php
public function store(Request $request)
{
    $validated = $request->validated();
    $group = $this->groupService->create($validated);
    return response()->json(['success' => true, 'data' => $group]);
}
```

### 2. Use Form Requests
```php
public function store(CreateGroupRequest $request)
{
    // Request already validated
    $group = $this->groupService->create($request->validated());
    return response()->json(['success' => true, 'data' => $group]);
}
```

### 3. Consistent Responses
```php
return response()->json([
    'success' => true,
    'message' => 'Group created successfully',
    'data' => $group,
]);
```

### 4. Error Handling
```php
try {
    $group = $this->groupService->create($data);
    return response()->json(['success' => true, 'data' => $group]);
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 400);
}
```

### 5. Resource Transformation
- Use API Resources for complex transformations
- Keep responses consistent
- Include relationships when needed

## Controller Testing

Controllers should be tested with:
- Feature tests for HTTP endpoints
- Mock services for isolation
- Test all response scenarios
- Test error cases

## Conclusion

Controllers are the entry point for HTTP requests. They:
- Validate requests
- Call services
- Format responses
- Handle errors
- Maintain consistency

