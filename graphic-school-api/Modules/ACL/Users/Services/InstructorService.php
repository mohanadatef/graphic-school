<?php

namespace Modules\ACL\Users\Services;

use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Attendance\Repositories\Interfaces\AttendanceRepositoryInterface;
use Modules\LMS\Enrollments\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Modules\LMS\Sessions\Repositories\Interfaces\SessionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InstructorService
{
    public function __construct(
        private SessionRepositoryInterface $sessionRepository,
        private AttendanceRepositoryInterface $attendanceRepository,
        private EnrollmentRepositoryInterface $enrollmentRepository
    ) {
    }

    public function myCourses(User $instructor): Collection
    {
        return $instructor->instructorCourses()
            ->with('category')
            ->orderBy('start_date')
            ->get();
    }

    public function courseSessions(User $instructor, Course $course): Collection
    {
        $this->ensureInstructorAssigned($instructor->id, $course->id);

        return $course->sessions()->orderBy('session_order')->get();
    }

    public function sessions(User $instructor, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->sessionRepository->query()
            ->with('course:id,title')
            ->whereHas('course.instructors', fn ($q) => $q->where('users.id', $instructor->id));

        if (! empty($filters['course_id'])) {
            $query->where('course_id', (int) $filters['course_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('session_date', '>=', Carbon::parse($filters['from_date']));
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('session_date', '<=', Carbon::parse($filters['to_date']));
        }

        return $query
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function storeAttendance(User $instructor, array $data): void
    {
        $session = $this->sessionRepository->query()->with('course')->findOrFail($data['session_id']);
        $this->ensureInstructorAssigned($instructor->id, $session->course_id);

        DB::transaction(function () use ($data, $session) {
            foreach ($data['records'] as $record) {
                $this->attendanceRepository->query()->updateOrCreate(
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
    }

    public function sessionAttendance(User $instructor, Session $session): Collection
    {
        $this->ensureInstructorAssigned($instructor->id, $session->course_id);

        $enrollments = $this->enrollmentRepository->query()
            ->where('course_id', $session->course_id)
            ->where('status', EnrollmentStatus::APPROVED->value)
            ->with('student:id,name,email')
            ->get();

        $existing = $session->attendance()->get()->keyBy('student_id');

        return $enrollments->map(function ($enrollment) use ($existing) {
            $record = $existing->get($enrollment->student_id);

            return [
                'student_id' => $enrollment->student_id,
                'name' => $enrollment->student?->name,
                'email' => $enrollment->student?->email,
                'status' => $record->status ?? AttendanceStatus::ABSENT->value,
                'note' => $record->note ?? null,
            ];
        });
    }

    public function updateSessionNote(User $instructor, Session $session, string $note): Session
    {
        $this->ensureInstructorAssigned($instructor->id, $session->course_id);

        $session = $this->sessionRepository->update($session, ['note' => $note]);

        return $this->sessionRepository->loadWithCourse($session);
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

