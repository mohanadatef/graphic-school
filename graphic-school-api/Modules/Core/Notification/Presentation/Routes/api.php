<?php

use Modules\Core\Notification\Presentation\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/notifications/send', [NotificationController::class, 'send']);
});

