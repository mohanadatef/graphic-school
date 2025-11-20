<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\Sliders\Http\Controllers\Admin\SliderController;

Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
    Route::apiResource('sliders', SliderController::class);
});