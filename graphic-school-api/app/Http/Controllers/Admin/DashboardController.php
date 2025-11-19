<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dashboard\DashboardFilterRequest;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function __invoke(DashboardFilterRequest $request)
    {
        return response()->json(
            $this->dashboardService->getDashboardData($request->validated())
        );
    }
}
