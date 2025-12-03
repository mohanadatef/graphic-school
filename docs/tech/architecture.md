# System Architecture

## Overview

Graphic School 2.0 is a modern **Learning Management & Coaching (LMC)** platform built with a clean, modular architecture. The system follows Domain-Driven Design principles and implements a clear separation between backend API and frontend SPA.

**Architecture Pattern:** Modular Monolith with DDD Structure  
**Tenancy:** One client per deployment (own domain, own DB)  
**Core Flow:** Course → Group → Session → Enrollment → Attendance → Certificate

## Architecture Pattern

**Backend:** Laravel 11 (PHP) - RESTful API  
**Frontend:** Vue.js 3 (Composition API) - Single Page Application  
**Database:** MySQL/MariaDB  
**Authentication:** Laravel Sanctum (Token-based)

## High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend (Vue.js 3)                   │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐              │
│  │  Public  │  │ Dashboard │  │  Setup   │              │
│  │  Pages   │  │  (Admin/ │  │  Wizard  │              │
│  │          │  │Instructor│  │          │              │
│  │          │  │ Student)  │  │          │              │
│  └──────────┘  └──────────┘  └──────────┘              │
│         │              │              │                  │
│         └──────────────┴──────────────┘                  │
│                    Pinia Stores                           │
│              (State Management)                           │
└─────────────────────────────────────────────────────────┘
                        │
                        │ HTTP/REST API (JSON)
                        │ Laravel Sanctum (Tokens)
                        │
┌─────────────────────────────────────────────────────────┐
│              Backend API (Laravel 11)                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐              │
│  │  Routes  │  │Controllers│ │ Services │              │
│  │          │  │          │  │          │              │
│  └──────────┘  └──────────┘  └──────────┘              │
│         │            │            │                     │
│         └────────────┴────────────┘                     │
│                    │                                     │
│              ┌─────▼─────┐                              │
│              │  Models   │                              │
│              │ (Eloquent)│                              │
│              │Repositories│                             │
│              └─────┬─────┘                              │
└────────────────────┼────────────────────────────────────┘
                     │
                     │ Eloquent ORM
                     │
┌────────────────────▼────────────────────────────────────┐
│              Database (MySQL/MariaDB)                     │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐              │
│  │   Auth   │  │   LMS    │  │   CMS    │              │
│  │  Tables  │  │  Tables  │  │  Tables  │              │
│  └──────────┘  └──────────┘  └──────────┘              │
└─────────────────────────────────────────────────────────┘
```

## Core Business Flow

```
Course (Multiple Instructors)
    ↓
Group (Multiple Groups per Course, Dates, Notes)
    ↓
Session (Sessions in Groups, Optional Instructor)
    ↓
Enrollment (Student → Course → Admin Approval → Group Assignment)
    ↓
Attendance (Instructor marks: Present/Absent/Late per Session)
    ↓
Certificate (Admin/Instructor issues with QR Verification)
```

**Additional Modules:**
- **Community:** Posts, Comments, Replies (NO gamification)
- **CMS:** Website pages, branding, multi-language
- **Localization:** Languages, currencies, countries

## Core Principles

### 1. Separation of Concerns
- **Backend:** Business logic, data persistence, API endpoints
- **Frontend:** User interface, client-side routing, state management
- **Database:** Data storage and relationships

### 2. Modular Design
- **Laravel Modules:** Organized by domain (ACL, LMS, CMS, Core)
- **Vue Components:** Reusable, composable UI components
- **Services:** Business logic encapsulation
- **Stores:** Centralized state management (Pinia)

### 3. API-First Approach
- All frontend-backend communication via REST API
- Stateless authentication (tokens)
- JSON-based data exchange
- Unified API response format

### 4. Domain-Driven Design
- Clear domain boundaries (Auth, LMS, CMS)
- Rich domain models with business logic
- Repository pattern for data access
- Zero dependencies between modules (communication via Interfaces & Events)

## Technology Stack

### Backend
- **Framework:** Laravel 11
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0+ / MariaDB 10.6+
- **Authentication:** Laravel Sanctum
- **API Documentation:** Swagger/OpenAPI

### Frontend
- **Framework:** Vue.js 3 (Composition API)
- **Build Tool:** Vite
- **State Management:** Pinia
- **Routing:** Vue Router
- **HTTP Client:** Axios
- **Styling:** Tailwind CSS
- **i18n:** Vue I18n (Multi-language support)

### Development Tools
- **Testing:** PHPUnit (Backend), Vitest (Frontend), Cypress (E2E)
- **Version Control:** Git
- **Package Management:** Composer (PHP), npm (JavaScript)

## Module Structure

### Backend Modules (`graphic-school-api/Modules/`)

#### ACL (Access Control Layer)
- **Auth:** Authentication & registration
- **Users:** User management (Admin, Instructor, Student)
- **Roles:** Role-based access control
- **Permissions:** Permission management

#### LMS (Learning Management System)
- **Courses:** Course management (TOP learning entity)
- **Categories:** Course categorization
- **Groups:** Group management (belongs to Course)
- **Sessions:** Group sessions (lectures)
- **Enrollments:** Student enrollment management (Course → Group)
- **Attendance:** Attendance tracking (Present/Absent/Late)
- **Certificates:** Certificate issuance with QR verification

#### CMS (Content Management System)
- **PublicSite:** Public-facing content
- **Settings:** System settings (branding, languages, currencies)
- **Contacts:** Contact form management
- **Pages:** CMS page builder

#### Core
- **Localization:** Multi-language support (default: en, RTL for Arabic)
- **Notification:** In-app notifications
- **FileStorage:** File upload/management
- **ExportImport:** Data export/import

### Frontend Structure (`graphic-school-frontend/src/`)

```
src/
├── components/          # Reusable Vue components
│   ├── common/         # Shared components
│   ├── layouts/        # Layout components
│   ├── admin/          # Admin-specific components
│   └── public/         # Public site components
├── views/              # Page components
│   ├── public/         # Public pages
│   │   ├── HomePage.vue
│   │   ├── CoursesPage.vue
│   │   ├── CourseDetailsPage.vue
│   │   ├── PublicEnrollmentForm.vue
│   │   ├── CertificateVerification.vue
│   │   └── ...
│   └── dashboard/      # Dashboard pages
│       ├── admin/      # Admin pages
│       │   ├── AdminDashboard.vue
│       │   ├── AdminCourses.vue
│       │   ├── AdminGroups.vue
│       │   ├── AdminSessions.vue
│       │   ├── AdminEnrollments.vue
│       │   ├── AdminAttendance.vue
│       │   ├── AdminCertificates.vue
│       │   ├── AdminCommunity.vue
│       │   └── ...
│       ├── instructor/ # Instructor pages
│       │   ├── InstructorMyGroups.vue
│       │   ├── InstructorSessions.vue
│       │   ├── InstructorTakeAttendance.vue
│       │   ├── InstructorCommunity.vue
│       │   └── ...
│       └── student/    # Student pages
│           ├── StudentMyCourses.vue
│           ├── StudentMyGroup.vue
│           ├── StudentMySessions.vue
│           ├── StudentAttendanceHistory.vue
│           ├── StudentCertificates.vue
│           ├── StudentCommunity.vue
│           └── ...
├── stores/             # Pinia stores
│   ├── auth.js
│   ├── course.js
│   ├── group.js
│   ├── session.js
│   ├── enrollment.js
│   ├── attendance.js
│   ├── certificate.js
│   ├── community.js
│   └── ...
├── services/           # API services
│   └── api/
│       ├── courseService.js
│       ├── groupService.js
│       ├── sessionService.js
│       ├── enrollmentService.js
│       ├── attendanceService.js
│       ├── certificateService.js
│       ├── communityService.js
│       └── ...
├── router/             # Vue Router configuration
├── composables/        # Vue composables
├── middleware/         # Route middleware
└── utils/              # Utility functions
```

## Data Flow

### Request Flow
1. **User Action** → Frontend component
2. **Store Action** → Pinia store method
3. **API Call** → Service method → Axios request to backend
4. **Route** → Laravel route handler
5. **Controller** → Business logic execution
6. **Service** → Domain logic processing
7. **Repository/Model** → Database query via Eloquent
8. **Response** → JSON response (unified format) to frontend
9. **State Update** → Pinia store update
10. **UI Update** → Reactive component update

### Authentication Flow
1. User submits login credentials
2. Frontend sends POST to `/api/login`
3. Backend validates credentials
4. Backend generates Sanctum token
5. Token returned to frontend
6. Frontend stores token in Pinia store (`auth.js`)
7. Token included in subsequent API requests (Axios interceptor)
8. Backend validates token on each request
9. Role-based access control enforced via middleware

### Enrollment Flow
1. Student submits enrollment (public form or authenticated)
2. Enrollment created with status: `pending`
3. Admin reviews enrollment
4. Admin approves and assigns to Group
5. Student automatically added to group
6. Attendance slots created for all group sessions
7. Student can now view courses, groups, sessions

## Security Architecture

### Authentication
- Token-based authentication (Laravel Sanctum)
- Password hashing (bcrypt)
- CSRF protection for web routes
- Rate limiting on auth endpoints (5 attempts per minute)

### Authorization
- Role-based access control (RBAC)
  - **Admin:** Full system access
  - **Instructor:** Group/session/attendance management
  - **Student:** Own courses/groups/sessions view
- Permission-based fine-grained control
- Middleware-based route protection
- Frontend route guards (role-based)

### Data Protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Vue.js auto-escaping)
- Input validation (Laravel Form Requests)
- Secure file uploads (validation, storage)

## Scalability Considerations

### Horizontal Scaling
- Stateless API design
- Database connection pooling
- Token storage in database
- Load balancer ready

### Performance Optimization
- Database indexing (on foreign keys, search fields)
- Query optimization (Eager loading relationships)
- Frontend code splitting (route-based)
- API response caching (where applicable)
- CDN for static assets

## Deployment Architecture

### Production Setup
- **Web Server:** Nginx
- **Application Server:** PHP-FPM
- **Database:** MySQL/MariaDB (master-slave for read scaling, optional)
- **Cache:** Redis (optional, for sessions/queue)
- **Queue:** Redis/Database queues (for async jobs)
- **File Storage:** Local/S3 (configurable)

### Environment Separation
- **Development:** Local development environment
- **Staging:** Pre-production testing
- **Production:** Live environment

## Monitoring & Logging

### Application Monitoring
- Laravel logging (daily rotation)
- Error tracking
- Performance monitoring
- Health check endpoints (`/api/health`)

### Infrastructure Monitoring
- Server resource monitoring
- Database performance monitoring
- API response time tracking
- User activity analytics (optional)

## Key Architectural Decisions

### 1. Course as Top Learning Entity
- **Decision:** Course is the primary learning entity (NO programs/tracks/batches)
- **Rationale:** Simplified structure aligns with business requirement
- **Impact:** Cleaner data model, easier to understand

### 2. Group-Based Organization
- **Decision:** Multiple groups per course, sessions belong to groups
- **Rationale:** Flexible scheduling, multiple cohorts per course
- **Impact:** Better organization, scalable to multiple groups

### 3. Session-Based Attendance
- **Decision:** Attendance tracked per session (not QR-based)
- **Rationale:** Manual control by instructor, no hardware dependency
- **Impact:** Simple, reliable attendance tracking

### 4. Certificate with QR Verification
- **Decision:** Certificates include QR code for public verification
- **Rationale:** Easy verification without login
- **Impact:** Transparent certificate verification

### 5. Community Module (No Gamification)
- **Decision:** Community features without points/XP/badges
- **Rationale:** Focus on engagement, not competition
- **Impact:** Cleaner, simpler community experience

## Future Architecture Considerations

### Potential Enhancements
- Real-time features (WebSockets for notifications)
- Mobile app support (API-first design supports this)
- Advanced reporting/analytics
- Integration with external tools (Zapier, webhooks)

## Conclusion

The architecture is designed to be:
- **Maintainable:** Clear separation of concerns, modular design
- **Scalable:** Stateless, horizontal scaling ready
- **Secure:** Multiple layers of protection
- **Extensible:** Easy to add new features
- **Testable:** Clear boundaries for testing
- **Business-Aligned:** Course → Group → Session → Enrollment → Attendance → Certificate flow
