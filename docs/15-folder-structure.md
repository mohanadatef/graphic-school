# 📁 Folder Structure - Graphic School

## هيكل المشروع

---

## Backend Structure (`graphic-school-api/`)

```
graphic-school-api/
├── app/                          # Application core
│   ├── Console/                  # Artisan commands
│   │   ├── Commands/            # Custom commands
│   │   └── Kernel.php           # Command scheduler
│   ├── Contracts/               # Interfaces & Contracts
│   │   ├── DTOs/                # DTO interfaces
│   │   ├── Events/              # Event interfaces
│   │   ├── Repositories/        # Repository interfaces
│   │   ├── Resources/           # Resource interfaces
│   │   └── Services/            # Service interfaces
│   ├── Enums/                   # Enums
│   ├── Exceptions/              # Custom exceptions
│   ├── Helpers/                 # Helper functions
│   ├── Http/                    # HTTP layer
│   │   ├── Controllers/         # Base controllers
│   │   ├── Kernel.php           # HTTP kernel
│   │   ├── Middleware/          # Custom middleware
│   │   └── Responses/           # Response classes
│   ├── Models/                  # App-level models
│   ├── Providers/               # Service providers
│   ├── Services/                 # App-level services
│   └── Support/                  # Support classes
│       ├── Audit/               # Audit trail
│       ├── Controllers/         # Base controllers
│       ├── Database/            # Database utilities
│       ├── DTOs/                # Base DTOs
│       ├── Events/              # Base events
│       ├── Export/              # Export utilities
│       ├── Health/              # Health check
│       ├── Jobs/                # Background jobs
│       ├── Models/              # Base models
│       ├── Observers/           # Base observers
│       ├── Repositories/        # Base repositories
│       ├── Table/               # Table builder
│       └── UseCases/            # Use case utilities
│
├── Modules/                      # Modular architecture
│   ├── ACL/                     # Access Control Layer
│   │   ├── Auth/                # Authentication
│   │   │   ├── Application/     # Use Cases, DTOs
│   │   │   ├── Domain/          # Events
│   │   │   ├── Infrastructure/  # Models, Repositories
│   │   │   └── Presentation/    # Controllers, Requests, Resources
│   │   ├── Permissions/         # Permissions management
│   │   ├── Roles/               # Roles management
│   │   └── Users/               # User management
│   │
│   ├── CMS/                     # Content Management System
│   │   ├── Contacts/            # Contact messages
│   │   ├── CourseReviews/       # Course reviews
│   │   ├── PublicSite/          # Public site controller
│   │   ├── Settings/            # Settings management
│   │   ├── Sliders/             # Sliders management
│   │   └── Testimonials/        # Testimonials management
│   │
│   ├── Core/                    # Core modules
│   │   ├── Categories/          # Categories (legacy)
│   │   ├── ExportImport/        # Export/Import
│   │   ├── FileStorage/         # File storage
│   │   ├── Localization/        # Multi-language support
│   │   ├── Notification/        # Notifications
│   │   └── Versioning/          # Versioning system
│   │
│   ├── LMS/                     # Learning Management System
│   │   ├── Assessments/         # Quizzes & Projects
│   │   ├── Attendance/          # Attendance tracking
│   │   ├── Categories/          # Course categories
│   │   ├── Certificates/        # Certificates
│   │   ├── CourseReviews/       # Course reviews
│   │   ├── Courses/             # Courses management
│   │   │   ├── Application/     # Use Cases, DTOs
│   │   │   ├── Domain/          # Events, Services
│   │   │   ├── Infrastructure/ # Models, Observers
│   │   │   └── Presentation/    # Controllers, Requests, Resources
│   │   ├── Curriculum/          # Curriculum (Modules, Lessons, Resources)
│   │   ├── Enrollments/         # Enrollments
│   │   ├── Progress/            # Student progress
│   │   └── Sessions/            # Sessions management
│   │
│   ├── Operations/              # Operations modules
│   │   ├── Analytics/           # Analytics
│   │   ├── Backup/              # Backup system
│   │   ├── Dashboard/           # Dashboard
│   │   ├── Logging/             # Activity logging
│   │   └── Reports/             # Reports
│   │
│   └── Support/                 # Support modules
│       ├── SystemHealth/        # System health
│       └── Tickets/             # Support tickets
│
├── bootstrap/                    # Bootstrap files
├── config/                       # Configuration files
├── database/                     # Database
│   ├── factories/               # Model factories
│   ├── migrations/               # Migrations
│   └── seeders/                 # Seeders
├── public/                       # Public files
├── resources/                    # Resources
│   ├── css/                     # CSS files
│   ├── js/                      # JS files
│   └── views/                   # Blade views
├── routes/                       # Routes
│   ├── api.php                  # API routes
│   ├── channels.php             # Broadcast channels
│   ├── console.php              # Console routes
│   └── web.php                  # Web routes
├── storage/                      # Storage
├── tests/                        # Tests
│   ├── Feature/                 # Feature tests
│   └── Unit/                    # Unit tests
└── vendor/                       # Composer dependencies
```

---

## Frontend Structure (`graphic-school-frontend/`)

```
graphic-school-frontend/
├── public/                       # Public assets
│   └── vite.svg                 # Favicon
│
├── src/                          # Source code
│   ├── assets/                   # Static assets
│   ├── components/               # Vue components
│   │   ├── common/               # Common components
│   │   │   ├── AccessibleButton.vue
│   │   │   ├── ErrorBoundary.vue
│   │   │   ├── FilterDropdown.vue
│   │   │   ├── Icon.vue
│   │   │   ├── LanguagePicker.vue
│   │   │   ├── LanguageSwitcher.vue
│   │   │   ├── LoadingSkeleton.vue
│   │   │   ├── PaginationControls.vue
│   │   │   ├── ThemeToggle.vue
│   │   │   └── ToastContainer.vue
│   │   └── layouts/              # Layout components
│   │       ├── DashboardLayout.vue
│   │       └── PublicLayout.vue
│   │
│   ├── composables/              # Vue composables
│   │   ├── useApi.js
│   │   ├── useAuth.js
│   │   ├── useFilters.js
│   │   ├── useI18n.js
│   │   ├── useListPage.js
│   │   ├── useLoading.js
│   │   ├── useLocale.js
│   │   ├── usePagination.js
│   │   ├── useSEO.js
│   │   ├── useTheme.js
│   │   └── useToast.js
│   │
│   ├── i18n/                     # Internationalization
│   │   ├── index.js
│   │   └── locales/
│   │       ├── ar.json           # Arabic translations
│   │       └── en.json            # English translations
│   │
│   ├── middleware/               # Route middleware
│   │   ├── auth.js
│   │   ├── guest.js
│   │   ├── index.js
│   │   └── role.js
│   │
│   ├── router/                   # Vue Router
│   │   └── index.js
│   │
│   ├── services/                 # API services
│   │   └── api/
│   │       ├── authService.js
│   │       ├── categoryService.js
│   │       ├── client.js          # Axios instance
│   │       ├── courseService.js
│   │       ├── index.js
│   │       ├── instructorService.js
│   │       ├── settingsService.js
│   │       ├── studentService.js
│   │       └── userService.js
│   │
│   ├── stores/                   # Pinia stores
│   │   ├── auth.js
│   │   ├── category.js
│   │   ├── course.js
│   │   ├── index.js
│   │   ├── instructor.js
│   │   ├── settings.js
│   │   └── student.js
│   │
│   ├── types/                     # TypeScript types (if used)
│   │   └── index.js
│   │
│   ├── utils/                     # Utilities
│   │   ├── errorHandler.js
│   │   ├── monitoring.js
│   │   ├── seo.js
│   │   └── validation.js
│   │
│   ├── views/                     # Vue views/pages
│   │   ├── dashboard/             # Dashboard views
│   │   │   ├── admin/             # Admin views
│   │   │   │   ├── AdminAttendance.vue
│   │   │   │   ├── AdminCategories.vue
│   │   │   │   ├── AdminContacts.vue
│   │   │   │   ├── AdminCourses.vue
│   │   │   │   ├── AdminDashboard.vue
│   │   │   │   ├── AdminEnrollments.vue
│   │   │   │   ├── AdminRoles.vue
│   │   │   │   ├── AdminSessions.vue
│   │   │   │   ├── AdminSettings.vue
│   │   │   │   ├── AdminSliders.vue
│   │   │   │   ├── AdminTranslations.vue
│   │   │   │   ├── AdminUsers.vue
│   │   │   │   ├── CategoryForm.vue
│   │   │   │   ├── CourseForm.vue
│   │   │   │   ├── EnrollmentForm.vue
│   │   │   │   ├── ReportsPage.vue
│   │   │   │   ├── RoleForm.vue
│   │   │   │   ├── SessionForm.vue
│   │   │   │   ├── SliderForm.vue
│   │   │   │   ├── StrategicReportsPage.vue
│   │   │   │   ├── TranslationForm.vue
│   │   │   │   └── UserForm.vue
│   │   │   ├── instructor/        # Instructor views
│   │   │   │   ├── InstructorAttendance.vue
│   │   │   │   ├── InstructorCourses.vue
│   │   │   │   ├── InstructorNotes.vue
│   │   │   │   └── InstructorSessions.vue
│   │   │   └── student/          # Student views
│   │   │       ├── CourseLearning.vue
│   │   │       ├── LessonPlayer.vue
│   │   │       ├── MyCourses.vue
│   │   │       ├── QuizAttempt.vue
│   │   │       ├── StudentAttendance.vue
│   │   │       ├── StudentCertificates.vue
│   │   │       ├── StudentCourses.vue
│   │   │       ├── StudentDashboard.vue
│   │   │       ├── StudentProfile.vue
│   │   │       ├── StudentProjects.vue
│   │   │       ├── StudentQuizzes.vue
│   │   │       └── StudentSessions.vue
│   │   └── public/               # Public views
│   │       ├── AboutPage.vue
│   │       ├── ContactPage.vue
│   │       ├── CourseDetailsPage.vue
│   │       ├── CoursesPage.vue
│   │       ├── HomePage.vue
│   │       ├── InstructorDetailsPage.vue
│   │       ├── InstructorsPage.vue
│   │       ├── LoginPage.vue
│   │       └── RegisterPage.vue
│   │
│   ├── App.vue                   # Root component
│   ├── main.js                   # Entry point
│   └── style.css                 # Global styles
│
├── index.html                     # HTML template
├── package.json                   # NPM dependencies
├── postcss.config.js              # PostCSS config
├── tailwind.config.js             # Tailwind CSS config
└── vite.config.js                 # Vite config
```

---

## Module Structure Pattern

كل Module يتبع نفس البنية:

```
ModuleName/
├── Application/                  # Application Layer
│   ├── DTOs/                    # Data Transfer Objects
│   └── UseCases/                # Use Cases
│
├── Domain/                       # Domain Layer
│   ├── Events/                  # Domain Events
│   └── Services/                # Domain Services
│
├── Infrastructure/               # Infrastructure Layer
│   ├── Models/                  # Eloquent Models
│   ├── Observers/               # Model Observers
│   ├── Repositories/            # Repositories
│   │   ├── Eloquent/           # Eloquent Implementation
│   │   └── Interfaces/         # Repository Interfaces
│   └── Jobs/                    # Background Jobs
│
├── Presentation/                 # Presentation Layer
│   └── Http/
│       ├── Controllers/         # Controllers
│       ├── Requests/            # Form Requests
│       ├── Resources/           # API Resources
│       └── Routes/
│           └── api.php         # Module routes
│
├── Database/
│   ├── Migrations/              # Database migrations
│   └── Seeders/                 # Database seeders
│
├── Providers/
│   └── ModuleServiceProvider.php # Module service provider
│
└── Config/
    └── module.php                # Module configuration
```

---

## Patterns المستخدمة

### 1. Modular Pattern
- كل Module مستقل
- بنية موحدة لكل Module
- سهولة الصيانة والتطوير

### 2. DDD Pattern
- Domain, Application, Infrastructure, Presentation layers
- فصل واضح للمسؤوليات

### 3. Repository Pattern
- Interface + Implementation
- Dependency Injection
- سهولة الاختبار

### 4. Use Case Pattern
- كل Use Case = عملية business واحدة
- BaseUseCase abstract class
- DTOs للـ input/output

### 5. Service Layer Pattern
- Services في Application/Infrastructure layers
- Business logic coordination

---

## File Naming Conventions

### Backend (PHP):
- **Controllers**: `{Resource}Controller.php` (PascalCase)
- **Models**: `{ModelName}.php` (PascalCase)
- **Requests**: `{Action}{Resource}Request.php` (PascalCase)
- **Resources**: `{Resource}Resource.php` (PascalCase)
- **Use Cases**: `{Action}{Resource}UseCase.php` (PascalCase)
- **DTOs**: `{Action}{Resource}DTO.php` (PascalCase)
- **Services**: `{Resource}Service.php` (PascalCase)
- **Repositories**: `{Resource}Repository.php` (PascalCase)
- **Migrations**: `{timestamp}_{description}.php` (snake_case)

### Frontend (JavaScript/Vue):
- **Components**: `{ComponentName}.vue` (PascalCase)
- **Composables**: `use{Name}.js` (camelCase)
- **Stores**: `{name}.js` (camelCase)
- **Services**: `{name}Service.js` (camelCase)
- **Utils**: `{name}.js` (camelCase)
- **Views**: `{PageName}.vue` (PascalCase)

---

## Important Directories

### Backend:
- `app/Support/`: Base classes, utilities
- `Modules/`: جميع Modules
- `database/migrations/`: Migrations
- `database/seeders/`: Seeders
- `routes/api.php`: Main API routes
- `tests/`: Tests

### Frontend:
- `src/components/`: Reusable components
- `src/views/`: Page components
- `src/stores/`: State management
- `src/services/`: API services
- `src/composables/`: Composable functions
- `src/utils/`: Utility functions

---

## Configuration Files

### Backend:
- `composer.json`: PHP dependencies
- `config/`: Laravel configuration
- `.env`: Environment variables
- `phpunit.xml`: Test configuration

### Frontend:
- `package.json`: NPM dependencies
- `vite.config.js`: Vite configuration
- `tailwind.config.js`: Tailwind CSS configuration
- `.env`: Environment variables

---

**آخر تحديث**: 2025-11-21  
**الإصدار**: 1.0.0

