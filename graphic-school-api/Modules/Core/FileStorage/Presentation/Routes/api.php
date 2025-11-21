<?php

use Modules\Core\FileStorage\Presentation\Http\Controllers\FileStorageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/files/upload', [FileStorageController::class, 'upload']);
    Route::delete('/files/delete', [FileStorageController::class, 'delete']);
});

