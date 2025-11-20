<?php

namespace Modules\LMS\Enrollments\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
    /**
     * Make model instance
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Enrollment();
    }

    public function paginateWithRelations(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with(['student', 'course']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['course_id'])) {
            $query->where('course_id', (int) $filters['course_id']);
        }

        if (! empty($filters['student_id'])) {
            $query->where('student_id', (int) $filters['student_id']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function existsForStudentCourse(int $studentId, int $courseId): bool
    {
        return $this->query()
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->exists();
    }

    public function loadRelations(Enrollment $enrollment, array $relations = []): Enrollment
    {
        return $enrollment->load($relations);
    }

    public function getStatusForStudentCourse(int $studentId, int $courseId): ?string
    {
        return $this->query()
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->value('status');
    }
}

