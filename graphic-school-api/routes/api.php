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
use Modules\Operations\Reports\Http\Controllers\StrategicReportController;
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

// CHANGE-002: CMS Public Pages
Route::get('/pages/{slug}', [\App\Http\Controllers\PageController::class, 'show']);
Route::get('/faqs', [\App\Http\Controllers\FAQController::class, 'index']);

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

    // Notifications (for all authenticated users)
    // Routes loaded from Modules/Core/Notification/Presentation/Routes/api.php

    // Messaging (Student ⇄ Instructor)
    Route::prefix('messaging')->middleware('auth:api')->group(function () {
        Route::get('/conversations', [\App\Http\Controllers\MessagingController::class, 'conversations']);
        Route::post('/conversations', [\App\Http\Controllers\MessagingController::class, 'getOrCreateConversation']);
        Route::get('/conversations/{id}/messages', [\App\Http\Controllers\MessagingController::class, 'messages']);
        Route::post('/messages', [\App\Http\Controllers\MessagingController::class, 'sendMessage']);
        Route::put('/conversations/{id}/archive', [\App\Http\Controllers\MessagingController::class, 'archive']);
    });

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
        
        // CHANGE-004: Payment Timeline
        Route::get('/payments', [\App\Http\Controllers\PaymentController::class, 'studentPayments']);
    });

    // Instructor routes
    Route::prefix('instructor')->middleware('role:instructor')->group(function () {
        Route::get('/courses', [InstructorController::class, 'myCourses']);
        Route::get('/courses/{course}/sessions', [InstructorController::class, 'courseSessions']);
        Route::get('/sessions', [InstructorController::class, 'sessions']);
        Route::post('/attendance', [InstructorController::class, 'storeAttendance']);
        Route::get('/attendance/{session}', [InstructorController::class, 'sessionAttendance']);
        Route::post('/sessions/{session}/note', [InstructorController::class, 'sessionNote']);
        
        // CHANGE-007: Advanced Reports for Instructors
        Route::get('/reports/performance', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'instructorPerformance']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', DashboardController::class);

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

        Route::get('/attendance', [AttendanceController::class, 'index']);

        // CHANGE-004: Payment Timeline
        Route::prefix('payments')->group(function () {
            Route::get('/', [\App\Http\Controllers\PaymentController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\PaymentController::class, 'store']);
            Route::put('/{id}', [\App\Http\Controllers\PaymentController::class, 'update']);
            Route::get('/reports', [\App\Http\Controllers\PaymentController::class, 'reports']);
        });

        // CHANGE-002: CMS Page Builder
        Route::apiResource('pages', \App\Http\Controllers\PageController::class);
        Route::get('/pages/{slug}/show', [\App\Http\Controllers\PageController::class, 'show']);

        // CHANGE-002: CMS FAQ
        Route::get('/faqs', [\App\Http\Controllers\FAQController::class, 'adminIndex']);
        Route::post('/faqs', [\App\Http\Controllers\FAQController::class, 'store']);
        Route::put('/faqs/{id}', [\App\Http\Controllers\FAQController::class, 'update']);
        Route::delete('/faqs/{id}', [\App\Http\Controllers\FAQController::class, 'destroy']);

        // CHANGE-002: CMS Media Library
        Route::apiResource('media', \App\Http\Controllers\MediaController::class);

        // CHANGE-006: Ticketing System
        Route::prefix('tickets')->group(function () {
            Route::get('/', [\Modules\Support\Tickets\Http\Controllers\TicketController::class, 'index']);
            Route::post('/', [\Modules\Support\Tickets\Http\Controllers\TicketController::class, 'store']);
            Route::get('/{id}', [\Modules\Support\Tickets\Http\Controllers\TicketController::class, 'show']);
            Route::put('/{id}', [\Modules\Support\Tickets\Http\Controllers\TicketController::class, 'update']);
            Route::post('/{id}/attachments', [\Modules\Support\Tickets\Http\Controllers\TicketController::class, 'uploadAttachment']);
            Route::get('/reports', [\Modules\Support\Tickets\Http\Controllers\TicketController::class, 'reports']);
        });

        // CHANGE-008: Audit Log
        Route::prefix('audit-logs')->group(function () {
            Route::get('/', [\Modules\Operations\Logging\Http\Controllers\AuditLogController::class, 'index']);
            Route::get('/{id}', [\Modules\Operations\Logging\Http\Controllers\AuditLogController::class, 'show']);
            Route::get('/entity/{modelType}/{modelId}', [\Modules\Operations\Logging\Http\Controllers\AuditLogController::class, 'forEntity']);
        });

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
        
        // Strategic Reports for Decision Making
        Route::prefix('reports/strategic')->group(function () {
            Route::get('/performance', [StrategicReportController::class, 'performance']);
            Route::get('/profitability', [StrategicReportController::class, 'profitability']);
            Route::get('/student-analytics', [StrategicReportController::class, 'studentAnalytics']);
            Route::get('/instructor-performance', [StrategicReportController::class, 'instructorPerformance']);
            Route::get('/forecasting', [StrategicReportController::class, 'forecasting']);
        });

        // CHANGE-007: Advanced Reports & Analytics
        Route::prefix('reports/advanced')->group(function () {
            Route::get('/top-students/grades', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'topStudentsByGrades']);
            Route::get('/top-students/attendance', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'topStudentsByAttendance']);
            Route::get('/top-students/engagement', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'topStudentsByEngagement']);
            Route::get('/average-grades/course', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'averageGradesByCourse']);
            Route::get('/average-grades/batch', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'averageGradesByBatch']);
            Route::get('/average-grades/instructor', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'averageGradesByInstructor']);
            Route::get('/attendance-rate/course', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'attendanceRateByCourse']);
            Route::get('/attendance-rate/student', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'attendanceRateByStudent']);
            Route::get('/engagement-quality', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'engagementQuality']);
            Route::get('/instructor-performance/{instructorId}', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'instructorPerformanceById']);
        });
    });
});
