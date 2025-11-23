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

// Export routes (loaded from module)
// POST /api/export

// Public branding endpoint (for frontend initialization)
Route::get('/branding/frontend', [\App\Http\Controllers\Admin\BrandingController::class, 'frontend']);

// Public setup status (for frontend routing)
Route::get('/setup/status', [\App\Http\Controllers\Admin\SetupWizardController::class, 'status']);

// Public routes
Route::get('/home', [PublicController::class, 'homeSummary']);
Route::get('/courses', [PublicController::class, 'courses']);
Route::get('/courses/{course}', [PublicController::class, 'courseShow']);
Route::get('/categories', [PublicController::class, 'categories']);

// Phase 6: Public Page Builder Routes
Route::get('/p/{academy_slug}/{page_slug}', [\App\Http\Controllers\Public\PageRendererController::class, 'render']);
Route::get('/instructors', [PublicController::class, 'instructors']);
Route::get('/instructors/{instructor}', [PublicController::class, 'instructorShow']);
Route::get('/settings', [PublicController::class, 'settings']);
Route::get('/sliders', [PublicController::class, 'sliders']);
Route::get('/testimonials', [PublicController::class, 'testimonials']);
Route::post('/contact', [PublicController::class, 'contact']);

// CHANGE-002: CMS Public Pages
Route::get('/pages/{slug}', [\App\Http\Controllers\PageController::class, 'show']);

// Phase 2: Public Program endpoints
Route::get('/programs', [\App\Http\Controllers\ProgramController::class, 'index']);
Route::get('/programs/{slug}', [\App\Http\Controllers\ProgramController::class, 'show']);
Route::get('/programs/{slug}/batches', [\App\Http\Controllers\ProgramController::class, 'batches']);
Route::get('/groups/{id}/sessions', [\App\Http\Controllers\GroupController::class, 'sessions']);
Route::get('/faqs', [\App\Http\Controllers\FAQController::class, 'index']);

// Phase 3: Public Enrollment
Route::post('/enroll', [\App\Http\Controllers\Public\EnrollmentController::class, 'enroll']);

// Phase 3: Public Certificate Verification
Route::get('/certificates/verify', [\App\Http\Controllers\Public\CertificateController::class, 'verify']);

// Phase 4: Public QR Code (for display)
Route::get('/qr/{token}', function ($token) {
    // Return QR code image
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($token);
    return redirect($qrUrl);
})->name('qr.display');

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

// Phase 5.3: HQ Admin Routes (Super Admin)
Route::middleware(['auth:api', 'role:hq'])->prefix('hq')->group(function () {
    Route::prefix('plans')->group(function () {
        Route::get('/', [\App\Http\Controllers\HQ\SubscriptionPlanController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\HQ\SubscriptionPlanController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\HQ\SubscriptionPlanController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\HQ\SubscriptionPlanController::class, 'destroy']);
    });
    
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [\App\Http\Controllers\HQ\SubscriptionController::class, 'index']);
        Route::put('/{id}/suspend', [\App\Http\Controllers\HQ\SubscriptionController::class, 'suspend']);
        Route::put('/{id}/resume', [\App\Http\Controllers\HQ\SubscriptionController::class, 'resume']);
        Route::get('/{id}/usage', [\App\Http\Controllers\HQ\SubscriptionController::class, 'usage']);
    });
});

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
        
        // CHANGE-004: Payment Timeline
        Route::get('/payments', [\App\Http\Controllers\PaymentController::class, 'studentPayments']);
        
        // Phase 3: Enrollment
        Route::post('/enroll', [\App\Http\Controllers\Student\EnrollmentController::class, 'enroll']);
        Route::get('/enrollments', [\App\Http\Controllers\Student\EnrollmentController::class, 'index']);
        
        // Phase 3: Payments
        Route::get('/invoices', [\App\Http\Controllers\Student\PaymentController::class, 'invoices']);
        Route::get('/invoices/{id}', [\App\Http\Controllers\Student\PaymentController::class, 'showInvoice']);
        Route::post('/invoices/pay', [\App\Http\Controllers\Student\PaymentController::class, 'pay']);
        
        // Phase 3: Attendance
        Route::get('/attendance', [\App\Http\Controllers\Student\AttendanceController::class, 'index']);
        
        // Phase 3: Certificates
        Route::get('/certificates', [\App\Http\Controllers\Student\CertificateController::class, 'index']);
        Route::get('/certificates/{id}/download', [\App\Http\Controllers\Student\CertificateController::class, 'download']);
        
        // Phase 4: QR Attendance
        Route::post('/qr-checkin', [\App\Http\Controllers\Student\QrAttendanceController::class, 'checkIn']);
        
        // Phase 4: Assignments
        Route::get('/assignments', [\App\Http\Controllers\Student\AssignmentController::class, 'index']);
        Route::get('/assignments/{id}', [\App\Http\Controllers\Student\AssignmentController::class, 'show']);
        Route::post('/assignments/{id}/submit', [\App\Http\Controllers\Student\AssignmentController::class, 'submit']);
        
        // Phase 4: Gradebook
        Route::get('/gradebook', [\App\Http\Controllers\Student\GradebookController::class, 'index']);
        
        // Phase 5.2: Community
        Route::prefix('community')->group(function () {
            Route::get('/posts', [\App\Http\Controllers\CommunityController::class, 'index']);
            Route::get('/posts/trending', [\App\Http\Controllers\CommunityController::class, 'trending']);
            Route::get('/posts/my-posts', [\App\Http\Controllers\CommunityController::class, 'myPosts']);
            Route::get('/posts/{id}', [\App\Http\Controllers\CommunityController::class, 'show']);
            Route::post('/posts', [\App\Http\Controllers\CommunityController::class, 'store']);
            Route::post('/comments', [\App\Http\Controllers\CommunityController::class, 'createComment']);
            Route::post('/replies', [\App\Http\Controllers\CommunityController::class, 'createReply']);
            Route::post('/like', [\App\Http\Controllers\CommunityController::class, 'toggleLike']);
            Route::post('/report', [\App\Http\Controllers\CommunityController::class, 'report']);
        });
        
        // Phase 5.1: Gamification
        Route::prefix('gamification')->group(function () {
            Route::get('/summary', [\App\Http\Controllers\Student\GamificationController::class, 'summary']);
            Route::get('/events', [\App\Http\Controllers\Student\GamificationController::class, 'events']);
            Route::get('/leaderboard', [\App\Http\Controllers\Student\GamificationController::class, 'leaderboard']);
        });
    });

    // Notifications (for all authenticated users)
    // Routes loaded from Modules/Core/Notification/Presentation/Routes/api.php

    // Messaging (Student ⇄ Instructor)
    Route::prefix('messaging')->group(function () {
        Route::get('/conversations', [\App\Http\Controllers\MessagingController::class, 'conversations']);
        Route::post('/conversations', [\App\Http\Controllers\MessagingController::class, 'getOrCreateConversation']);
        Route::get('/conversations/{id}/messages', [\App\Http\Controllers\MessagingController::class, 'messages']);
        Route::post('/messages', [\App\Http\Controllers\MessagingController::class, 'sendMessage']);
        Route::put('/conversations/{id}/archive', [\App\Http\Controllers\MessagingController::class, 'archive']);
    });

    // Instructor routes
    Route::prefix('instructor')->middleware('role:instructor')->group(function () {
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
        
        // Phase 4: QR Attendance
        Route::post('/sessions/{sessionId}/qr-generate', [\App\Http\Controllers\Instructor\QrAttendanceController::class, 'generateQr']);
        
        // Phase 4: Assignments
        Route::get('/assignments', [\App\Http\Controllers\Instructor\AssignmentController::class, 'index']);
        Route::post('/assignments', [\App\Http\Controllers\Instructor\AssignmentController::class, 'store']);
        Route::get('/assignments/{assignmentId}/submissions', [\App\Http\Controllers\Instructor\AssignmentController::class, 'submissions']);
        Route::post('/submissions/{submissionId}/grade', [\App\Http\Controllers\Instructor\AssignmentController::class, 'gradeSubmission']);
        
        // Phase 4: Gradebook
        Route::get('/groups/{groupId}/gradebook', [\App\Http\Controllers\Instructor\GradebookController::class, 'getForGroup']);
        
        // Phase 5.1: Gamification
        Route::prefix('gamification')->group(function () {
            Route::get('/group-leaderboard', [\App\Http\Controllers\Instructor\GamificationController::class, 'groupLeaderboard']);
        });
        
        // Phase 5.2: Community
        Route::prefix('community')->group(function () {
            Route::post('/posts/{id}/pin', [\App\Http\Controllers\Instructor\CommunityController::class, 'pinPost']);
            Route::post('/posts/{id}/unpin', [\App\Http\Controllers\Instructor\CommunityController::class, 'unpinPost']);
        });
        
        // CHANGE-007: Advanced Reports for Instructors
        Route::get('/reports/performance', [\Modules\Operations\Reports\Http\Controllers\AdvancedReportController::class, 'instructorPerformance']);
    });

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', DashboardController::class);

        // Branding management
        Route::prefix('branding')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BrandingController::class, 'index']);
            Route::post('/update', [\App\Http\Controllers\Admin\BrandingController::class, 'update']);
        });

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

        // Phase 3: Enrollment management
        Route::post('/enrollments/{id}/approve', [\App\Http\Controllers\Admin\EnrollmentController::class, 'approve']);
        Route::post('/enrollments/{id}/reject', [\App\Http\Controllers\Admin\EnrollmentController::class, 'reject']);
        Route::post('/enrollments/{id}/withdraw', [\App\Http\Controllers\Admin\EnrollmentController::class, 'withdraw']);
        Route::get('/enrollments/{id}/logs', [\App\Http\Controllers\Admin\EnrollmentLogController::class, 'index']);
        
        // Phase 3: Invoices
        Route::get('/invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index']);
        Route::get('/invoices/{id}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show']);
        Route::post('/invoices/{id}/mark-paid', [\App\Http\Controllers\Admin\InvoiceController::class, 'markPaid']);

        // Phase 3: Attendance
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index']);
        
        // Phase 3: Certificates
        Route::get('/certificates', [\App\Http\Controllers\Admin\CertificateController::class, 'index']);
        Route::post('/certificates/issue', [\App\Http\Controllers\Admin\CertificateController::class, 'issue']);
        
        // Phase 3: Payment Methods
        Route::get('/payment-methods', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'index']);
        
        // Phase 5.1: Gamification Management
        Route::prefix('gamification')->group(function () {
            // Rules
            Route::get('/rules', [\App\Http\Controllers\Admin\GamificationController::class, 'rules']);
            Route::post('/rules', [\App\Http\Controllers\Admin\GamificationController::class, 'createRule']);
            Route::put('/rules/{id}', [\App\Http\Controllers\Admin\GamificationController::class, 'updateRule']);
            Route::delete('/rules/{id}', [\App\Http\Controllers\Admin\GamificationController::class, 'deleteRule']);
            
            // Levels
            Route::get('/levels', [\App\Http\Controllers\Admin\GamificationController::class, 'levels']);
            Route::post('/levels', [\App\Http\Controllers\Admin\GamificationController::class, 'createLevel']);
            Route::put('/levels/{id}', [\App\Http\Controllers\Admin\GamificationController::class, 'updateLevel']);
            Route::delete('/levels/{id}', [\App\Http\Controllers\Admin\GamificationController::class, 'deleteLevel']);
            
            // Badges
            Route::get('/badges', [\App\Http\Controllers\Admin\GamificationController::class, 'badges']);
            Route::post('/badges', [\App\Http\Controllers\Admin\GamificationController::class, 'createBadge']);
            Route::put('/badges/{id}', [\App\Http\Controllers\Admin\GamificationController::class, 'updateBadge']);
            Route::delete('/badges/{id}', [\App\Http\Controllers\Admin\GamificationController::class, 'deleteBadge']);
        });
        
        // Phase 5.2: Community Management
        Route::prefix('community')->group(function () {
            Route::get('/posts', [\App\Http\Controllers\Admin\CommunityController::class, 'posts']);
            Route::put('/posts/{id}/pin', [\App\Http\Controllers\Admin\CommunityController::class, 'togglePin']);
            Route::put('/posts/{id}/lock', [\App\Http\Controllers\Admin\CommunityController::class, 'toggleLock']);
            Route::delete('/posts/{id}', [\App\Http\Controllers\Admin\CommunityController::class, 'deletePost']);
            Route::get('/reports', [\App\Http\Controllers\Admin\CommunityController::class, 'reports']);
            Route::put('/reports/{id}/resolve', [\App\Http\Controllers\Admin\CommunityController::class, 'resolveReport']);
        });
        
        // Phase 5.3: Subscriptions (Academy Admin)
        Route::prefix('academy')->group(function () {
            Route::get('/subscription', [\App\Http\Controllers\Academy\SubscriptionController::class, 'index']);
            Route::get('/subscription/usage', [\App\Http\Controllers\Academy\SubscriptionController::class, 'usage']);
            Route::post('/subscription/change-plan', [\App\Http\Controllers\Academy\SubscriptionController::class, 'changePlan']);
            Route::post('/subscription/cancel', [\App\Http\Controllers\Academy\SubscriptionController::class, 'cancel']);
            Route::post('/subscription/renew', [\App\Http\Controllers\Academy\SubscriptionController::class, 'renew']);
            Route::get('/subscription/invoices', [\App\Http\Controllers\Academy\SubscriptionController::class, 'invoices']);
        });
        
        // Phase 6: Page Builder (Academy Admin)
        Route::prefix('page-builder')->group(function () {
            Route::get('/pages', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'index']);
            Route::get('/pages/{id}', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'show']);
            Route::post('/pages', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'store']);
            Route::put('/pages/{id}', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'update']);
            Route::delete('/pages/{id}', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'destroy']);
            Route::post('/pages/{id}/structure', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'saveStructure']);
            Route::post('/pages/{id}/publish', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'publish']);
            Route::post('/pages/{id}/duplicate', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'duplicate']);
            Route::get('/templates', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'templates']);
            Route::post('/pages/{id}/apply-template', [\App\Http\Controllers\PageBuilder\PageBuilderController::class, 'applyTemplate']);
        });

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

        // Phase 2: Dynamic Learning Structure
        Route::apiResource('programs', \App\Http\Controllers\Admin\ProgramController::class);
        Route::apiResource('batches', \App\Http\Controllers\Admin\BatchController::class);
        Route::apiResource('groups', \App\Http\Controllers\Admin\GroupController::class);

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
