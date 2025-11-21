<?php

namespace Modules\Operations\Analytics\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Operations\Analytics\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    /**
     * Get course visits
     */
    public function getCourseVisits(Request $request): JsonResponse
    {
        $courseId = $request->input('course_id');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $visits = $this->analyticsService->getCourseVisits($courseId, $startDate, $endDate);

        return response()->json($visits);
    }

    /**
     * Get instructor visits
     */
    public function getInstructorVisits(Request $request): JsonResponse
    {
        $instructorId = $request->input('instructor_id');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $visits = $this->analyticsService->getInstructorVisits($instructorId, $startDate, $endDate);

        return response()->json($visits);
    }

    /**
     * Get overview statistics
     */
    public function getOverview(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $overview = $this->analyticsService->getOverview($startDate, $endDate);

        return response()->json($overview);
    }

    /**
     * Get daily trend
     */
    public function getDailyTrend(Request $request): JsonResponse
    {
        $days = $request->integer('days', 30);
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $trend = $this->analyticsService->getDailyTrend($startDate, $endDate, $days);

        return response()->json($trend);
    }
}

