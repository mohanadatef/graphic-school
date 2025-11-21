<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Notification\Presentation\Http\Controllers\InAppNotificationController;

/**
 * CHANGE-003: In-App Notifications System & Notification Center
 */
Route::middleware('auth:api')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [InAppNotificationController::class, 'index']);
        Route::get('/unread-count', [InAppNotificationController::class, 'unreadCount']);
        Route::put('/{id}/read', [InAppNotificationController::class, 'markAsRead']);
        Route::put('/read-all', [InAppNotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [InAppNotificationController::class, 'destroy']);
    });
});
