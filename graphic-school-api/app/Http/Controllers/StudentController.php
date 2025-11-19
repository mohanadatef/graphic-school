<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function myCourses(Request $request)
    {
        $student = $request->user();

        $courses = Enrollment::with(['course.category', 'course.instructors'])
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->get();

        return response()->json($courses);
    }

    public function courseSessions(Request $request, Course $course)
    {
        $this->ensureEnrollment($request->user(), $course->id);

        $sessions = $course->sessions()->orderBy('session_order')->get();

        return response()->json($sessions);
    }

    public function sessions(Request $request)
    {
        $student = $request->user();

        $query = Session::with(['course:id,title'])
            ->with(['attendance' => fn ($q) => $q->where('student_id', $student->id)])
            ->whereHas('course.enrollments', function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->where('status', 'approved');
            });

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('attendance_status')) {
            $status = $request->get('attendance_status');

            if ($status === 'present') {
                $query->whereHas('attendance', fn ($q) => $q->where('student_id', $student->id)->where('status', 'present'));
            } elseif ($status === 'absent') {
                $query->where(function ($outer) use ($student) {
                    $outer->whereHas('attendance', fn ($q) => $q->where('student_id', $student->id)->where('status', 'absent'))
                        ->orWhereDoesntHave('attendance', fn ($q) => $q->where('student_id', $student->id));
                });
            }
        }

        $sessions = $query
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->paginate($request->integer('per_page', 10));

        return response()->json($sessions);
    }

    public function courseAttendance(Request $request, Course $course)
    {
        $this->ensureEnrollment($request->user(), $course->id);

        $attendance = Attendance::where('student_id', $request->user()->id)
            ->whereHas('session', fn ($q) => $q->where('course_id', $course->id))
            ->with('session:id,course_id,title,session_date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($attendance);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'file', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_path'] = $path;
        }

        unset($data['avatar']);

        $user->update($data);

        return response()->json($user->fresh());
    }

    public function enroll(Request $request, Course $course)
    {
        $student = $request->user();

        if ($course->is_hidden || ! $course->is_published) {
            return response()->json(['message' => 'Course not available'], 422);
        }

        $existing = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already enrolled'], 409);
        }

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'payment_status' => 'not_paid',
            'status' => 'pending',
            'can_attend' => false,
        ]);

        return response()->json($enrollment, 201);
    }

    public function reviewCourse(Request $request, Course $course)
    {
        $student = $request->user();
        $this->ensureEnrollment($student, $course->id);

        abort_if($course->status !== 'completed', 422, 'Course not completed yet');

        $data = $request->validate([
            'instructor_id' => ['nullable', 'exists:users,id'],
            'rating_course' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_instructor' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        $review = CourseReview::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id' => $course->id,
            ],
            [
                'instructor_id' => $data['instructor_id'] ?? null,
                'rating_course' => $data['rating_course'],
                'rating_instructor' => $data['rating_instructor'],
                'comment' => $data['comment'] ?? null,
            ]
        );

        return response()->json($review);
    }

    protected function ensureEnrollment(User $student, int $courseId): void
    {
        $hasEnrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereIn('status', ['approved', 'pending'])
            ->exists();

        abort_unless($hasEnrollment, 403, 'غير مشترك في هذا الكورس');
    }
}
