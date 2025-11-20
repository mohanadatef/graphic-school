<?php

namespace App\Http\Controllers;

use App\Support\Health\HealthChecker;
use App\Http\Responses\ApiResponse;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     */
    public function check()
    {
        $health = HealthChecker::check();

        return ApiResponse::success($health, 'Health check completed');
    }
}

