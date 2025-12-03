<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Sessions\Models\GroupSession;
use Modules\LMS\Attendance\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Get student's enrolled courses
     * GET /api/student/my-courses
     */
    public function myCourses(Request $request): JsonResponse
    {
        $student = Auth::user();
        
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with(['course.category', 'course.instructors', 'group'])
            ->get();

        $courses = $enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title,
                'slug' => $enrollment->course->slug,
                'description' => $enrollment->course->description,
                'image_path' => $enrollment->course->image_path,
                'category' => $enrollment->course->category,
                'instructors' => $enrollment->course->instructors,
                'enrollment' => [
                    'id' => $enrollment->id,
                    'status' => $enrollment->status,
                    'group_id' => $enrollment->group_id,
                    'group' => $enrollment->group,
                    'can_attend' => $enrollment->can_attend,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    /**
     * Get student's group for a course
     * GET /api/student/my-group
     */
    public function myGroup(Request $request): JsonResponse
    {
        $student = Auth::user();
        $courseId = $request->query('course_id');

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->when($courseId, fn($q) => $q->where('course_id', $courseId))
            ->with(['group.course', 'group.instructor', 'group.students'])
            ->first();

        if (!$enrollment || !$enrollment->group) {
            return response()->json([
                'success' => false,
                'message' => 'No group found',
            ], 404);
        }

        $group = $enrollment->group;
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $group->id,
                'code' => $group->code,
                'name' => $group->name,
                'capacity' => $group->capacity,
                'room' => $group->room,
                'course' => $group->course,
                'instructor' => $group->instructor,
                'students_count' => $group->students->count(),
                'students' => $group->students,
            ],
        ]);
    }

    /**
     * Get student's sessions
     * GET /api/student/my-sessions
     */
    public function mySessions(Request $request): JsonResponse
    {
        $student = Auth::user();
        $perPage = $request->integer('per_page', 15);
        
        // Get all approved enrollments
        $enrollmentIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('group_id')
            ->filter();

        if ($enrollmentIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'meta' => [
                    'pagination' => [
                        'current_page' => 1,
                        'per_page' => $perPage,
                        'total' => 0,
                        'last_page' => 1,
                    ],
                ],
            ]);
        }

        $query = GroupSession::whereIn('group_id', $enrollmentIds)
            ->with(['group.course', 'sessionTemplate', 'attendance' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }]);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->has('course_id')) {
            $query->whereHas('group', fn($q) => $q->where('course_id', $request->integer('course_id')));
        }

        // Order by date
        $query->orderBy('session_date', 'desc')->orderBy('start_time', 'desc');

        $sessions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sessions->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $sessions->currentPage(),
                    'per_page' => $sessions->perPage(),
                    'total' => $sessions->total(),
                    'last_page' => $sessions->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get student's attendance history
     * GET /api/student/attendance-history
     */
    public function attendanceHistory(Request $request): JsonResponse
    {
        $student = Auth::user();
        $perPage = $request->integer('per_page', 15);

        $query = Attendance::where('student_id', $student->id)
            ->with(['groupSession.group.course', 'groupSession.sessionTemplate']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->has('course_id')) {
            $query->whereHas('groupSession.group', fn($q) => $q->where('course_id', $request->integer('course_id')));
        }

        if ($request->has('date_from')) {
            $query->whereHas('groupSession', fn($q) => $q->where('session_date', '>=', $request->date('date_from')));
        }

        if ($request->has('date_to')) {
            $query->whereHas('groupSession', fn($q) => $q->where('session_date', '<=', $request->date('date_to')));
        }

        // Order by date
        $query->orderBy('created_at', 'desc');

        $attendance = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendance->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $attendance->currentPage(),
                    'per_page' => $attendance->perPage(),
                    'total' => $attendance->total(),
                    'last_page' => $attendance->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get student profile
     * GET /api/student/profile
     */
    public function profile(): JsonResponse
    {
        $student = Auth::user();
        
        $student->load(['role']);

        // Get enrollment stats
        $enrollmentsCount = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->count();

        $attendanceStats = Attendance::where('student_id', $student->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $student,
                'stats' => [
                    'enrollments_count' => $enrollmentsCount,
                    'attendance' => [
                        'total' => $attendanceStats->total ?? 0,
                        'present' => $attendanceStats->present ?? 0,
                        'absent' => $attendanceStats->absent ?? 0,
                        'rate' => ($attendanceStats->total ?? 0) > 0 
                            ? round((($attendanceStats->present ?? 0) / ($attendanceStats->total ?? 0)) * 100, 2)
                            : 0,
                    ],
                ],
            ],
        ]);
    }
}

