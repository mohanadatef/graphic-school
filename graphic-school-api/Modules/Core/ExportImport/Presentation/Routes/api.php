<?php

use Modules\Core\ExportImport\Presentation\Http\Controllers\ExportImportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/export', [ExportImportController::class, 'export']);
});

