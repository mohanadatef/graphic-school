# System Overview

## Introduction

Graphic School 2.0 is a comprehensive Learning Management Core (LMC) platform designed for educational institutions to manage courses, students, instructors, and learning activities. The system provides a complete solution for course delivery, student enrollment, attendance tracking, and administrative management.

## System Purpose

The platform enables educational institutions to:
- Create and manage courses with multiple instructors
- Organize students into groups within courses
- Schedule and manage group sessions
- Track student enrollment and attendance
- Manage assignments and communication
- Issue certificates upon course completion
- Provide a public-facing website for course discovery

## Core Business Model

### Learning Management Core (LMC)

The system is built around a simple, powerful domain model:

```
Course
  ├── Multiple Instructors
  ├── Multiple Groups
  │     ├── One Instructor per Group
  │     ├── Multiple Students
  │     └── Multiple Group Sessions
  │           └── Attendance Records
  └── Session Templates
        └── Used by Group Sessions
```

### Key Entities

1. **Course:** The main learning program
   - Has multiple instructors
   - Contains multiple groups
   - Has session templates
   - Manages enrollments

2. **Group:** A specific class within a course
   - Belongs to one course
   - Has one primary instructor
   - Contains multiple students
   - Has scheduled sessions

3. **SessionTemplate:** Reusable session structure
   - Defines session content
   - Linked to course
   - Used to generate group sessions

4. **GroupSession:** Actual scheduled session
   - Belongs to a group
   - Based on a session template
   - Has specific date/time
   - Tracks attendance

5. **Enrollment:** Student registration
   - Links student to course
   - Assigns student to group
   - Tracks approval status
   - Manages payment status

6. **Attendance:** Session attendance record
   - Links student to group session
   - Records attendance status
   - Tracks who marked attendance

## System Components

### 1. Authentication & Authorization
- User registration and login
- Role-based access control (Admin, Instructor, Student)
- Permission management
- Session management

### 2. Course Management
- Course creation and editing
- Category organization
- Instructor assignment
- Session template creation
- Course publishing

### 3. Group Management
- Group creation within courses
- Instructor assignment to groups
- Student assignment to groups
- Capacity management

### 4. Session Management
- Session template creation
- Group session scheduling
- Session date/time management
- Session notes and comments

### 5. Enrollment Management
- Student enrollment requests
- Enrollment approval workflow
- Group assignment
- Payment tracking

### 6. Attendance Tracking
- Session-based attendance
- QR code attendance (optional)
- Attendance history
- Attendance reports

### 7. Public Website
- Course listing
- Course details
- Instructor profiles
- Contact forms
- FAQ pages
- Dynamic page builder

### 8. System Settings
- Branding customization
- Multi-language support
- Currency management
- Country management
- Setup wizard

## User Roles

### Admin
- Full system access
- Course and group management
- User management
- Enrollment approval
- System configuration
- Reports and analytics

### Instructor
- View assigned groups
- Manage group sessions
- Take attendance
- View student lists
- Session management
- Calendar view

### Student
- View enrolled courses
- View assigned group
- View sessions
- View attendance history
- Submit assignments
- View profile

## Key Features

### Core Features
- ✅ Multi-instructor courses
- ✅ Group-based learning
- ✅ Session scheduling
- ✅ Enrollment workflow
- ✅ Attendance tracking
- ✅ Assignment management
- ✅ Certificate generation
- ✅ Communication tools

### Public Website Features
- ✅ Course discovery
- ✅ Course details
- ✅ Instructor profiles
- ✅ Contact forms
- ✅ FAQ pages
- ✅ Dynamic page builder
- ✅ Multi-language support

### Administrative Features
- ✅ User management
- ✅ Role management
- ✅ System settings
- ✅ Branding customization
- ✅ Language management
- ✅ Currency management
- ✅ Country management
- ✅ Setup wizard

## System Workflows

### Course Creation Workflow
1. Admin creates course
2. Admin assigns instructors
3. Admin creates session templates
4. Admin creates groups
5. Admin assigns instructors to groups
6. Course is published

### Enrollment Workflow
1. Student views public course
2. Student submits enrollment request
3. Admin reviews enrollment
4. Admin approves/rejects enrollment
5. If approved, student is assigned to group
6. Student can access course content

### Attendance Workflow
1. Instructor creates group session
2. Session date/time is scheduled
3. On session day, instructor takes attendance
4. Attendance is recorded per student
5. Students can view attendance history
6. Admin can view attendance reports

## Technical Specifications

### Backend
- **Framework:** Laravel 11
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0+ / MariaDB 10.6+
- **API:** RESTful JSON API
- **Authentication:** Laravel Sanctum

### Frontend
- **Framework:** Vue.js 3 (Composition API)
- **Build Tool:** Vite
- **State Management:** Pinia
- **Routing:** Vue Router
- **Styling:** Tailwind CSS
- **i18n:** Vue I18n

### Database
- **Primary Database:** MySQL/MariaDB
- **Schema:** Normalized relational database
- **Relationships:** Foreign keys and indexes
- **Migrations:** Version-controlled schema

## System Requirements

### Server Requirements
- PHP 8.2 or higher
- MySQL 8.0+ or MariaDB 10.6+
- Nginx or Apache
- Composer
- Node.js 18+ (for frontend build)

### Client Requirements
- Modern web browser (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Internet connection

## Deployment Model

### Single-Tenant
- One instance per institution
- Isolated data
- Custom branding
- Independent configuration

### Deployment Options
- Self-hosted
- Cloud hosting (AWS, DigitalOcean, etc.)
- VPS deployment
- Dedicated server

## Security Model

### Authentication
- Token-based authentication
- Secure password hashing
- Session management
- Password reset functionality

### Authorization
- Role-based access control
- Permission-based fine-grained control
- Route protection
- API endpoint security

### Data Protection
- SQL injection prevention
- XSS protection
- CSRF protection
- Secure file uploads
- Data encryption at rest

## Integration Capabilities

### Current Integrations
- Email service (SMTP)
- File storage (local/S3)
- QR code generation

### Potential Integrations
- Payment gateways
- SMS notifications
- Video conferencing
- Calendar systems
- Learning analytics

## Scalability

### Current Capacity
- Supports multiple courses
- Multiple groups per course
- Multiple students per group
- Multiple sessions per group

### Scaling Considerations
- Horizontal scaling ready
- Database optimization
- Caching strategies
- Load balancing support

## Maintenance & Support

### System Maintenance
- Regular backups
- Database optimization
- Security updates
- Performance monitoring

### Support Model
- Documentation
- Error logging
- Health monitoring
- Update procedures

## Conclusion

Graphic School 2.0 provides a complete, modern solution for learning management. The system is designed to be:
- **User-friendly:** Intuitive interfaces for all user types
- **Flexible:** Adaptable to different educational models
- **Scalable:** Can grow with institution needs
- **Secure:** Multiple layers of security
- **Maintainable:** Clean architecture and documentation

