<?php

use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SessionController as AdminSessionController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\TranslationController as AdminTranslationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/home', [PublicController::class, 'homeSummary']);
Route::get('/courses', [PublicController::class, 'courses']);
Route::get('/courses/{course}', [PublicController::class, 'courseShow']);
Route::get('/categories', [PublicController::class, 'categories']);
Route::get('/instructors', [PublicController::class, 'instructors']);
Route::get('/settings', [PublicController::class, 'settings']);
Route::get('/sliders', [PublicController::class, 'sliders']);
Route::get('/testimonials', [PublicController::class, 'testimonials']);
Route::post('/contact', [PublicController::class, 'contact']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Language routes
Route::get('/locale', [LanguageController::class, 'getLocale']);
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

        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('roles', AdminRoleController::class)->except(['show']);
        Route::apiResource('categories', AdminCategoryController::class)->except(['show']);
        Route::apiResource('courses', AdminCourseController::class);
        Route::post('/courses/{course}/assign-instructors', [AdminCourseController::class, 'assignInstructors']);
        Route::post('/courses/{course}/sessions/generate', [AdminCourseController::class, 'generateSessions']);

        Route::apiResource('sessions', AdminSessionController::class)->only(['index', 'show', 'update', 'destroy']);

        Route::get('/enrollments', [AdminEnrollmentController::class, 'index']);
        Route::post('/enrollments', [AdminEnrollmentController::class, 'store']);
        Route::put('/enrollments/{enrollment}', [AdminEnrollmentController::class, 'update']);

        Route::get('/attendance', [AdminAttendanceController::class, 'index']);

        Route::get('/settings', [AdminSettingController::class, 'index']);
        Route::post('/settings', [AdminSettingController::class, 'update']);

        Route::get('/contacts', [AdminContactController::class, 'index']);
        Route::post('/contacts/{contactMessage}/resolve', [AdminContactController::class, 'resolve']);

        Route::apiResource('sliders', AdminSliderController::class);
        Route::apiResource('testimonials', AdminTestimonialController::class)->only(['index', 'update', 'destroy']);

        Route::apiResource('translations', AdminTranslationController::class);
        Route::get('/translations/groups', [AdminTranslationController::class, 'groups']);
        Route::get('/translations/locales', [AdminTranslationController::class, 'locales']);
        Route::post('/translations/clear-cache', [AdminTranslationController::class, 'clearCache']);

        Route::get('/reports/courses', [ReportController::class, 'courses']);
        Route::get('/reports/instructors', [ReportController::class, 'instructors']);
    });
});
