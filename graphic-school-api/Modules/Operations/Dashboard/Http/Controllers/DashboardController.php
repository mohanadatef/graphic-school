<?php

namespace Modules\Operations\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Operations\Dashboard\Http\Requests\DashboardFilterRequest;
use Modules\Operations\Dashboard\Services\DashboardService;

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

