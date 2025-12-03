<?php

use Modules\CMS\PublicSite\Http\Controllers\PublicController;
use Modules\ACL\Auth\Http\Controllers\AuthController;
use Modules\Core\Localization\Http\Controllers\LanguageController;
use Modules\ACL\Users\Http\Controllers\StudentController;
use Modules\ACL\Users\Http\Controllers\InstructorController;
use Modules\Operations\Dashboard\Http\Controllers\DashboardController;
use Modules\ACL\Users\Http\Controllers\UserController;
use Modules\ACL\Roles\Http\Controllers\RoleController;
use Modules\LMS\Categories\Http\Controllers\CategoryController;
use Modules\LMS\Courses\Http\Controllers\CourseController;
use Modules\LMS\Sessions\Http\Controllers\SessionController;
use Modules\LMS\Enrollments\Http\Controllers\EnrollmentController;
use Modules\LMS\Attendance\Http\Controllers\AttendanceController;
use Modules\CMS\Settings\Http\Controllers\SettingController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// API Documentation
Route::get('/docs', [\App\Http\Controllers\DocsController::class, 'index']);
Route::get('/docs-json', [\App\Http\Controllers\DocsController::class, 'json']);
Route::get('/docs-yaml', [\App\Http\Controllers\DocsController::class, 'yaml']);

// Health check
Route::get('/health', [HealthController::class, 'check']);

// File Storage routes (loaded from module)
// POST /api/files/upload
// DELETE /api/files/delete

// Notification routes (loaded from module)
// POST /api/notifications/send

// Export routes removed - ExportImport module deleted

// Public branding endpoint (for frontend initialization)
Route::get('/branding/frontend', function () {
    $settings = \App\Models\WebsiteSetting::getDefault();
    return response()->json([
        'success' => true,
        'data' => $settings->getPublicSettings(),
    ]);
});

// Setup Wizard removed

// Public routes
Route::get('/home', [PublicController::class, 'homeSummary']);
Route::get('/courses', [PublicController::class, 'courses']);
Route::get('/courses/{course}', [PublicController::class, 'courseShow']);
Route::get('/categories', [PublicController::class, 'categories']);

// Public routes
Route::get('/instructors', [PublicController::class, 'instructors']);
Route::get('/instructors/{instructor}', [PublicController::class, 'instructorShow']);
Route::get('/settings', [PublicController::class, 'settings']);
Route::post('/contact', [PublicController::class, 'contact']);

// Phase 3: Public Enrollment
Route::post('/enroll', [\App\Http\Controllers\Public\EnrollmentController::class, 'enroll']);


// Auth routes with strict rate limiting to prevent brute force attacks
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute

// Language routes
Route::get('/locale', [LanguageController::class, 'getLocale']);
Route::get('/locales', [LanguageController::class, 'getAvailableLocales']); // Get all available locales
Route::post('/locale/{locale}', [LanguageController::class, 'setLocale']);
Route::get('/translations', [LanguageController::class, 'getTranslations']);
Route::get('/translations/{group}', [LanguageController::class, 'getTranslations']);

// Public settings endpoint (for frontend pages like About)
Route::get('/settings', function () {
    try {
        $settings = \App\Models\WebsiteSetting::getDefault();
        $contact = $settings->contact_settings ?? [];
        $general = $settings->general_info ?? [];
        $branding = $settings->branding ?? [];
        
        return response()->json([
            'site_name' => $general['site_name'] ?? 'Graphic School',
            'about_us' => $general['description'] ?? null,
            'email' => $contact['email'] ?? null,
            'phone' => $contact['phone'] ?? null,
            'address' => $contact['address'] ?? null,
            'logo' => $branding['logo'] ?? null,
            'primary_color' => $branding['primary_color'] ?? '#3b82f6',
            'secondary_color' => $branding['secondary_color'] ?? '#6366f1',
        ]);
    } catch (\Exception $e) {
        // Return default values if settings cannot be loaded
        \Log::error('Error loading settings: ' . $e->getMessage());
        return response()->json([
            'site_name' => 'Graphic School',
            'about_us' => null,
            'email' => null,
            'phone' => null,
            'address' => null,
            'logo' => null,
            'primary_color' => '#3b82f6',
            'secondary_color' => '#6366f1',
        ], 200); // Still return 200 to avoid frontend errors
    }
});

// Public pages
Route::get('/public/pages/{slug}', [\App\Http\Controllers\Public\PageController::class, 'show']);

// Public courses
Route::get('/public/courses', [\Modules\CMS\PublicSite\Http\Controllers\PublicController::class, 'courses']);
Route::get('/public/courses/{course}', [\Modules\CMS\PublicSite\Http\Controllers\PublicController::class, 'courseShow']);

// Public instructors
Route::get('/public/instructors', [\Modules\CMS\PublicSite\Http\Controllers\PublicController::class, 'instructors']);
Route::get('/public/instructors/{instructor}', [\Modules\CMS\PublicSite\Http\Controllers\PublicController::class, 'instructorShow']);

// Public contact form
Route::post('/public/contact', [\App\Http\Controllers\Public\ContactController::class, 'send']);

// Public certificate verification
Route::get('/certificates/verify/{code}', [\App\Http\Controllers\Public\CertificateController::class, 'verify']);

// HQ Admin Routes removed - will be rebuilt later

Route::middleware('auth:sanctum')->group(function () {
    // Get current authenticated user
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        
        // Ensure role is loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        // Get role as string
        $role = $user->role ? $user->role->name : null;
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role,
            'role_name' => $role,
        ]);
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // Student routes
    Route::prefix('student')->middleware('role:student')->group(function () {
        // New student endpoints using new domain models
        Route::get('/my-courses', [\App\Http\Controllers\Student\StudentController::class, 'myCourses']);
        Route::get('/my-group', [\App\Http\Controllers\Student\StudentController::class, 'myGroup']);
        Route::get('/my-sessions', [\App\Http\Controllers\Student\StudentController::class, 'mySessions']);
        Route::get('/attendance-history', [\App\Http\Controllers\Student\StudentController::class, 'attendanceHistory']);
        Route::get('/profile', [\App\Http\Controllers\Student\StudentController::class, 'profile']);

        // Legacy endpoints (keep for backward compatibility)
        Route::get('/courses', [StudentController::class, 'myCourses']);
        Route::get('/courses/{course}/sessions', [StudentController::class, 'courseSessions']);
        Route::get('/courses/{course}/attendance', [StudentController::class, 'courseAttendance']);
        Route::get('/sessions', [StudentController::class, 'sessions']);
        Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll']);
        Route::post('/courses/{course}/review', [StudentController::class, 'reviewCourse']);
        Route::get('/profile', [StudentController::class, 'profile']);
        Route::post('/profile', [StudentController::class, 'updateProfile']);
        
        // Enrollment
        Route::post('/enroll', [\App\Http\Controllers\Student\EnrollmentController::class, 'enroll']);
        Route::get('/enrollments', [\App\Http\Controllers\Student\EnrollmentController::class, 'index']);
        
        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Student\AttendanceController::class, 'index']);
        
        // Certificates
        Route::get('/certificates', [\App\Http\Controllers\Student\CertificateController::class, 'index']);
        Route::get('/certificates/{id}', [\App\Http\Controllers\Student\CertificateController::class, 'show']);
        
    });

    // Notifications (for all authenticated users)
    // Routes loaded from Modules/Core/Notification/Presentation/Routes/api.php


    // Instructor routes
    Route::prefix('instructor')->middleware('role:instructor')->group(function () {
        // New instructor endpoints using new domain models
        Route::get('/my-groups', [\App\Http\Controllers\Instructor\InstructorController::class, 'myGroups']);
        Route::get('/groups/{groupId}/sessions', [\App\Http\Controllers\Instructor\InstructorController::class, 'groupSessions']);
        Route::get('/groups/{groupId}/students', [\App\Http\Controllers\Instructor\InstructorController::class, 'groupStudents']);
        Route::get('/sessions/{sessionId}/attendance', [\App\Http\Controllers\Instructor\InstructorController::class, 'sessionAttendance']);
        Route::post('/sessions/{sessionId}/attendance', [\App\Http\Controllers\Instructor\InstructorController::class, 'takeAttendance']);

        // Legacy endpoints (keep for backward compatibility)
        Route::get('/courses', [InstructorController::class, 'myCourses']);
        Route::get('/courses/{course}/sessions', [InstructorController::class, 'courseSessions']);
        Route::get('/sessions', [InstructorController::class, 'sessions']);
        Route::post('/attendance', [InstructorController::class, 'storeAttendance']);
        Route::get('/attendance/{session}', [InstructorController::class, 'sessionAttendance']);
        Route::post('/sessions/{session}/note', [InstructorController::class, 'sessionNote']);
        
        // Phase 3: Attendance
        Route::get('/sessions', [\App\Http\Controllers\Instructor\AttendanceController::class, 'sessions']);
        Route::get('/sessions/{sessionId}/attendance', [\App\Http\Controllers\Instructor\AttendanceController::class, 'attendance']);
        Route::post('/sessions/{sessionId}/attendance/update', [\App\Http\Controllers\Instructor\AttendanceController::class, 'updateAttendance']);
        
        
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', DashboardController::class);


        // System Settings (Language, Currency, etc.)
        Route::prefix('system-settings')->group(function () {
            Route::get('/', [\Modules\CMS\Settings\Http\Controllers\SystemSettingController::class, 'index']);
            Route::get('/public', [\Modules\CMS\Settings\Http\Controllers\SystemSettingController::class, 'getPublic']);
            Route::get('/group/{group}', [\Modules\CMS\Settings\Http\Controllers\SystemSettingController::class, 'getByGroup']);
            Route::put('/', [\Modules\CMS\Settings\Http\Controllers\SystemSettingController::class, 'update']);
        });

        // Setup Wizard
        Route::prefix('setup')->group(function () {
            Route::get('/status', [\App\Http\Controllers\Admin\SetupWizardController::class, 'status']);
            Route::post('/save-step/{step}', [\App\Http\Controllers\Admin\SetupWizardController::class, 'saveStep']);
            Route::post('/activate-default', [\App\Http\Controllers\Admin\SetupWizardController::class, 'activateDefault']);
            Route::post('/complete', [\App\Http\Controllers\Admin\SetupWizardController::class, 'complete']);
            Route::post('/reset', [\App\Http\Controllers\Admin\SetupWizardController::class, 'resetToDefault']);
            Route::post('/test-email', [\App\Http\Controllers\Admin\SetupWizardController::class, 'testEmail']);
        });

        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('courses', CourseController::class);
        Route::post('/courses/{course}/assign-instructors', [CourseController::class, 'assignInstructors']);
        Route::post('/courses/{course}/sessions/generate', [CourseController::class, 'generateSessions']);

        Route::apiResource('sessions', SessionController::class)->only(['index', 'show', 'update', 'destroy']);

        Route::get('/enrollments', [EnrollmentController::class, 'index']);
        Route::post('/enrollments', [EnrollmentController::class, 'store']);
        Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update']);

        // Enrollment management
        Route::post('/enrollments/{id}/approve', [\App\Http\Controllers\Admin\EnrollmentController::class, 'approve']);
        Route::post('/enrollments/{id}/reject', [\App\Http\Controllers\Admin\EnrollmentController::class, 'reject']);
        Route::post('/enrollments/{id}/withdraw', [\App\Http\Controllers\Admin\EnrollmentController::class, 'withdraw']);

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index']);

        // Certificates
        Route::apiResource('certificates', \App\Http\Controllers\Admin\CertificateController::class);

        // CMS Pages
        Route::apiResource('pages', \App\Http\Controllers\Admin\PageController::class);
        Route::get('/pages/slug/{slug}', [\App\Http\Controllers\Admin\PageController::class, 'showBySlug']);
        Route::put('/pages/{id}/blocks', [\App\Http\Controllers\Admin\PageController::class, 'updateBlocks']);

        // Groups Management
        Route::apiResource('groups', \App\Http\Controllers\Admin\GroupController::class);

        // Languages CRUD
        Route::apiResource('languages', \App\Http\Controllers\Admin\LanguageController::class);
        Route::get('/languages/active', [\App\Http\Controllers\Admin\LanguageController::class, 'active']);

        // Currencies CRUD
        Route::apiResource('currencies', \App\Http\Controllers\Admin\CurrencyController::class);
        Route::get('/currencies/active', [\App\Http\Controllers\Admin\CurrencyController::class, 'active']);

        // Countries CRUD
        Route::apiResource('countries', \App\Http\Controllers\Admin\CountryController::class);
        Route::get('/countries/active', [\App\Http\Controllers\Admin\CountryController::class, 'active']);

        Route::get('/settings', [SettingController::class, 'index']);
        Route::post('/settings', [SettingController::class, 'update']);


    });
});
