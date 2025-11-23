<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Assessments\Http\Controllers\QuizController;

Route::middleware(['auth:api'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin,instructor'])->prefix('admin')->group(function () {
        Route::post('/quizzes', [QuizController::class, 'store']);
        Route::put('/quizzes/{quizId}', [QuizController::class, 'update']);
    });

    // Student routes
    Route::prefix('student')->group(function () {
        Route::get('/quizzes', [QuizController::class, 'getStudentQuizzes']);
        Route::get('/quizzes/{quizId}', [QuizController::class, 'show']);
        Route::post('/quizzes/{quizId}/submit', [QuizController::class, 'submit']);
        Route::get('/quizzes/{quizId}/attempts', [QuizController::class, 'getAttempts']);
        Route::get('/projects', [\Modules\LMS\Assessments\Http\Controllers\ProjectController::class, 'index']);
        Route::post('/projects', [\Modules\LMS\Assessments\Http\Controllers\ProjectController::class, 'store']);
        Route::get('/projects/{projectId}', [\Modules\LMS\Assessments\Http\Controllers\ProjectController::class, 'show']);
    });
});

