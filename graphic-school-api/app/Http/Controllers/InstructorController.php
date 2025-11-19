<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    public function myCourses(Request $request)
    {
        $instructor = $request->user();

        $courses = $instructor->instructorCourses()
            ->with('category')
            ->orderBy('start_date')
            ->get();

        return response()->json($courses);
    }

    public function courseSessions(Request $request, Course $course)
    {
        $this->ensureInstructorAssigned($request->user()->id, $course->id);

        return response()->json(
            $course->sessions()->orderBy('session_order')->get()
        );
    }

    public function sessions(Request $request)
    {
        $instructorId = $request->user()->id;

        $query = Session::with('course:id,title')
            ->whereHas('course.instructors', fn ($q) => $q->where('users.id', $instructorId));

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('session_date', '>=', Carbon::parse($request->get('from_date')));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('session_date', '<=', Carbon::parse($request->get('to_date')));
        }

        $sessions = $query
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->paginate($request->integer('per_page', 10));

        return response()->json($sessions);
    }

    public function storeAttendance(Request $request)
    {
        $data = $request->validate([
            'session_id' => ['required', 'exists:sessions,id'],
            'records' => ['required', 'array'],
            'records.*.student_id' => ['required', 'exists:users,id'],
            'records.*.status' => ['required', 'in:present,absent'],
            'records.*.note' => ['nullable', 'string'],
        ]);

        $session = Session::with('course')->findOrFail($data['session_id']);
        $this->ensureInstructorAssigned($request->user()->id, $session->course_id);

        DB::transaction(function () use ($data, $session) {
            foreach ($data['records'] as $record) {
                Attendance::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'student_id' => $record['student_id'],
                    ],
                    [
                        'status' => $record['status'],
                        'note' => $record['note'] ?? $session->note,
                    ]
                );
            }
        });

        return response()->json(['message' => 'Attendance saved']);
    }

    public function sessionAttendance(Request $request, Session $session)
    {
        $this->ensureInstructorAssigned($request->user()->id, $session->course_id);

        $enrollments = $session->course
            ->enrollments()
            ->where('status', 'approved')
            ->with('student:id,name,email')
            ->get();

        $existing = $session->attendance()->get()->keyBy('student_id');

        $students = $enrollments->map(function (Enrollment $enrollment) use ($existing) {
            $record = $existing->get($enrollment->student_id);

            return [
                'student_id' => $enrollment->student_id,
                'name' => $enrollment->student?->name,
                'email' => $enrollment->student?->email,
                'status' => $record->status ?? 'absent',
                'note' => $record->note ?? null,
            ];
        });

        return response()->json($students);
    }

    public function sessionNote(Request $request, Session $session)
    {
        $this->ensureInstructorAssigned($request->user()->id, $session->course_id);

        $data = $request->validate([
            'note' => ['required', 'string'],
        ]);

        $session->update(['note' => $data['note']]);

        return response()->json($session);
    }

    protected function ensureInstructorAssigned(int $instructorId, int $courseId): void
    {
        $assigned = DB::table('course_instructor')
            ->where('course_id', $courseId)
            ->where('instructor_id', $instructorId)
            ->exists();

        abort_unless($assigned, 403, 'غير مصرح لك بهذه الدورة');
    }
}
