<?php

namespace Modules\Operations\Reports\Services;

use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Enums\EnrollmentPaymentStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\CourseReviews\Models\CourseReview;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Sessions\Enums\SessionStatus;
use Modules\LMS\Attendance\Models\Attendance;
use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Modules\LMS\Courses\Enums\CourseStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Strategic Reports Service
 * Provides high-level business intelligence reports for decision making
 * Optimized for performance with caching and aggregation
 */
class StrategicReportService
{
    /**
     * Report 1: Comprehensive Performance Report (KPIs)
     * Key Performance Indicators for executive decision making
     */
    public function performanceReport(array $filters = []): array
    {
        $cacheKey = 'strategic_report_performance_' . md5(json_encode($filters));
        
        try {
            return Cache::remember($cacheKey, 3600, function () use ($filters) {
                $period = $filters['period'] ?? 'month'; // day, week, month, quarter, year
                $startDate = $this->getStartDate($period);
                
                // Core KPIs using aggregation for performance
                $kpis = [
                    'revenue' => $this->getRevenueMetrics($startDate),
                    'enrollments' => $this->getEnrollmentMetrics($startDate),
                    'courses' => $this->getCourseMetrics($startDate),
                    'students' => $this->getStudentMetrics($startDate),
                    'instructors' => $this->getInstructorMetrics($startDate),
                    'sessions' => $this->getSessionMetrics($startDate),
                    'attendance' => $this->getAttendanceMetrics($startDate),
                    'satisfaction' => $this->getSatisfactionMetrics($startDate),
                ];
            
            // Growth rates (comparing with previous period)
            $previousStartDate = $this->getPreviousPeriodStart($period, $startDate);
            $growth = [
                'revenue_growth' => $this->calculateGrowthRate(
                    (float) $this->getRevenueMetrics($previousStartDate)['total'],
                    (float) $kpis['revenue']['total']
                ),
                'enrollment_growth' => $this->calculateGrowthRate(
                    $this->getEnrollmentMetrics($previousStartDate)['total'],
                    $kpis['enrollments']['total']
                ),
                'student_growth' => $this->calculateGrowthRate(
                    $this->getStudentMetrics($previousStartDate)['new_students'],
                    $kpis['students']['new_students']
                ),
            ];
            
            // Trends (last 6 periods)
            $trends = $this->getTrendsData($period, 6);
            
            // Alerts and recommendations
            $alerts = $this->generateAlerts($kpis, $growth);
            $recommendations = $this->generateRecommendations($kpis, $growth);
            
                return [
                    'period' => $period,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => now()->toDateString(),
                    'kpis' => $kpis,
                    'growth' => $growth,
                    'trends' => $trends,
                    'alerts' => $alerts,
                    'recommendations' => $recommendations,
                    'generated_at' => now()->toDateTimeString(),
                ];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // If it's a connection error, clear cache and throw
            Cache::forget($cacheKey);
            throw $e;
        } catch (\Exception $e) {
            // For other errors, return empty structure
            \Log::error('Error generating performance report: ' . $e->getMessage());
            return [
                'period' => $filters['period'] ?? 'month',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->toDateString(),
                'kpis' => [],
                'growth' => [],
                'trends' => [],
                'alerts' => [],
                'recommendations' => [],
                'generated_at' => now()->toDateTimeString(),
                'error' => config('app.debug') ? $e->getMessage() : 'Error generating report',
            ];
        }
    }
    
    /**
     * Report 2: Profitability Report
     * Financial analysis for revenue optimization
     */
    public function profitabilityReport(array $filters = []): array
    {
        $cacheKey = 'strategic_report_profitability_' . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, 3600, function () use ($filters) {
            $startDate = $filters['start_date'] ?? now()->subMonths(6)->startOfMonth();
            $endDate = $filters['end_date'] ?? now()->endOfMonth();
            
            if (is_string($startDate)) {
                $startDate = Carbon::parse($startDate);
            }
            if (is_string($endDate)) {
                $endDate = Carbon::parse($endDate);
            }
            
            // Build base query with filters
            $enrollmentsQuery = Enrollment::where('status', EnrollmentStatus::APPROVED->value)
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            // Apply category filter
            if (!empty($filters['category_id'])) {
                $enrollmentsQuery->whereHas('course', fn ($q) => $q->where('category_id', $filters['category_id']));
            }
            
            // Apply course filter
            if (!empty($filters['course_id'])) {
                $enrollmentsQuery->where('course_id', $filters['course_id']);
            }
            
            // Revenue analysis
            $revenue = [
                'total' => (clone $enrollmentsQuery)->sum('total_amount') ?? 0,
                'collected' => (clone $enrollmentsQuery)
                    ->where('payment_status', EnrollmentPaymentStatus::PAID->value)
                    ->sum('paid_amount') ?? 0,
                'outstanding' => (clone $enrollmentsQuery)
                    ->whereIn('payment_status', [
                        EnrollmentPaymentStatus::NOT_PAID->value,
                        EnrollmentPaymentStatus::PARTIALLY_PAID->value,
                    ])
                    ->sum(DB::raw('total_amount - paid_amount')) ?? 0,
                'collection_rate' => 0,
            ];
            
            if ($revenue['total'] > 0) {
                $revenue['collection_rate'] = round(($revenue['collected'] / $revenue['total']) * 100, 2);
            }
            
            // Revenue by course (top 10) with filters
            $revenueByCourseQuery = Course::query()
                ->select('courses.id', 'courses.title', 'courses.code')
                ->selectRaw('COALESCE(SUM(enrollments.total_amount), 0) as total_revenue')
                ->selectRaw('COALESCE(SUM(CASE WHEN enrollments.payment_status = ? THEN enrollments.paid_amount ELSE 0 END), 0) as collected_revenue', [EnrollmentPaymentStatus::PAID->value])
                ->selectRaw('COUNT(DISTINCT enrollments.id) as enrollments_count')
                ->leftJoin('enrollments', function ($join) use ($startDate, $endDate) {
                    $join->on('courses.id', '=', 'enrollments.course_id')
                        ->where('enrollments.status', EnrollmentStatus::APPROVED->value)
                        ->whereBetween('enrollments.created_at', [$startDate, $endDate]);
                });
            
            // Apply category filter
            if (!empty($filters['category_id'])) {
                $revenueByCourseQuery->where('courses.category_id', $filters['category_id']);
            }
            
            $revenueByCourse = $revenueByCourseQuery
                ->groupBy('courses.id', 'courses.title', 'courses.code')
                ->orderByDesc('total_revenue')
                ->limit(10)
                ->get();
            
            // Revenue by month
            $revenueByMonth = $this->getRevenueByMonth($startDate, $endDate);
            
            // Average revenue per student
            $avgRevenuePerStudent = $revenue['collected'] > 0 
                ? round($revenue['collected'] / max(1, Enrollment::where('payment_status', EnrollmentPaymentStatus::PAID->value)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->distinct('student_id')
                    ->count('student_id')), 2)
                : 0;
            
            // Profitability insights
            $insights = [
                'most_profitable_courses' => $revenueByCourse->take(5)->values(),
                'underperforming_courses' => $revenueByCourse->where('total_revenue', '<', $revenueByCourse->avg('total_revenue'))->take(5)->values(),
                'collection_efficiency' => $revenue['collection_rate'] >= 80 ? 'excellent' : ($revenue['collection_rate'] >= 60 ? 'good' : 'needs_improvement'),
                'revenue_trend' => $this->calculateTrend($revenueByMonth->pluck('revenue')->toArray()),
            ];
            
            return [
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'revenue' => $revenue,
                'revenue_by_course' => $revenueByCourse,
                'revenue_by_month' => $revenueByMonth,
                'metrics' => [
                    'avg_revenue_per_student' => $avgRevenuePerStudent,
                    'avg_revenue_per_course' => $revenueByCourse->count() > 0 
                        ? round($revenueByCourse->avg('total_revenue'), 2) 
                        : 0,
                ],
                'insights' => $insights,
                'recommendations' => $this->generateProfitabilityRecommendations($revenue, $insights),
                'generated_at' => now()->toDateTimeString(),
            ];
        });
    }
    
    /**
     * Report 3: Student Analytics Report
     * Student behavior and engagement analysis
     */
    public function studentAnalyticsReport(array $filters = []): array
    {
        $cacheKey = 'strategic_report_student_analytics_' . md5(json_encode($filters));
        
        try {
            return Cache::remember($cacheKey, 3600, function () use ($filters) {
            $startDate = $filters['start_date'] ?? now()->subMonths(6)->startOfMonth();
            $endDate = $filters['end_date'] ?? now()->endOfMonth();
            
            if (is_string($startDate)) {
                $startDate = Carbon::parse($startDate);
            }
            if (is_string($endDate)) {
                $endDate = Carbon::parse($endDate);
            }
            
            // Student metrics
            $totalStudents = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->count();
            $newStudents = User::whereHas('role', fn ($q) => $q->where('name', 'student'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            // Enrollment metrics with filters
            $enrollmentsQuery = Enrollment::whereBetween('created_at', [$startDate, $endDate]);
            
            // Apply category filter
            if (!empty($filters['category_id'])) {
                $enrollmentsQuery->whereHas('course', fn ($q) => $q->where('category_id', $filters['category_id']));
            }
            
            // Apply course filter
            if (!empty($filters['course_id'])) {
                $enrollmentsQuery->where('course_id', $filters['course_id']);
            }
            
            // Apply instructor filter
            if (!empty($filters['instructor_id'])) {
                $enrollmentsQuery->whereHas('course.instructors', fn ($q) => $q->where('users.id', $filters['instructor_id']));
            }
            
            $enrollments = (clone $enrollmentsQuery)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
                ', [
                    EnrollmentStatus::APPROVED->value,
                    EnrollmentStatus::PENDING->value,
                    EnrollmentStatus::REJECTED->value,
                ])
                ->first();
            
            // Completion rates
            $completionData = $this->getCompletionRates($startDate, $endDate);
            
            // Attendance analysis
            $attendanceData = $this->getAttendanceAnalysis($startDate, $endDate);
            
            // Student satisfaction
            $satisfactionData = $this->getStudentSatisfaction($startDate, $endDate);
            
            // Retention analysis
            $retentionData = $this->getRetentionAnalysis();
            
            // Student segments
            $segments = [
                'active' => Enrollment::where('status', EnrollmentStatus::APPROVED->value)
                    ->whereHas('course', fn ($q) => $q->whereIn('status', [CourseStatus::UPCOMING->value, CourseStatus::RUNNING->value]))
                    ->distinct('student_id')
                    ->count('student_id'),
                'completed' => Enrollment::where('status', EnrollmentStatus::APPROVED->value)
                    ->whereHas('course', fn ($q) => $q->where('status', CourseStatus::COMPLETED->value))
                    ->distinct('student_id')
                    ->count('student_id'),
                'inactive' => $totalStudents - Enrollment::where('status', EnrollmentStatus::APPROVED->value)
                    ->distinct('student_id')
                    ->count('student_id'),
            ];
            
            return [
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'overview' => [
                    'total_students' => $totalStudents,
                    'new_students' => $newStudents,
                    'growth_rate' => $totalStudents > 0 ? round(($newStudents / $totalStudents) * 100, 2) : 0,
                ],
                'enrollments' => [
                    'total' => $enrollments->total ?? 0,
                    'approved' => $enrollments->approved ?? 0,
                    'pending' => $enrollments->pending ?? 0,
                    'rejected' => $enrollments->rejected ?? 0,
                    'approval_rate' => ($enrollments->total ?? 0) > 0 
                        ? round((($enrollments->approved ?? 0) / ($enrollments->total ?? 0)) * 100, 2) 
                        : 0,
                ],
                'completion' => $completionData,
                'attendance' => $attendanceData,
                'satisfaction' => $satisfactionData,
                'retention' => $retentionData,
                'segments' => $segments,
                'insights' => $this->generateStudentInsights($completionData, $attendanceData, $satisfactionData),
                'recommendations' => $this->generateStudentRecommendations($completionData, $attendanceData, $satisfactionData),
                'generated_at' => now()->toDateTimeString(),
            ];
            });
        } catch (\Exception $e) {
            // Log error and return empty structure
            Log::error('Error generating student analytics report: ' . $e->getMessage());
            $startDate = $filters['start_date'] ?? now()->subMonths(6)->startOfMonth();
            $endDate = $filters['end_date'] ?? now()->endOfMonth();
            
            if (is_string($startDate)) {
                $startDate = Carbon::parse($startDate);
            }
            if (is_string($endDate)) {
                $endDate = Carbon::parse($endDate);
            }
            
            return [
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'overview' => [
                    'total_students' => 0,
                    'new_students' => 0,
                    'growth_rate' => 0,
                ],
                'enrollments' => [
                    'total' => 0,
                    'approved' => 0,
                    'pending' => 0,
                    'rejected' => 0,
                    'approval_rate' => 0,
                ],
                'completion' => [
                    'total' => 0,
                    'completed' => 0,
                    'in_progress' => 0,
                    'not_started' => 0,
                    'completion_rate' => 0,
                ],
                'attendance' => [
                    'total_sessions' => 0,
                    'total_attendance' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'rate' => 0,
                ],
                'satisfaction' => [
                    'avg_course_rating' => 0,
                    'avg_instructor_rating' => 0,
                    'total_reviews' => 0,
                ],
                'retention' => [
                    'total_students' => 0,
                    'returning_students' => 0,
                    'retention_rate' => 0,
                ],
                'segments' => [
                    'active' => 0,
                    'completed' => 0,
                    'inactive' => 0,
                ],
                'insights' => [],
                'recommendations' => [],
                'generated_at' => now()->toDateTimeString(),
                'error' => config('app.debug') ? $e->getMessage() : 'Error generating report',
            ];
        }
    }
    
    /**
     * Report 4: Instructor Performance Report
     * Instructor effectiveness and quality analysis
     */
    public function instructorPerformanceReport(array $filters = []): array
    {
        $cacheKey = 'strategic_report_instructor_performance_' . md5(json_encode($filters));
        
        try {
            return Cache::remember($cacheKey, 3600, function () use ($filters) {
                $startDate = $filters['start_date'] ?? now()->subMonths(6)->startOfMonth();
                $endDate = $filters['end_date'] ?? now()->endOfMonth();
                
                if (is_string($startDate)) {
                    $startDate = Carbon::parse($startDate);
                }
                if (is_string($endDate)) {
                    $endDate = Carbon::parse($endDate);
                }
                
                // Get all instructors with aggregated data
                $instructorsQuery = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'));
                
                // Apply instructor filter if provided
                if (!empty($filters['instructor_id'])) {
                    $instructorsQuery->where('id', $filters['instructor_id']);
                }
                
                $instructors = $instructorsQuery->get()
                    ->map(function ($instructor) use ($startDate, $endDate, $filters) {
                        try {
                            $coursesQuery = $instructor->instructorCourses()
                                ->whereBetween('courses.created_at', [$startDate, $endDate]);
                            
                            // Apply category filter
                            if (!empty($filters['category_id'])) {
                                $coursesQuery->where('courses.category_id', $filters['category_id']);
                            }
                            
                            // Apply course filter
                            if (!empty($filters['course_id'])) {
                                $coursesQuery->where('courses.id', $filters['course_id']);
                            }
                            
                            $courses = $coursesQuery->get();
                    
                    $totalStudents = 0;
                    $totalSessions = 0;
                    $completedSessions = 0;
                    $totalRevenue = 0;
                    
                    foreach ($courses as $course) {
                        $enrollments = Enrollment::where('course_id', $course->id)
                            ->where('status', EnrollmentStatus::APPROVED->value)
                            ->count();
                        $totalStudents += $enrollments;
                        
                        $sessions = Session::where('course_id', $course->id)->count();
                        $totalSessions += $sessions;
                        
                        $completed = Session::where('course_id', $course->id)
                            ->where('status', SessionStatus::COMPLETED->value)
                            ->count();
                        $completedSessions += $completed;
                        
                        $revenue = Enrollment::where('course_id', $course->id)
                            ->where('status', EnrollmentStatus::APPROVED->value)
                            ->where('payment_status', EnrollmentPaymentStatus::PAID->value)
                            ->sum('paid_amount');
                        $totalRevenue += $revenue;
                    }
                    
                    // Ratings
                    $ratings = CourseReview::where('instructor_id', $instructor->id)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->selectRaw('AVG(rating_instructor) as avg_rating, COUNT(*) as reviews_count')
                        ->first();
                    
                            // Attendance rate for instructor's sessions
                            $attendanceRate = $this->getInstructorAttendanceRate($instructor->id, $startDate, $endDate);
                            
                            return [
                                'id' => $instructor->id,
                                'name' => $instructor->name,
                                'email' => $instructor->email,
                                'is_active' => $instructor->is_active,
                                'courses_count' => $courses->count(),
                                'total_students' => $totalStudents,
                                'total_sessions' => $totalSessions,
                                'completed_sessions' => $completedSessions,
                                'completion_rate' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 0,
                                'total_revenue' => round($totalRevenue, 2),
                                'avg_rating' => round($ratings->avg_rating ?? 0, 2),
                                'reviews_count' => $ratings->reviews_count ?? 0,
                                'attendance_rate' => $attendanceRate,
                                'performance_score' => $this->calculateInstructorPerformanceScore([
                                    'completion_rate' => $totalSessions > 0 ? ($completedSessions / $totalSessions) : 0,
                                    'avg_rating' => $ratings->avg_rating ?? 0,
                                    'attendance_rate' => $attendanceRate,
                                    'students_count' => $totalStudents,
                                ]),
                            ];
                        } catch (\Exception $e) {
                            Log::error("Error processing instructor {$instructor->id}: " . $e->getMessage());
                            // Return minimal data for this instructor
                            return [
                                'id' => $instructor->id,
                                'name' => $instructor->name,
                                'email' => $instructor->email,
                                'is_active' => $instructor->is_active,
                                'courses_count' => 0,
                                'total_students' => 0,
                                'total_sessions' => 0,
                                'completed_sessions' => 0,
                                'completion_rate' => 0,
                                'total_revenue' => 0,
                                'avg_rating' => 0,
                                'reviews_count' => 0,
                                'attendance_rate' => 0,
                                'performance_score' => 0,
                            ];
                        }
                    })
                    ->sortByDesc('performance_score')
                    ->values();
            
            // Overall statistics
            $overall = [
                'total_instructors' => $instructors->count(),
                'active_instructors' => $instructors->where('is_active', true)->count(),
                'avg_rating' => round($instructors->avg('avg_rating') ?? 0, 2),
                'avg_completion_rate' => round($instructors->avg('completion_rate') ?? 0, 2),
                'avg_attendance_rate' => round($instructors->avg('attendance_rate') ?? 0, 2),
                'total_revenue' => round($instructors->sum('total_revenue'), 2),
            ];
            
            // Top and bottom performers
            $topPerformers = $instructors->take(5)->values();
            $bottomPerformers = $instructors->reverse()->take(5)->values();
            
            return [
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'overall' => $overall,
                'instructors' => $instructors,
                'top_performers' => $topPerformers,
                'bottom_performers' => $bottomPerformers,
                'insights' => $this->generateInstructorInsights($overall, $topPerformers, $bottomPerformers),
                'recommendations' => $this->generateInstructorRecommendations($overall, $topPerformers, $bottomPerformers),
                'generated_at' => now()->toDateTimeString(),
                ];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // If it's a connection or constraint error, clear cache and throw
            Cache::forget($cacheKey);
            Log::error('Database error in instructor performance report: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            // For other errors, return empty structure
            Log::error('Error generating instructor performance report: ' . $e->getMessage());
            $startDate = $filters['start_date'] ?? now()->subMonths(6)->startOfMonth();
            $endDate = $filters['end_date'] ?? now()->endOfMonth();
            
            if (is_string($startDate)) {
                $startDate = Carbon::parse($startDate);
            }
            if (is_string($endDate)) {
                $endDate = Carbon::parse($endDate);
            }
            
            return [
                'period' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ],
                'overall' => [
                    'total_instructors' => 0,
                    'active_instructors' => 0,
                    'avg_rating' => 0,
                    'avg_completion_rate' => 0,
                    'avg_attendance_rate' => 0,
                    'total_revenue' => 0,
                ],
                'instructors' => [],
                'top_performers' => [],
                'bottom_performers' => [],
                'insights' => [],
                'recommendations' => [],
                'generated_at' => now()->toDateTimeString(),
                'error' => config('app.debug') ? $e->getMessage() : 'Error generating report',
            ];
        }
    }
    
    /**
     * Report 5: Forecasting Report
     * Predictive analytics for future planning
     */
    public function forecastingReport(array $filters = []): array
    {
        $cacheKey = 'strategic_report_forecasting_' . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, 7200, function () use ($filters) {
            $forecastMonths = $filters['months'] ?? 6;
            
            // Historical data (last 12 months)
            $historicalData = $this->getHistoricalData(12);
            
            // Revenue forecast
            $revenueForecast = $this->forecastRevenue($historicalData['revenue'], $forecastMonths);
            
            // Enrollment forecast
            $enrollmentForecast = $this->forecastEnrollments($historicalData['enrollments'], $forecastMonths);
            
            // Student growth forecast
            $studentGrowthForecast = $this->forecastStudentGrowth($historicalData['students'], $forecastMonths);
            
            // Course demand forecast
            $courseDemandForecast = $this->forecastCourseDemand($historicalData['courses'], $forecastMonths);
            
            // Risk analysis
            $risks = $this->identifyRisks($historicalData, $revenueForecast);
            
            // Opportunities
            $opportunities = $this->identifyOpportunities($historicalData, $revenueForecast);
            
            return [
                'forecast_period' => $forecastMonths,
                'historical_data' => $historicalData,
                'forecasts' => [
                    'revenue' => $revenueForecast,
                    'enrollments' => $enrollmentForecast,
                    'student_growth' => $studentGrowthForecast,
                    'course_demand' => $courseDemandForecast,
                ],
                'risks' => $risks,
                'opportunities' => $opportunities,
                'recommendations' => $this->generateForecastingRecommendations($revenueForecast, $risks, $opportunities),
                'generated_at' => now()->toDateTimeString(),
            ];
        });
    }
    
    // Helper methods for performance optimization
    
    private function getRevenueMetrics(Carbon $startDate): array
    {
        try {
            $result = DB::table('enrollments')
                ->where('status', EnrollmentStatus::APPROVED->value)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('
                    COALESCE(SUM(total_amount), 0) as total,
                    COALESCE(SUM(CASE WHEN payment_status = ? THEN paid_amount ELSE 0 END), 0) as collected,
                    COALESCE(SUM(CASE WHEN payment_status IN (?, ?) THEN total_amount - paid_amount ELSE 0 END), 0) as outstanding
                ', [
                    EnrollmentPaymentStatus::PAID->value,
                    EnrollmentPaymentStatus::NOT_PAID->value,
                    EnrollmentPaymentStatus::PARTIALLY_PAID->value,
                ])
                ->first();
            
            return [
                'total' => (float) ($result->total ?? 0),
                'collected' => (float) ($result->collected ?? 0),
                'outstanding' => (float) ($result->outstanding ?? 0),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting revenue metrics: ' . $e->getMessage());
            return [
                'total' => 0,
                'collected' => 0,
                'outstanding' => 0,
            ];
        }
    }
    
    private function getEnrollmentMetrics(Carbon $startDate): array
    {
        $result = DB::table('enrollments')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
            ', [
                EnrollmentStatus::APPROVED->value,
                EnrollmentStatus::PENDING->value,
                EnrollmentStatus::REJECTED->value,
            ])
            ->first();
        
        return [
            'total' => $result->total ?? 0,
            'approved' => $result->approved ?? 0,
            'pending' => $result->pending ?? 0,
            'rejected' => $result->rejected ?? 0,
            'approval_rate' => ($result->total ?? 0) > 0 
                ? round((($result->approved ?? 0) / ($result->total ?? 0)) * 100, 2) 
                : 0,
        ];
    }
    
    private function getCourseMetrics(Carbon $startDate): array
    {
        return [
            'total' => Course::where('created_at', '>=', $startDate)->count(),
            'active' => Course::whereIn('status', [CourseStatus::UPCOMING->value, CourseStatus::RUNNING->value])
                ->where('is_hidden', false)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'completed' => Course::where('status', CourseStatus::COMPLETED->value)
                ->where('created_at', '>=', $startDate)
                ->count(),
        ];
    }
    
    private function getStudentMetrics(Carbon $startDate): array
    {
        $total = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->count();
        $new = User::whereHas('role', fn ($q) => $q->where('name', 'student'))
            ->where('created_at', '>=', $startDate)
            ->count();
        
        return [
            'total' => $total,
            'new_students' => $new,
            'growth_rate' => $total > 0 ? round(($new / $total) * 100, 2) : 0,
        ];
    }
    
    private function getInstructorMetrics(Carbon $startDate): array
    {
        $total = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->count();
        $new = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))
            ->where('created_at', '>=', $startDate)
            ->count();
        
        return [
            'total' => $total,
            'new_instructors' => $new,
        ];
    }
    
    private function getSessionMetrics(Carbon $startDate): array
    {
        $result = DB::table('sessions')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as scheduled
            ', [
                SessionStatus::COMPLETED->value,
                SessionStatus::SCHEDULED->value,
            ])
            ->first();
        
        return [
            'total' => $result->total ?? 0,
            'completed' => $result->completed ?? 0,
            'scheduled' => $result->scheduled ?? 0,
            'completion_rate' => ($result->total ?? 0) > 0 
                ? round((($result->completed ?? 0) / ($result->total ?? 0)) * 100, 2) 
                : 0,
        ];
    }
    
    private function getAttendanceMetrics(Carbon $startDate): array
    {
        $result = DB::table('attendance')
            ->join('sessions', 'attendance.session_id', '=', 'sessions.id')
            ->where('sessions.created_at', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN attendance.status = ? THEN 1 ELSE 0 END) as present
            ', [AttendanceStatus::PRESENT->value])
            ->first();
        
        return [
            'total' => $result->total ?? 0,
            'present' => $result->present ?? 0,
            'rate' => ($result->total ?? 0) > 0 
                ? round((($result->present ?? 0) / ($result->total ?? 0)) * 100, 2) 
                : 0,
        ];
    }
    
    private function getSatisfactionMetrics(Carbon $startDate): array
    {
        $result = CourseReview::where('created_at', '>=', $startDate)
            ->selectRaw('
                AVG(rating_course) as avg_course_rating,
                AVG(rating_instructor) as avg_instructor_rating,
                COUNT(*) as total_reviews
            ')
            ->first();
        
        return [
            'avg_course_rating' => round($result->avg_course_rating ?? 0, 2),
            'avg_instructor_rating' => round($result->avg_instructor_rating ?? 0, 2),
            'total_reviews' => $result->total_reviews ?? 0,
        ];
    }
    
    private function getStartDate(string $period): Carbon
    {
        return match ($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }
    
    private function getPreviousPeriodStart(string $period, Carbon $currentStart): Carbon
    {
        return match ($period) {
            'day' => $currentStart->copy()->subDay(),
            'week' => $currentStart->copy()->subWeek(),
            'month' => $currentStart->copy()->subMonth(),
            'quarter' => $currentStart->copy()->subQuarter(),
            'year' => $currentStart->copy()->subYear(),
            default => $currentStart->copy()->subMonth(),
        };
    }
    
    private function calculateGrowthRate(float $previous, float $current): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }
    
    private function getTrendsData(string $period, int $count): array
    {
        $trends = [];
        $current = now();
        
        for ($i = $count - 1; $i >= 0; $i--) {
            $periodStart = match ($period) {
                'day' => $current->copy()->subDays($i)->startOfDay(),
                'week' => $current->copy()->subWeeks($i)->startOfWeek(),
                'month' => $current->copy()->subMonths($i)->startOfMonth(),
                'quarter' => $current->copy()->subQuarters($i)->startOfQuarter(),
                'year' => $current->copy()->subYears($i)->startOfYear(),
                default => $current->copy()->subMonths($i)->startOfMonth(),
            };
            
            $periodEnd = match ($period) {
                'day' => $periodStart->copy()->endOfDay(),
                'week' => $periodStart->copy()->endOfWeek(),
                'month' => $periodStart->copy()->endOfMonth(),
                'quarter' => $periodStart->copy()->endOfQuarter(),
                'year' => $periodStart->copy()->endOfYear(),
                default => $periodStart->copy()->endOfMonth(),
            };
            
            $revenue = Enrollment::where('payment_status', EnrollmentPaymentStatus::PAID->value)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum('paid_amount');
            
            $enrollments = Enrollment::whereBetween('created_at', [$periodStart, $periodEnd])->count();
            
            $trends[] = [
                'period' => $periodStart->format('Y-m-d'),
                'label' => $periodStart->format('M Y'),
                'revenue' => round($revenue, 2),
                'enrollments' => $enrollments,
            ];
        }
        
        return $trends;
    }
    
    private function generateAlerts(array $kpis, array $growth): array
    {
        $alerts = [];
        
        if (($growth['revenue_growth'] ?? 0) < -10) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'انخفاض في الإيرادات',
                'message' => 'الإيرادات انخفضت بنسبة ' . abs($growth['revenue_growth']) . '% مقارنة بالفترة السابقة',
            ];
        }
        
        if (($kpis['attendance']['rate'] ?? 0) < 70) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'معدل حضور منخفض',
                'message' => 'معدل الحضور ' . ($kpis['attendance']['rate'] ?? 0) . '% وهو أقل من المستوى المطلوب (70%)',
            ];
        }
        
        if (($kpis['satisfaction']['avg_course_rating'] ?? 0) < 3.5) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'رضا منخفض',
                'message' => 'متوسط تقييم الكورسات ' . ($kpis['satisfaction']['avg_course_rating'] ?? 0) . ' وهو أقل من المستوى المطلوب',
            ];
        }
        
        return $alerts;
    }
    
    private function generateRecommendations(array $kpis, array $growth): array
    {
        $recommendations = [];
        
        if (($growth['revenue_growth'] ?? 0) < 0) {
            $recommendations[] = 'تحسين استراتيجية التسويق لزيادة التسجيلات';
            $recommendations[] = 'مراجعة أسعار الكورسات لضمان التنافسية';
        }
        
        if (($kpis['attendance']['rate'] ?? 0) < 80) {
            $recommendations[] = 'تحسين جودة المحتوى لزيادة الحضور';
            $recommendations[] = 'إرسال تذكيرات للطلاب قبل الجلسات';
        }
        
        if (($kpis['enrollments']['approval_rate'] ?? 0) < 80) {
            $recommendations[] = 'تحسين عملية الموافقة على التسجيلات';
        }
        
        return $recommendations;
    }
    
    private function getRevenueByMonth(Carbon $startDate, Carbon $endDate): Collection
    {
        return DB::table('enrollments')
            ->where('payment_status', EnrollmentPaymentStatus::PAID->value)
            ->where('status', EnrollmentStatus::APPROVED->value)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                SUM(paid_amount) as revenue,
                COUNT(*) as count
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'revenue' => round($item->revenue, 2),
                    'count' => $item->count,
                ];
            });
    }
    
    private function calculateTrend(array $values): string
    {
        if (count($values) < 2) {
            return 'stable';
        }
        
        $firstHalf = array_slice($values, 0, ceil(count($values) / 2));
        $secondHalf = array_slice($values, ceil(count($values) / 2));
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);
        
        if ($secondAvg > $firstAvg * 1.1) {
            return 'increasing';
        } elseif ($secondAvg < $firstAvg * 0.9) {
            return 'decreasing';
        }
        
        return 'stable';
    }
    
    private function generateProfitabilityRecommendations(array $revenue, array $insights): array
    {
        $recommendations = [];
        
        if ($revenue['collection_rate'] < 80) {
            $recommendations[] = 'تحسين عملية تحصيل المدفوعات - معدل التحصيل الحالي ' . $revenue['collection_rate'] . '%';
        }
        
        if ($insights['revenue_trend'] === 'decreasing') {
            $recommendations[] = 'الإيرادات في انخفاض - مراجعة استراتيجية التسعير والتسويق';
        }
        
        return $recommendations;
    }
    
    private function getCompletionRates(Carbon $startDate, Carbon $endDate): array
    {
        $enrollments = Enrollment::where('status', EnrollmentStatus::APPROVED->value)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('course')
            ->get();
        
        $completed = 0;
        $inProgress = 0;
        $notStarted = 0;
        
        foreach ($enrollments as $enrollment) {
            // Handle case where course might be null
            if (!$enrollment->course) {
                $notStarted++;
                continue;
            }
            
            if ($enrollment->course->status === CourseStatus::COMPLETED->value) {
                $completed++;
            } elseif (in_array($enrollment->course->status, [CourseStatus::UPCOMING->value, CourseStatus::RUNNING->value])) {
                $inProgress++;
            } else {
                $notStarted++;
            }
        }
        
        $total = $enrollments->count();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
    
    private function getAttendanceAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $sessions = Session::whereBetween('created_at', [$startDate, $endDate])->pluck('id');
        
        if ($sessions->isEmpty()) {
            return [
                'total_sessions' => 0,
                'total_attendance' => 0,
                'present' => 0,
                'absent' => 0,
                'rate' => 0,
            ];
        }
        
        $result = DB::table('attendance')
            ->whereIn('session_id', $sessions)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present
            ', [AttendanceStatus::PRESENT->value])
            ->first();
        
        return [
            'total_sessions' => $sessions->count(),
            'total_attendance' => $result->total ?? 0,
            'present' => $result->present ?? 0,
            'absent' => ($result->total ?? 0) - ($result->present ?? 0),
            'rate' => ($result->total ?? 0) > 0 
                ? round((($result->present ?? 0) / ($result->total ?? 0)) * 100, 2) 
                : 0,
        ];
    }
    
    private function getStudentSatisfaction(Carbon $startDate, Carbon $endDate): array
    {
        $reviews = CourseReview::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                AVG(rating_course) as avg_course,
                AVG(rating_instructor) as avg_instructor,
                COUNT(*) as total
            ')
            ->first();
        
        return [
            'avg_course_rating' => round($reviews->avg_course ?? 0, 2),
            'avg_instructor_rating' => round($reviews->avg_instructor ?? 0, 2),
            'total_reviews' => $reviews->total ?? 0,
        ];
    }
    
    private function getRetentionAnalysis(): array
    {
        // Students who enrolled in multiple courses
        $multiCourseStudents = DB::table('enrollments')
            ->where('status', EnrollmentStatus::APPROVED->value)
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('COUNT(DISTINCT course_id) > 1')
            ->count();
        
        $totalStudents = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->count();
        
        return [
            'total_students' => $totalStudents,
            'returning_students' => $multiCourseStudents,
            'retention_rate' => $totalStudents > 0 
                ? round(($multiCourseStudents / $totalStudents) * 100, 2) 
                : 0,
        ];
    }
    
    private function generateStudentInsights(array $completion, array $attendance, array $satisfaction): array
    {
        $insights = [];
        
        if ($completion['completion_rate'] < 70) {
            $insights[] = 'معدل إتمام الكورسات منخفض - يحتاج تحسين';
        }
        
        if ($attendance['rate'] < 75) {
            $insights[] = 'معدل الحضور يحتاج تحسين';
        }
        
        if ($satisfaction['avg_course_rating'] >= 4.5) {
            $insights[] = 'رضا الطلاب ممتاز - استمر في نفس المستوى';
        }
        
        return $insights;
    }
    
    private function generateStudentRecommendations(array $completion, array $attendance, array $satisfaction): array
    {
        $recommendations = [];
        
        if ($completion['completion_rate'] < 70) {
            $recommendations[] = 'تحسين محتوى الكورسات لزيادة معدل الإتمام';
            $recommendations[] = 'إضافة حوافز للطلاب لإكمال الكورسات';
        }
        
        if ($attendance['rate'] < 75) {
            $recommendations[] = 'تحسين جدولة الجلسات لتناسب الطلاب';
            $recommendations[] = 'إرسال تذكيرات قبل الجلسات';
        }
        
        return $recommendations;
    }
    
    private function getInstructorAttendanceRate(int $instructorId, Carbon $startDate, Carbon $endDate): float
    {
        try {
            $courseIds = DB::table('course_instructor')
                ->where('instructor_id', $instructorId)
                ->pluck('course_id');
            
            if ($courseIds->isEmpty()) {
                return 0;
            }
            
            $sessions = Session::whereIn('course_id', $courseIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->pluck('id');
            
            if ($sessions->isEmpty()) {
                return 0;
            }
            
            $result = DB::table('attendance')
                ->whereIn('session_id', $sessions)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present
                ', [AttendanceStatus::PRESENT->value])
                ->first();
            
            if (($result->total ?? 0) === 0) {
                return 0;
            }
            
            return round((($result->present ?? 0) / ($result->total ?? 0)) * 100, 2);
        } catch (\Exception $e) {
            Log::error("Error getting instructor attendance rate for instructor {$instructorId}: " . $e->getMessage());
            return 0;
        }
    }
    
    private function calculateInstructorPerformanceScore(array $metrics): float
    {
        $score = 0;
        
        // Completion rate (30%)
        $score += ($metrics['completion_rate'] / 100) * 30;
        
        // Rating (40%)
        $score += ($metrics['avg_rating'] / 5) * 40;
        
        // Attendance (20%)
        $score += ($metrics['attendance_rate'] / 100) * 20;
        
        // Students count (10% - normalized)
        $score += min(($metrics['students_count'] / 100) * 10, 10);
        
        return round($score, 2);
    }
    
    private function generateInstructorInsights(array $overall, Collection $top, Collection $bottom): array
    {
        $insights = [];
        
        if ($overall['avg_rating'] >= 4.5) {
            $insights[] = 'جودة المدربين ممتازة بشكل عام';
        }
        
        if ($overall['avg_completion_rate'] < 80) {
            $insights[] = 'بعض المدربين يحتاجون دعم لتحسين معدل الإتمام';
        }
        
        return $insights;
    }
    
    private function generateInstructorRecommendations(array $overall, Collection $top, Collection $bottom): array
    {
        $recommendations = [];
        
        if ($overall['avg_rating'] < 4.0) {
            $recommendations[] = 'تنظيم برامج تدريبية للمدربين لتحسين الجودة';
        }
        
        if ($bottom->isNotEmpty()) {
            $recommendations[] = 'مراجعة أداء المدربين ذوي الأداء المنخفض وتقديم الدعم';
        }
        
        return $recommendations;
    }
    
    private function getHistoricalData(int $months): array
    {
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            
            $data[] = [
                'month' => $date->format('Y-m'),
                'revenue' => Enrollment::where('payment_status', EnrollmentPaymentStatus::PAID->value)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('paid_amount'),
                'enrollments' => Enrollment::whereBetween('created_at', [$start, $end])->count(),
                'students' => User::whereHas('role', fn ($q) => $q->where('name', 'student'))
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'courses' => Course::whereBetween('created_at', [$start, $end])->count(),
            ];
        }
        
        return [
            'revenue' => array_column($data, 'revenue'),
            'enrollments' => array_column($data, 'enrollments'),
            'students' => array_column($data, 'students'),
            'courses' => array_column($data, 'courses'),
        ];
    }
    
    private function forecastRevenue(array $historical, int $months): array
    {
        // Simple linear regression for forecasting
        $forecast = [];
        $n = count($historical);
        
        if ($n < 2) {
            // Not enough data, use average
            $avg = $n > 0 ? array_sum($historical) / $n : 0;
            for ($i = 1; $i <= $months; $i++) {
                $forecast[] = round($avg, 2);
            }
            return $forecast;
        }
        
        // Calculate trend
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1;
            $y = $historical[$i];
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        // Forecast future months
        for ($i = 1; $i <= $months; $i++) {
            $forecast[] = round(max(0, $intercept + $slope * ($n + $i)), 2);
        }
        
        return $forecast;
    }
    
    private function forecastEnrollments(array $historical, int $months): array
    {
        return $this->forecastRevenue($historical, $months); // Same method
    }
    
    private function forecastStudentGrowth(array $historical, int $months): array
    {
        return $this->forecastRevenue($historical, $months); // Same method
    }
    
    private function forecastCourseDemand(array $historical, int $months): array
    {
        return $this->forecastRevenue($historical, $months); // Same method
    }
    
    private function identifyRisks(array $historical, array $revenueForecast): array
    {
        $risks = [];
        
        // Check for declining trend
        $recent = array_slice($historical['revenue'], -3);
        if (count($recent) >= 2 && $recent[count($recent) - 1] < $recent[0] * 0.9) {
            $risks[] = [
                'type' => 'revenue_decline',
                'severity' => 'high',
                'description' => 'انخفاض في الإيرادات خلال الأشهر الأخيرة',
            ];
        }
        
        // Check forecast for negative trend
        if (count($revenueForecast) >= 2 && $revenueForecast[count($revenueForecast) - 1] < $revenueForecast[0] * 0.9) {
            $risks[] = [
                'type' => 'forecasted_decline',
                'severity' => 'medium',
                'description' => 'التوقعات تشير إلى انخفاض محتمل في الإيرادات',
            ];
        }
        
        return $risks;
    }
    
    private function identifyOpportunities(array $historical, array $revenueForecast): array
    {
        $opportunities = [];
        
        // Check for growth potential
        $avgRevenue = array_sum($historical['revenue']) / count($historical['revenue']);
        $forecastAvg = array_sum($revenueForecast) / count($revenueForecast);
        
        if ($forecastAvg > $avgRevenue * 1.1) {
            $opportunities[] = [
                'type' => 'growth_potential',
                'description' => 'إمكانية نمو في الإيرادات بنسبة ' . round((($forecastAvg / $avgRevenue) - 1) * 100, 2) . '%',
            ];
        }
        
        return $opportunities;
    }
    
    private function generateForecastingRecommendations(array $forecast, array $risks, array $opportunities): array
    {
        $recommendations = [];
        
        if (!empty($risks)) {
            $recommendations[] = 'اتخاذ إجراءات فورية لمعالجة المخاطر المحددة';
        }
        
        if (!empty($opportunities)) {
            $recommendations[] = 'الاستفادة من فرص النمو المتوقعة';
        }
        
        return $recommendations;
    }
}

