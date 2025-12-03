# Backend Services

## Overview

Services contain business logic and orchestrate domain operations. They are located in `app/Services/` and module `Services/` directories.

## Service Pattern

Services follow these principles:
- **Single Responsibility:** Each service handles one domain area
- **Dependency Injection:** Services are injected via constructor
- **Transaction Management:** Critical operations use database transactions
- **Error Handling:** Proper exception handling and logging

## Core Services

### EnrollmentService (`app/Services/EnrollmentService.php`)

**Purpose:** Manages student enrollment workflow.

**Key Methods:**
- `createEnrollment($studentId, $courseId, $groupId)` - Create enrollment
- `approveEnrollment($enrollmentId, $adminId, $groupId)` - Approve enrollment
- `rejectEnrollment($enrollmentId, $adminId, $reason)` - Reject enrollment
- `withdrawEnrollment($enrollmentId, $adminId)` - Withdraw enrollment

**Business Logic:**
- Enrollment starts as "pending"
- Admin approves/rejects enrollment
- On approval, student assigned to group
- Group capacity checked before assignment
- Attendance slots created for all group sessions

### AttendanceService (`app/Services/AttendanceService.php`)

**Purpose:** Manages attendance tracking.

**Key Methods:**
- `updateAttendance($sessionId, $studentId, $status, $markedBy, $notes)` - Update single attendance
- `bulkUpdateAttendance($sessionId, $attendanceData, $markedBy)` - Bulk update attendance
- `getStudentAttendance($studentId, $groupId)` - Get student attendance history
- `getSessionAttendance($sessionId)` - Get attendance for a session

**Business Logic:**
- One attendance record per student per session
- Status: present, absent, late, excused
- Can be marked by instructor or admin
- Timestamp recorded when marked present

### WebsiteActivationService (`app/Services/WebsiteActivationService.php`)

**Purpose:** Manages website setup and activation.

**Key Methods:**
- `isActivated()` - Check if website is activated
- `shouldRunSetup()` - Check if setup wizard should run
- `activateDefaultWebsite()` - Activate with defaults
- `completeSetup($data)` - Complete setup wizard
- `saveStep($step, $data)` - Save setup step
- `createDefaultPages()` - Create default CMS pages

**Business Logic:**
- Website must be activated before public access
- Setup wizard runs on first visit if not activated
- Default pages created on activation
- Settings saved incrementally during setup

### BrandingService (`app/Services/BrandingService.php`)

**Purpose:** Manages branding customization.

**Key Methods:**
- `updateBranding($data)` - Update branding settings
- `getBranding()` - Get current branding
- `getPublicBranding()` - Get public-facing branding

**Business Logic:**
- Branding stored in `website_settings.branding` JSON field
- Includes: logo, colors, fonts, theme
- Public branding excludes sensitive data

### CalendarService (`app/Services/CalendarService.php`)

**Purpose:** Manages calendar events.

**Key Methods:**
- `getUserEvents($userId, $startDate, $endDate)` - Get user events
- `createEvent($data)` - Create calendar event
- `updateEvent($eventId, $data)` - Update event
- `deleteEvent($eventId)` - Delete event

**Business Logic:**
- Events linked to users
- Can reference sessions, courses, etc.
- Supports all-day events
- Color coding for event types

### FileStorageService (`app/Services/FileStorageService.php`)

**Purpose:** Manages file uploads and storage.

**Key Methods:**
- `uploadFile($file, $path)` - Upload file
- `deleteFile($path)` - Delete file
- `getFileUrl($path)` - Get file URL

**Business Logic:**
- Files stored in `storage/app/public/`
- Supports local and S3 storage
- File validation and security checks

### PageBuilderService (`app/Services/PageBuilderService.php`)

**Purpose:** Manages CMS page building.

**Key Methods:**
- `createPage($data)` - Create page
- `updatePage($pageId, $data)` - Update page
- `updateBlocks($pageId, $blocks)` - Update page blocks
- `renderPage($slug)` - Render page for public

**Business Logic:**
- Pages have multiple blocks
- Blocks can be enabled/disabled
- Multi-language content support
- Blocks rendered in order

### QrAttendanceService (`app/Services/QrAttendanceService.php`)

**Purpose:** Manages QR code attendance.

**Key Methods:**
- `generateQr($sessionId)` - Generate QR code for session
- `verifyQr($token)` - Verify QR token
- `checkIn($token, $studentId)` - Student check-in via QR

**Business Logic:**
- QR tokens are time-limited
- Tokens are unique per session
- Check-in creates attendance record
- Tokens expire after session time

## Service Best Practices

### 1. Transaction Management
```php
public function createEnrollment($data)
{
    return DB::transaction(function () use ($data) {
        // All operations in transaction
        $enrollment = Enrollment::create($data);
        // ... more operations
        return $enrollment;
    });
}
```

### 2. Error Handling
```php
try {
    // Operation
} catch (\Exception $e) {
    Log::error('Operation failed', [
        'error' => $e->getMessage(),
        'data' => $data,
    ]);
    throw $e;
}
```

### 3. Validation
- Validate input before processing
- Use Form Requests for HTTP validation
- Service-level validation for business rules

### 4. Logging
- Log important operations
- Log errors with context
- Use appropriate log levels

### 5. Return Types
- Return domain models
- Use type hints
- Return consistent response formats

## Service Dependencies

Services can depend on:
- **Repositories:** Data access
- **Other Services:** Cross-domain operations
- **Models:** Direct model access (when needed)
- **Events:** Trigger domain events

## Testing Services

Services should be tested with:
- Unit tests for business logic
- Integration tests for database operations
- Mock dependencies for isolation

## Service Lifecycle

1. **Request:** Controller receives HTTP request
2. **Validation:** Form Request validates input
3. **Service Call:** Controller calls service method
4. **Business Logic:** Service executes business logic
5. **Data Access:** Service uses repository/model
6. **Response:** Service returns result
7. **Controller:** Controller formats response

## Conclusion

Services are the heart of business logic in the application. They:
- Encapsulate domain operations
- Ensure data consistency
- Handle complex workflows
- Maintain business rules
- Provide reusable functionality

