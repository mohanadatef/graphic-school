# Backend Folder Structure

## Overview

The backend follows Laravel's modular architecture with domain-driven organization. All code is organized in `graphic-school-api/`.

## Root Structure

```
graphic-school-api/
├── app/                    # Application core
├── bootstrap/              # Bootstrap files
├── config/                 # Configuration files
├── database/              # Migrations, seeders, factories
├── deployment/            # Deployment documentation
├── Modules/               # Domain modules
├── public/                # Public entry point
├── resources/             # Views, assets
├── routes/                # Route definitions
├── storage/               # File storage, logs
├── tests/                 # Test files
└── vendor/                 # Composer dependencies
```

## App Directory (`app/`)

### Models (`app/Models/`)
Core domain models (non-module specific):
- `Attendance.php` - Attendance records
- `CalendarEvent.php` - Calendar events
- `Country.php` - Countries
- `Currency.php` - Currencies
- `Group.php` - Course groups
- `Page.php` - CMS pages
- `PageBlock.php` - Page blocks
- `SessionTemplate.php` - Session templates
- `SubscriptionPlan.php` - Subscription plans (optional)
- `SubscriptionUsageTracker.php` - Usage tracking (optional)
- `WebsiteSetting.php` - Website settings

### Controllers (`app/Http/Controllers/`)

#### Admin Controllers
- `Admin/AttendanceController.php` - Attendance management
- `Admin/CountryController.php` - Country CRUD
- `Admin/CurrencyController.php` - Currency CRUD
- `Admin/EnrollmentController.php` - Enrollment management
- `Admin/GroupController.php` - Group management
- `Admin/LanguageController.php` - Language CRUD
- `Admin/PageController.php` - CMS page management
- `Admin/SetupWizardController.php` - Setup wizard

#### Instructor Controllers
- `Instructor/AttendanceController.php` - Take attendance
- `Instructor/InstructorController.php` - Instructor dashboard
- `Instructor/QrAttendanceController.php` - QR attendance

#### Student Controllers
- `Student/AttendanceController.php` - View attendance
- `Student/EnrollmentController.php` - Enrollment requests
- `Student/StudentController.php` - Student dashboard
- `Student/QrAttendanceController.php` - QR check-in

#### Public Controllers
- `Public/ContactController.php` - Contact form
- `Public/EnrollmentController.php` - Public enrollment
- `Public/PageController.php` - Public pages

#### System Controllers
- `CalendarController.php` - Calendar management
- `HealthController.php` - Health checks
- `DocsController.php` - API documentation

### Services (`app/Services/`)
Business logic services:
- `AttendanceService.php` - Attendance business logic
- `BrandingService.php` - Branding management
- `CalendarService.php` - Calendar operations
- `EnrollmentService.php` - Enrollment business logic
- `FileStorageService.php` - File operations
- `PageBuilderService.php` - Page builder logic
- `QrAttendanceService.php` - QR attendance
- `WebsiteActivationService.php` - Website activation

### Support (`app/Support/`)
Shared support classes:
- `BaseCommand.php` - Base command class
- `BaseQuery.php` - Base query builder
- `BaseUseCase.php` - Base use case
- `Controllers/BaseController.php` - Base controller
- `Repositories/BaseRepository.php` - Base repository

## Modules Directory (`Modules/`)

Domain modules organized by business capability:

### ACL (Access Control Layer)
```
Modules/ACL/
├── Auth/              # Authentication
│   ├── Http/Controllers/
│   ├── Services/
│   └── Requests/
├── Permissions/       # Permission management
├── Roles/            # Role management
└── Users/            # User management
```

### LMS (Learning Management System)
```
Modules/LMS/
├── Attendance/        # Attendance tracking
├── Categories/        # Course categories
├── Courses/           # Course management
├── Enrollments/       # Enrollment management
└── Sessions/          # Session management
```

### CMS (Content Management System)
```
Modules/CMS/
├── PublicSite/        # Public-facing content
├── Settings/          # System settings
└── Contacts/          # Contact management
```

### Core
```
Modules/Core/
├── Localization/      # Multi-language support
├── Notification/      # In-app notifications
├── FileStorage/       # File management
└── ExportImport/      # Data export/import
```

## Database Directory (`database/`)

### Migrations (`database/migrations/`)
- Course-related migrations
- Group-related migrations
- Session-related migrations
- Enrollment-related migrations
- Attendance-related migrations
- CMS-related migrations
- Settings-related migrations

### Seeders (`database/seeders/`)
- Default data seeders
- Test data seeders

### Factories (`database/factories/`)
- Model factories for testing

## Routes (`routes/`)

### API Routes (`routes/api.php`)
- Public routes (no auth)
- Authenticated routes
- Role-based route groups (admin, instructor, student)

### Web Routes (`routes/web.php`)
- Web-specific routes (if any)

## Configuration (`config/`)

Key configuration files:
- `app.php` - Application configuration
- `auth.php` - Authentication configuration
- `database.php` - Database configuration
- `cors.php` - CORS configuration
- `sanctum.php` - Sanctum configuration

## Testing (`tests/`)

### Feature Tests (`tests/Feature/`)
- API endpoint tests
- Integration tests
- Business logic tests

### Unit Tests (`tests/Unit/`)
- Model tests
- Service tests
- Utility tests

## Deployment (`deployment/`)

Deployment documentation:
- `server-setup.md` - Server configuration
- `security-hardening.md` - Security setup
- `monitoring-setup.md` - Monitoring configuration
- `email-service-setup.md` - Email configuration
- `payment-gateway-setup.md` - Payment setup

## Key Patterns

### Repository Pattern
- Interfaces in `Contracts/Repositories/`
- Implementations in `Repositories/Eloquent/`

### Service Pattern
- Business logic in `Services/`
- Domain services in module `Services/` directories

### Request Validation
- Form requests in module `Http/Requests/` directories

### Resource Transformation
- API resources in module `Http/Resources/` directories

## Naming Conventions

### Controllers
- PascalCase: `GroupController.php`
- Namespace: `App\Http\Controllers\Admin\`

### Models
- PascalCase: `Group.php`
- Namespace: `App\Models\` or `Modules\{Module}\Models\`

### Services
- PascalCase with "Service" suffix: `EnrollmentService.php`
- Namespace: `App\Services\`

### Migrations
- Snake_case with timestamp: `2025_01_20_120000_create_groups_table.php`

## Module Structure Template

Each module follows this structure:
```
ModuleName/
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Repositories/
│   ├── Eloquent/
│   └── Interfaces/
├── Services/
└── Providers/
    └── ModuleServiceProvider.php
```

## Best Practices

1. **Separation of Concerns**
   - Controllers handle HTTP requests
   - Services contain business logic
   - Models contain data and relationships
   - Repositories handle data access

2. **Single Responsibility**
   - Each class has one clear purpose
   - Services are focused on specific domains

3. **Dependency Injection**
   - Services injected via constructor
   - Interfaces for abstraction

4. **Error Handling**
   - Custom exceptions for domain errors
   - Proper error responses

5. **Validation**
   - Form requests for input validation
   - Model validation in services

