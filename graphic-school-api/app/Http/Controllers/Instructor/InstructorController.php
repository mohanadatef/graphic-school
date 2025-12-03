<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Modules\LMS\Sessions\Models\GroupSession;
use Modules\LMS\Attendance\Models\Attendance;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    /**
     * Get instructor's groups
     * GET /api/instructor/my-groups
     */
    public function myGroups(Request $request): JsonResponse
    {
        $instructor = Auth::user();
        
        $groups = Group::where('instructor_id', $instructor->id)
            ->with(['course.category', 'course.instructors', 'students'])
            ->get();

        $groupsData = $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'code' => $group->code,
                'name' => $group->name,
                'capacity' => $group->capacity,
                'room' => $group->room,
                'is_active' => $group->is_active,
                'course' => $group->course,
                'students_count' => $group->students->count(),
                'available_spots' => $group->getAvailableSpotsAttribute(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $groupsData,
        ]);
    }

    /**
     * Get sessions for a group
     * GET /api/instructor/groups/{groupId}/sessions
     */
    public function groupSessions(Request $request, int $groupId): JsonResponse
    {
        $instructor = Auth::user();
        $perPage = $request->integer('per_page', 15);

        // Verify instructor owns this group
        $group = Group::where('id', $groupId)
            ->where('instructor_id', $instructor->id)
            ->firstOrFail();

        $query = GroupSession::where('group_id', $groupId)
            ->with(['sessionTemplate', 'attendance.student']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->has('date_from')) {
            $query->where('session_date', '>=', $request->date('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('session_date', '<=', $request->date('date_to'));
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
     * Get students in a group
     * GET /api/instructor/groups/{groupId}/students
     */
    public function groupStudents(Request $request, int $groupId): JsonResponse
    {
        $instructor = Auth::user();

        // Verify instructor owns this group
        $group = Group::where('id', $groupId)
            ->where('instructor_id', $instructor->id)
            ->with(['students', 'course'])
            ->firstOrFail();

        // Get enrollments for this group
        $enrollments = Enrollment::where('group_id', $groupId)
            ->where('status', 'approved')
            ->with('student')
            ->get();

        $students = $enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->student->id,
                'name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
                'phone' => $enrollment->student->phone,
                'avatar_path' => $enrollment->student->avatar_path,
                'enrollment_id' => $enrollment->id,
                'enrollment_status' => $enrollment->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'group' => [
                    'id' => $group->id,
                    'code' => $group->code,
                    'name' => $group->name,
                    'course' => $group->course,
                ],
                'students' => $students,
            ],
        ]);
    }

    /**
     * Get attendance for a session
     * GET /api/instructor/sessions/{sessionId}/attendance
     */
    public function sessionAttendance(Request $request, int $sessionId): JsonResponse
    {
        $instructor = Auth::user();

        // Verify instructor owns the group for this session
        $session = GroupSession::where('id', $sessionId)
            ->with(['group', 'attendance.student'])
            ->firstOrFail();

        if ($session->group->instructor_id !== $instructor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Get all students in the group
        $enrollments = Enrollment::where('group_id', $session->group_id)
            ->where('status', 'approved')
            ->with('student')
            ->get();

        // Build attendance list
        $attendanceList = $enrollments->map(function ($enrollment) use ($session) {
            $attendance = $session->attendance->firstWhere('student_id', $enrollment->student_id);
            
            return [
                'student_id' => $enrollment->student_id,
                'student' => $enrollment->student,
                'attendance' => $attendance ? [
                    'id' => $attendance->id,
                    'status' => $attendance->status,
                    'note' => $attendance->note,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'session_date' => $session->session_date,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'group' => $session->group,
                ],
                'attendance' => $attendanceList,
            ],
        ]);
    }

    /**
     * Take attendance for a session
     * POST /api/instructor/sessions/{sessionId}/attendance
     */
    public function takeAttendance(Request $request, int $sessionId): JsonResponse
    {
        $instructor = Auth::user();

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|integer|exists:users,id',
            'attendance.*.status' => 'required|string|in:present,absent,late',
            'attendance.*.note' => 'nullable|string|max:500',
        ]);

        // Verify instructor owns the group for this session
        $session = GroupSession::where('id', $sessionId)
            ->with('group')
            ->firstOrFail();

        if ($session->group->instructor_id !== $instructor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify all students belong to the group
        $groupStudentIds = Enrollment::where('group_id', $session->group_id)
            ->where('status', 'approved')
            ->pluck('student_id')
            ->toArray();

        foreach ($validated['attendance'] as $attendanceData) {
            if (!in_array($attendanceData['student_id'], $groupStudentIds)) {
                return response()->json([
                    'success' => false,
                    'message' => "Student {$attendanceData['student_id']} is not enrolled in this group",
                ], 422);
            }
        }

        // Update or create attendance records
        DB::transaction(function () use ($sessionId, $validated, $instructor) {
            foreach ($validated['attendance'] as $attendanceData) {
                Attendance::updateOrCreate(
                    [
                        'group_session_id' => $sessionId,
                        'student_id' => $attendanceData['student_id'],
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'note' => $attendanceData['note'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
        ]);
    }
}

