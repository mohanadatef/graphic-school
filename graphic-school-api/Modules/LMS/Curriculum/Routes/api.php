<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Curriculum\Http\Controllers\CurriculumController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin,instructor'])->prefix('admin')->group(function () {
        Route::get('/courses/{courseId}/curriculum', [CurriculumController::class, 'getCourseCurriculum']);
        Route::post('/modules', [CurriculumController::class, 'storeModule']);
        Route::put('/modules/{moduleId}', [CurriculumController::class, 'updateModule']);
        Route::delete('/modules/{moduleId}', [CurriculumController::class, 'deleteModule']);
        Route::post('/lessons', [CurriculumController::class, 'storeLesson']);
        Route::put('/lessons/{lessonId}', [CurriculumController::class, 'updateLesson']);
        Route::delete('/lessons/{lessonId}', [CurriculumController::class, 'deleteLesson']);
        Route::post('/resources', [CurriculumController::class, 'storeResource']);
        Route::put('/resources/{resourceId}', [CurriculumController::class, 'updateResource']);
        Route::delete('/resources/{resourceId}', [CurriculumController::class, 'deleteResource']);
    });

    // Student routes
    Route::prefix('student')->group(function () {
        Route::get('/courses/{courseId}/curriculum', [CurriculumController::class, 'getCourseCurriculum']);
    });
});

