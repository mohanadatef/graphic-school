<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Progress\Http\Controllers\ProgressController;

Route::middleware(['auth:api'])->prefix('student')->group(function () {
    Route::get('/enrollments/{enrollmentId}/progress', [ProgressController::class, 'getProgress']);
    Route::post('/enrollments/{enrollmentId}/lessons/{lessonId}/complete', [ProgressController::class, 'markLessonComplete']);
    Route::put('/enrollments/{enrollmentId}/lessons/{lessonId}/progress', [ProgressController::class, 'updateProgress']);
});

