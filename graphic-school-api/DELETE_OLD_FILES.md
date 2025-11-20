# 🗑️ Delete Old Files - ملفات قديمة غير مستخدمة

## ⚠️ تحذير: هذه الملفات قديمة وتم نقلها إلى Modules

**✅ تم التحقق:** جميع Routes تستخدم Controllers من Modules فقط

---

## 📋 الملفات الجاهزة للحذف

### 1. Controllers (19 files)
```
app/Http/Controllers/Admin/AttendanceController.php
app/Http/Controllers/Admin/CategoryController.php
app/Http/Controllers/Admin/ContactController.php
app/Http/Controllers/Admin/CourseController.php
app/Http/Controllers/Admin/DashboardController.php
app/Http/Controllers/Admin/EnrollmentController.php
app/Http/Controllers/Admin/ReportController.php
app/Http/Controllers/Admin/RoleController.php
app/Http/Controllers/Admin/SessionController.php
app/Http/Controllers/Admin/SettingController.php
app/Http/Controllers/Admin/TestimonialController.php
app/Http/Controllers/Admin/TranslationController.php
app/Http/Controllers/Admin/UserController.php
app/Http/Controllers/AuthController.php
app/Http/Controllers/StudentController.php
app/Http/Controllers/InstructorController.php
app/Http/Controllers/PublicController.php
app/Http/Controllers/LanguageController.php
```

### 2. Services (17 files)
```
app/Services/AttendanceService.php
app/Services/AuthService.php
app/Services/CategoryService.php
app/Services/ContactMessageService.php
app/Services/CourseService.php
app/Services/DashboardService.php
app/Services/EnrollmentService.php
app/Services/InstructorService.php
app/Services/PublicSiteService.php
app/Services/RoleService.php
app/Services/SessionService.php
app/Services/SettingService.php
app/Services/StudentService.php
app/Services/TestimonialService.php
app/Services/TranslationService.php
app/Services/UserService.php
```

### 3. Repositories (12 files)
```
app/Repositories/Eloquent/AttendanceRepository.php
app/Repositories/Eloquent/CategoryRepository.php
app/Repositories/Eloquent/ContactMessageRepository.php
app/Repositories/Eloquent/CourseRepository.php
app/Repositories/Eloquent/CourseReviewRepository.php
app/Repositories/Eloquent/EnrollmentRepository.php
app/Repositories/Eloquent/RoleRepository.php
app/Repositories/Eloquent/SessionRepository.php
app/Repositories/Eloquent/SettingRepository.php
app/Repositories/Eloquent/TestimonialRepository.php
app/Repositories/Eloquent/UserRepository.php
```

### 4. Repository Interfaces (11 files)
```
app/Repositories/Contracts/AttendanceRepositoryInterface.php
app/Repositories/Contracts/CategoryRepositoryInterface.php
app/Repositories/Contracts/ContactMessageRepositoryInterface.php
app/Repositories/Contracts/CourseRepositoryInterface.php
app/Repositories/Contracts/CourseReviewRepositoryInterface.php
app/Repositories/Contracts/EnrollmentRepositoryInterface.php
app/Repositories/Contracts/RoleRepositoryInterface.php
app/Repositories/Contracts/SessionRepositoryInterface.php
app/Repositories/Contracts/SettingRepositoryInterface.php
app/Repositories/Contracts/TestimonialRepositoryInterface.php
app/Repositories/Contracts/UserRepositoryInterface.php
```

### 5. Models (14 files)
```
app/Models/ApplicationLog.php
app/Models/Attendance.php
app/Models/Category.php
app/Models/ContactMessage.php
app/Models/Course.php
app/Models/CourseReview.php
app/Models/Enrollment.php
app/Models/Permission.php
app/Models/Role.php
app/Models/Session.php
app/Models/Setting.php
app/Models/Testimonial.php
app/Models/Translation.php
app/Models/User.php
```

### 6. Resources (11 files)
```
app/Http/Resources/AttendanceResource.php
app/Http/Resources/CategoryResource.php
app/Http/Resources/ContactMessageResource.php
app/Http/Resources/CourseResource.php
app/Http/Resources/CourseReviewResource.php
app/Http/Resources/EnrollmentResource.php
app/Http/Resources/RoleResource.php
app/Http/Resources/SessionResource.php
app/Http/Resources/TestimonialResource.php
app/Http/Resources/TranslationResource.php
app/Http/Resources/UserResource.php
```

### 7. Requests (42 files)
```
app/Http/Requests/Admin/* (24 files)
app/Http/Requests/Auth/* (3 files)
app/Http/Requests/Instructor/* (6 files)
app/Http/Requests/Public/* (1 file)
app/Http/Requests/Student/* (8 files)
```

---

## ✅ الملفات التي يجب الاحتفاظ بها

### Shared Infrastructure
- ✅ `app/Contracts/` - Shared Interfaces
- ✅ `app/Support/` - Shared Support Classes
- ✅ `app/Http/Responses/ApiResponse.php` - Unified Response
- ✅ `app/Exceptions/Handler.php` - Global Exception Handler
- ✅ `app/Models/Version.php` - Version Model
- ✅ `app/Repositories/Contracts/BaseRepositoryInterface.php` - Base Interface
- ✅ `app/Repositories/Eloquent/BaseRepository.php` - Base Repository
- ✅ `app/Http/Controllers/Controller.php` - Laravel Base Controller
- ✅ `app/Http/Controllers/HealthController.php` - Health Check Controller

---

## 🗑️ خطوات الحذف

### PowerShell Script:
```powershell
# Controllers
Remove-Item -Path "app/Http/Controllers/Admin" -Recurse -Force
Remove-Item -Path "app/Http/Controllers/AuthController.php" -Force
Remove-Item -Path "app/Http/Controllers/StudentController.php" -Force
Remove-Item -Path "app/Http/Controllers/InstructorController.php" -Force
Remove-Item -Path "app/Http/Controllers/PublicController.php" -Force
Remove-Item -Path "app/Http/Controllers/LanguageController.php" -Force

# Services
Remove-Item -Path "app/Services" -Recurse -Force

# Repositories
Remove-Item -Path "app/Repositories/Eloquent" -Recurse -Force
Remove-Item -Path "app/Repositories/Contracts" -Recurse -Force
# Keep BaseRepositoryInterface.php manually

# Models
Get-ChildItem -Path "app/Models" -Exclude "Version.php" | Remove-Item -Force

# Resources
Remove-Item -Path "app/Http/Resources" -Recurse -Force

# Requests
Remove-Item -Path "app/Http/Requests/Admin" -Recurse -Force
Remove-Item -Path "app/Http/Requests/Auth" -Recurse -Force
Remove-Item -Path "app/Http/Requests/Instructor" -Recurse -Force
Remove-Item -Path "app/Http/Requests/Public" -Recurse -Force
Remove-Item -Path "app/Http/Requests/Student" -Recurse -Force
```

---

**⚠️ تحذير:** تأكد من عمل Backup قبل الحذف!

**Status: Ready for Cleanup (after backup)**

