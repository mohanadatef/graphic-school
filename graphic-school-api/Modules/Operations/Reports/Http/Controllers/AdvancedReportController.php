<?php

namespace Modules\Operations\Reports\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\Operations\Reports\Services\AdvancedReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CHANGE-007: Advanced Reports & Analytics
 */
class AdvancedReportController extends BaseController
{
    public function __construct(
        private AdvancedReportService $reportService
    ) {}

    /**
     * Get top students by grades
     */
    public function topStudentsByGrades(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 10);
        $report = $this->reportService->topStudentsByGrades($request->all(), $limit);

        return $this->success($report, 'Top students by grades retrieved successfully');
    }

    /**
     * Get top students by attendance
     */
    public function topStudentsByAttendance(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 10);
        $report = $this->reportService->topStudentsByAttendance($request->all(), $limit);

        return $this->success($report, 'Top students by attendance retrieved successfully');
    }

    /**
     * Get top students by engagement
     */
    public function topStudentsByEngagement(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 10);
        $report = $this->reportService->topStudentsByEngagement($request->all(), $limit);

        return $this->success($report, 'Top students by engagement retrieved successfully');
    }

    /**
     * Get average grades by course
     */
    public function averageGradesByCourse(Request $request): JsonResponse
    {
        $report = $this->reportService->averageGradesByCourse($request->all());

        return $this->success($report, 'Average grades by course retrieved successfully');
    }

    /**
     * Get average grades by batch
     */
    public function averageGradesByBatch(Request $request): JsonResponse
    {
        $report = $this->reportService->averageGradesByBatch($request->all());

        return $this->success($report, 'Average grades by batch retrieved successfully');
    }

    /**
     * Get average grades by instructor
     */
    public function averageGradesByInstructor(Request $request): JsonResponse
    {
        $report = $this->reportService->averageGradesByInstructor($request->all());

        return $this->success($report, 'Average grades by instructor retrieved successfully');
    }

    /**
     * Get attendance rate by course
     */
    public function attendanceRateByCourse(Request $request): JsonResponse
    {
        $report = $this->reportService->attendanceRateByCourse($request->all());

        return $this->success($report, 'Attendance rate by course retrieved successfully');
    }

    /**
     * Get attendance rate by student
     */
    public function attendanceRateByStudent(Request $request): JsonResponse
    {
        $report = $this->reportService->attendanceRateByStudent($request->all());

        return $this->success($report, 'Attendance rate by student retrieved successfully');
    }

    /**
     * Get engagement quality metrics
     */
    public function engagementQuality(Request $request): JsonResponse
    {
        $report = $this->reportService->engagementQuality($request->all());

        return $this->success($report, 'Engagement quality metrics retrieved successfully');
    }

    /**
     * Get instructor performance (for instructors)
     */
    public function instructorPerformance(Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;
        $report = $this->reportService->instructorPerformance($instructorId, $request->all());

        return $this->success($report, 'Instructor performance retrieved successfully');
    }

    /**
     * Get instructor performance by ID (for admin)
     */
    public function instructorPerformanceById(Request $request, int $instructorId): JsonResponse
    {
        $report = $this->reportService->instructorPerformance($instructorId, $request->all());

        return $this->success($report, 'Instructor performance retrieved successfully');
    }
}

