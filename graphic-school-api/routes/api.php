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
use Modules\CMS\Contacts\Http\Controllers\ContactController;
use Modules\CMS\Testimonials\Http\Controllers\TestimonialController;
use Modules\Core\Localization\Http\Controllers\TranslationController;
use Modules\Operations\Reports\Http\Controllers\ReportController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', [HealthController::class, 'check']);

// File Storage routes (loaded from module)
// POST /api/files/upload
// DELETE /api/files/delete

// Notification routes (loaded from module)
// POST /api/notifications/send

// Export routes (loaded from module)
// POST /api/export

// Public routes
Route::get('/home', [PublicController::class, 'homeSummary']);
Route::get('/courses', [PublicController::class, 'courses']);
Route::get('/courses/{course}', [PublicController::class, 'courseShow']);
Route::get('/categories', [PublicController::class, 'categories']);
Route::get('/instructors', [PublicController::class, 'instructors']);
Route::get('/instructors/{instructor}', [PublicController::class, 'instructorShow']);
Route::get('/settings', [PublicController::class, 'settings']);
Route::get('/sliders', [PublicController::class, 'sliders']);
Route::get('/testimonials', [PublicController::class, 'testimonials']);
Route::post('/contact', [PublicController::class, 'contact']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Language routes
Route::get('/locale', [LanguageController::class, 'getLocale']);
Route::get('/locales', [LanguageController::class, 'getAvailableLocales']); // Get all available locales
Route::post('/locale/{locale}', [LanguageController::class, 'setLocale']);
Route::get('/translations', [LanguageController::class, 'getTranslations']);
Route::get('/translations/{group}', [LanguageController::class, 'getTranslations']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Student routes
    Route::prefix('student')->middleware('role:student')->group(function () {
        Route::get('/courses', [StudentController::class, 'myCourses']);
        Route::get('/courses/{course}/sessions', [StudentController::class, 'courseSessions']);
        Route::get('/courses/{course}/attendance', [StudentController::class, 'courseAttendance']);
        Route::get('/sessions', [StudentController::class, 'sessions']);
        Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll']);
        Route::post('/courses/{course}/review', [StudentController::class, 'reviewCourse']);
        Route::get('/profile', [StudentController::class, 'profile']);
        Route::post('/profile', [StudentController::class, 'updateProfile']);
    });

    // Instructor routes
    Route::prefix('instructor')->middleware('role:instructor')->group(function () {
        Route::get('/courses', [InstructorController::class, 'myCourses']);
        Route::get('/courses/{course}/sessions', [InstructorController::class, 'courseSessions']);
        Route::get('/sessions', [InstructorController::class, 'sessions']);
        Route::post('/attendance', [InstructorController::class, 'storeAttendance']);
        Route::get('/attendance/{session}', [InstructorController::class, 'sessionAttendance']);
        Route::post('/sessions/{session}/note', [InstructorController::class, 'sessionNote']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', DashboardController::class);

        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class)->except(['show']);
        Route::apiResource('categories', CategoryController::class)->except(['show']);
        Route::apiResource('courses', CourseController::class);
        Route::post('/courses/{course}/assign-instructors', [CourseController::class, 'assignInstructors']);
        Route::post('/courses/{course}/sessions/generate', [CourseController::class, 'generateSessions']);

        Route::apiResource('sessions', SessionController::class)->only(['index', 'show', 'update', 'destroy']);

        Route::get('/enrollments', [EnrollmentController::class, 'index']);
        Route::post('/enrollments', [EnrollmentController::class, 'store']);
        Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update']);

        Route::get('/attendance', [AttendanceController::class, 'index']);

        Route::get('/settings', [SettingController::class, 'index']);
        Route::post('/settings', [SettingController::class, 'update']);

        Route::get('/contacts', [ContactController::class, 'index']);
        Route::post('/contacts/{contactMessage}/resolve', [ContactController::class, 'resolve']);

        Route::apiResource('testimonials', TestimonialController::class)->only(['index', 'update', 'destroy']);

        Route::apiResource('translations', TranslationController::class);
        Route::get('/translations/groups', [TranslationController::class, 'groups']);
        Route::get('/translations/locales', [TranslationController::class, 'locales']);
        Route::post('/translations/clear-cache', [TranslationController::class, 'clearCache']);

        Route::get('/reports/courses', [ReportController::class, 'courses']);
        Route::get('/reports/instructors', [ReportController::class, 'instructors']);
        Route::get('/reports/financial', [ReportController::class, 'financial']);
    });
});
