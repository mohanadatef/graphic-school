<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Enums\AttendanceStatus;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentPaymentStatus;
use App\Enums\EnrollmentStatus;
use App\Repositories\Contracts\CourseReviewRepositoryInterface;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Repositories\Contracts\SessionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class StudentService
{
    public function __construct(
        private EnrollmentRepositoryInterface $enrollmentRepository,
        private SessionRepositoryInterface $sessionRepository,
        private AttendanceRepositoryInterface $attendanceRepository,
        private CourseReviewRepositoryInterface $courseReviewRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function myCourses(User $student): Collection
    {
        return $this->enrollmentRepository->query()
            ->with(['course.category', 'course.instructors'])
            ->where('student_id', $student->id)
            ->where('status', EnrollmentStatus::APPROVED->value)
            ->get();
    }

    public function courseSessions(User $student, Course $course): Collection
    {
        $this->ensureEnrollment($student, $course->id);

        return $course->sessions()->orderBy('session_order')->get();
    }

    public function sessions(User $student, array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->sessionRepository->query()
            ->with(['course:id,title'])
            ->with(['attendance' => fn ($q) => $q->where('student_id', $student->id)])
            ->whereHas('course.enrollments', function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->where('status', EnrollmentStatus::APPROVED->value);
            });

        if (! empty($filters['course_id'])) {
            $query->where('course_id', (int) $filters['course_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['attendance_status'])) {
            $status = $filters['attendance_status'];

            if ($status === AttendanceStatus::PRESENT->value) {
                $query->whereHas('attendance', fn ($q) => $q
                    ->where('student_id', $student->id)
                    ->where('status', AttendanceStatus::PRESENT->value));
            } elseif ($status === AttendanceStatus::ABSENT->value) {
                $query->where(function ($outer) use ($student) {
                    $outer->whereHas('attendance', fn ($q) => $q
                        ->where('student_id', $student->id)
                        ->where('status', AttendanceStatus::ABSENT->value))
                        ->orWhereDoesntHave('attendance', fn ($q) => $q->where('student_id', $student->id));
                });
            }
        }

        return $query
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function courseAttendance(User $student, Course $course): Collection
    {
        $this->ensureEnrollment($student, $course->id);

        return $this->attendanceRepository->forStudentCourse($student->id, $course->id);
    }

    public function profile(User $student): User
    {
        return $this->userRepository->loadRole($student->load('role'));
    }

    public function updateProfile(User $student, array $data, ?UploadedFile $avatar = null): User
    {
        if ($avatar) {
            $data['avatar_path'] = $avatar->store('avatars', 'public');
        }

        unset($data['avatar']);

        $student = $this->userRepository->update($student, $data);

        return $this->userRepository->loadRole($student);
    }

    public function enroll(User $student, Course $course): Enrollment
    {
        if ($course->is_hidden || ! $course->is_published) {
            abort(422, 'Course not available');
        }

        if ($this->enrollmentRepository->existsForStudentCourse($student->id, $course->id)) {
            abort(409, 'Already enrolled');
        }

        return $this->enrollmentRepository->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'payment_status' => EnrollmentPaymentStatus::NOT_PAID->value,
            'status' => EnrollmentStatus::PENDING->value,
            'can_attend' => false,
        ]);
    }

    public function reviewCourse(User $student, Course $course, array $data): CourseReview
    {
        $this->ensureEnrollment($student, $course->id);

        abort_if($course->status !== CourseStatus::COMPLETED->value, 422, 'Course not completed yet');

        return $this->courseReviewRepository->updateOrCreate(
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
    }

    protected function ensureEnrollment(User $student, int $courseId): void
    {
        $hasEnrollment = $this->enrollmentRepository->query()
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereIn('status', [
                EnrollmentStatus::APPROVED->value,
                EnrollmentStatus::PENDING->value,
            ])
            ->exists();

        abort_unless($hasEnrollment, 403, 'غير مشترك في هذا الكورس');
    }
}

