# Backend Structure

## Overview

The backend follows Laravel's modular architecture with Domain-Driven Design (DDD) principles. All code is organized in `graphic-school-api/` with clear separation between modules, services, controllers, and models.

**Architecture:** Modular Monolith  
**Pattern:** Domain-Driven Design (DDD)  
**Communication:** Interfaces & Events (zero dependencies between modules)

## Root Structure

```
graphic-school-api/
├── app/                    # Application core
│   ├── Http/
│   │   └── Controllers/    # Application controllers
│   ├── Models/             # Core domain models
│   ├── Services/           # Business logic services
│   └── Support/            # Shared support classes
├── bootstrap/              # Bootstrap files
├── config/                 # Configuration files
├── database/              # Migrations, seeders, factories
│   ├── migrations/        # Database migrations
│   └── seeders/           # Data seeders
├── Modules/               # Domain modules (DDD)
├── public/                # Public entry point
├── routes/                # Route definitions
│   └── api.php            # API routes
├── storage/               # File storage, logs
├── tests/                 # Test files
└── vendor/                 # Composer dependencies
```

## Application Directory (`app/`)

### Models (`app/Models/`)

Core domain models (non-module specific):

- **`Group.php`** - Course groups (belongs to Course)
- **`Attendance.php`** - Attendance records (belongs to GroupSession)
- **`WebsiteSetting.php`** - Website settings (branding, activation)
- **`Page.php`** - CMS pages
- **`PageBlock.php`** - Page builder blocks
- **`SessionTemplate.php`** - Session templates (optional)
- **`Country.php`** - Countries
- **`Currency.php`** - Currencies
- **`CalendarEvent.php`** - Calendar events (optional)

**Note:** Course, Enrollment, Certificate models are in `Modules/LMS/`

### Controllers (`app/Http/Controllers/`)

#### Admin Controllers
- **`Admin/AttendanceController.php`** - Attendance overview and management
- **`Admin/CertificateController.php`** - Certificate issuance and management
- **`Admin/CountryController.php`** - Country CRUD
- **`Admin/CurrencyController.php`** - Currency CRUD
- **`Admin/EnrollmentController.php`** - Enrollment approval/rejection/assignment
- **`Admin/GroupController.php`** - Group CRUD operations
- **`Admin/LanguageController.php`** - Language CRUD
- **`Admin/PageController.php`** - CMS page management
- **`Admin/SetupWizardController.php`** - Setup wizard flow

#### Instructor Controllers
- **`Instructor/AttendanceController.php`** - Take attendance (session-based)
- **`Instructor/InstructorController.php`** - Instructor dashboard (groups, sessions, students)

#### Student Controllers
- **`Student/AttendanceController.php`** - View attendance history
- **`Student/CertificateController.php`** - View certificates
- **`Student/EnrollmentController.php`** - Enrollment requests
- **`Student/StudentController.php`** - Student dashboard (courses, groups, sessions)

#### Public Controllers
- **`Public/CertificateController.php`** - Public certificate verification
- **`Public/ContactController.php`** - Contact form submission
- **`Public/EnrollmentController.php`** - Public enrollment (creates student + enrollment)
- **`Public/PageController.php`** - Public CMS pages

#### System Controllers
- **`HealthController.php`** - Health check endpoint
- **`DocsController.php`** - API documentation

### Services (`app/Services/`)

Business logic services:

- **`EnrollmentService.php`** - Enrollment workflow (create, approve, reject, withdraw)
- **`AttendanceService.php`** - Attendance business logic
- **`CertificateService.php`** - Certificate issuance and QR generation
- **`BrandingService.php`** - Branding management
- **`WebsiteActivationService.php`** - Website activation logic
- **`FileStorageService.php`** - File upload/management

**Service Pattern:**
```php
class EnrollmentService
{
    public function createEnrollment(int $studentId, int $courseId, ?int $groupId = null): Enrollment
    public function approveEnrollment(int $enrollmentId, ?int $adminId = null, ?int $groupId = null): Enrollment
    public function rejectEnrollment(int $enrollmentId, ?int $adminId = null, string $reason = ''): Enrollment
    public function withdrawEnrollment(int $enrollmentId, ?int $adminId = null): Enrollment
}
```

### Support (`app/Support/`)

Shared support classes:

- **`BaseController.php`** - Base controller with common methods
- **`BaseRepository.php`** - Base repository pattern
- **`BaseUseCase.php`** - Base use case pattern

## Modules Directory (`Modules/`)

Domain modules organized by business capability. Each module is self-contained with zero dependencies on other modules.

### Module Structure Template

```
ModuleName/
├── Database/
│   ├── Migrations/        # Module migrations
│   └── Seeders/           # Module seeders
├── Domain/                # Domain layer (business logic)
│   ├── Entities/          # Domain entities
│   ├── Repositories/      # Repository interfaces
│   └── Services/          # Domain services
├── Application/           # Application layer
│   ├── UseCases/          # Use cases
│   └── DTOs/              # Data Transfer Objects
├── Infrastructure/        # Infrastructure layer
│   ├── Repositories/      # Repository implementations
│   └── External/          # External integrations
├── Presentation/          # Presentation layer
│   ├── Http/
│   │   ├── Controllers/   # HTTP controllers
│   │   ├── Requests/      # Form requests
│   │   └── Resources/     # API resources
│   └── Routes/            # Route definitions
└── Providers/
    └── ModuleServiceProvider.php
```

### ACL (Access Control Layer)

```
Modules/ACL/
├── Auth/                  # Authentication
│   ├── Domain/
│   ├── Application/
│   └── Presentation/
├── Users/                 # User management
├── Roles/                 # Role management
└── Permissions/           # Permission management (if implemented)
```

**Responsibilities:**
- User authentication (login, logout, registration)
- User management (CRUD)
- Role-based access control (RBAC)
- Permission management

### LMS (Learning Management System)

```
Modules/LMS/
├── Courses/               # Course management
├── Categories/            # Course categories
├── Sessions/              # Session management
├── Enrollments/           # Enrollment management
├── Attendance/            # Attendance tracking
└── Certificates/          # Certificate management
```

**Key Models:**
- **Course** - Top learning entity (multiple instructors)
- **Group** - Belongs to Course (multiple groups per course)
- **GroupSession** - Belongs to Group (sessions/lectures)
- **Enrollment** - Student → Course → Group assignment
- **Attendance** - Per session (present/absent/late)
- **Certificate** - With QR verification

**Core Flow:**
```
Course → Group → Session → Enrollment → Attendance → Certificate
```

### CMS (Content Management System)

```
Modules/CMS/
├── PublicSite/            # Public-facing content
├── Settings/              # System settings
└── Contacts/              # Contact management
```

**Responsibilities:**
- Public website content
- CMS page builder
- Website settings (branding, languages, currencies)
- Contact form management

### Core Modules

```
Modules/Core/
├── Localization/          # Multi-language support
├── Notification/          # In-app notifications
├── FileStorage/           # File management
└── ExportImport/          # Data export/import
```

**Responsibilities:**
- Multi-language support (default: en, RTL for Arabic)
- Multi-currency support (default: EGP)
- Notification system
- File upload/management
- Data export/import

### Operations

```
Modules/Operations/
└── Dashboard/             # Dashboard operations
```

## Database Directory (`database/`)

### Migrations (`database/migrations/`)

**Core Migrations:**
- Course-related migrations
- Group-related migrations (with start_date, end_date, notes)
- GroupSession-related migrations (with instructor_id optional)
- Enrollment migrations (Course-based, removed program/batch)
- Attendance migrations (linked to GroupSession)
- Certificate migrations (with group_id, instructor_id, qr_code)
- Community migrations (removed program/batch references)
- CMS migrations (pages, blocks)
- Localization migrations (languages, currencies, countries)

**Migration Naming:**
- `YYYY_MM_DD_HHMMSS_descriptive_name.php`
- Example: `2025_01_28_200000_update_certificates_table_for_business_spec.php`

### Seeders (`database/seeders/`)

- Default data seeders (roles, languages, currencies, countries)
- Test data seeders (courses, groups, users)
- **Note:** CommunitySeeder cleaned (removed program/batch/gamification references)

## Routes (`routes/api.php`)

### Route Organization

```php
// Public routes (no auth)
Route::prefix('setup')->group(...);
Route::get('/courses', ...);
Route::post('/enroll', ...);
Route::get('/certificates/verify/{code}', ...);

// Authenticated routes
Route::middleware('auth:api')->group(function () {
    // Student routes
    Route::prefix('student')->middleware('role:student')->group(...);
    
    // Instructor routes
    Route::prefix('instructor')->middleware('role:instructor')->group(...);
    
    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(...);
});
```

### Route Conventions

- RESTful resource routes where applicable
- Action-based routes for complex operations (approve, reject, issue)
- Clear naming: `/api/{role}/{resource}/{action}`
- Unified API response format

**Example:**
```php
// Resource route
Route::apiResource('certificates', CertificateController::class);

// Action route
Route::post('/enrollments/{id}/approve', [EnrollmentController::class, 'approve']);
```

## Configuration (`config/`)

Key configuration files:

- **`app.php`** - Application configuration (name, locale, timezone)
- **`auth.php`** - Authentication configuration (Sanctum)
- **`database.php`** - Database configuration
- **`cors.php`** - CORS configuration
- **`sanctum.php`** - Sanctum token configuration
- **`filesystems.php`** - File storage configuration

## Naming Conventions

### Controllers
- **PascalCase** with "Controller" suffix: `GroupController.php`
- **Namespace:** `App\Http\Controllers\{Role}\`
- **Example:** `App\Http\Controllers\Admin\GroupController`

### Models
- **PascalCase:** `Group.php`
- **Namespace:** `App\Models\` or `Modules\{Module}\Models\`
- **Example:** `App\Models\Group` or `Modules\LMS\Courses\Models\Course`

### Services
- **PascalCase** with "Service" suffix: `EnrollmentService.php`
- **Namespace:** `App\Services\`
- **Example:** `App\Services\EnrollmentService`

### Migrations
- **Snake_case** with timestamp: `2025_01_20_120000_create_groups_table.php`
- Descriptive names explaining the change

## Key Patterns

### Repository Pattern

**Interface:**
```php
interface EnrollmentRepositoryInterface
{
    public function find(int $id): ?Enrollment;
    public function create(array $data): Enrollment;
    public function update(Enrollment $enrollment, array $data): Enrollment;
}
```

**Implementation:**
```php
class EloquentEnrollmentRepository implements EnrollmentRepositoryInterface
{
    // Eloquent-based implementation
}
```

### Service Pattern

Services encapsulate business logic:

```php
class EnrollmentService
{
    public function __construct(
        private EnrollmentRepositoryInterface $repository,
        private AttendanceService $attendanceService
    ) {}
    
    public function approveEnrollment(...): Enrollment
    {
        // Business logic here
    }
}
```

### Form Request Validation

```php
class StoreEnrollmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'group_id' => 'nullable|exists:groups,id',
        ];
    }
}
```

### API Resources

Transform models for API responses:

```php
class EnrollmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'course' => new CourseResource($this->course),
            'group' => new GroupResource($this->group),
            'status' => $this->status,
        ];
    }
}
```

## Best Practices

### 1. Separation of Concerns
- **Controllers:** Handle HTTP requests, delegate to services
- **Services:** Contain business logic
- **Models:** Data and relationships only
- **Repositories:** Data access abstraction

### 2. Single Responsibility
- Each class has one clear purpose
- Services are focused on specific domains
- Controllers are thin (delegate to services)

### 3. Dependency Injection
- Services injected via constructor
- Interfaces for abstraction
- Laravel service container handles resolution

### 4. Error Handling
- Custom exceptions for domain errors
- Proper error responses (unified format)
- Try-catch in services, not controllers

### 5. Validation
- Form requests for input validation
- Model validation in services
- Business rule validation in services

### 6. Transaction Management
- Database transactions for multi-step operations
- Use `DB::transaction()` for atomic operations

**Example:**
```php
public function approveEnrollment(...): Enrollment
{
    return DB::transaction(function () use (...) {
        // Multiple database operations
        // All succeed or all fail
    });
}
```

## Unified API Response Format

All API responses follow this format:

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

**Error Response:**
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

## Module Communication

Modules communicate via:
- **Interfaces:** Define contracts
- **Events:** Asynchronous communication
- **Zero Dependencies:** Modules don't directly depend on each other

**Example:**
```php
// Module A publishes event
event(new EnrollmentApproved($enrollment));

// Module B listens to event
class SomeService
{
    public function handle(EnrollmentApproved $event)
    {
        // Handle event
    }
}
```

## Testing

### Test Structure

```
tests/
├── Feature/               # Feature/integration tests
│   ├── Api/              # API endpoint tests
│   └── ...               # Other feature tests
└── Unit/                 # Unit tests
    ├── Services/         # Service tests
    └── ...               # Other unit tests
```

### Test Conventions
- Feature tests for API endpoints
- Unit tests for services and models
- Test coverage for critical business logic
- Use factories for test data

