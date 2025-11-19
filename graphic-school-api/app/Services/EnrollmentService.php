<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EnrollmentService
{
    public function __construct(private EnrollmentRepositoryInterface $enrollmentRepository)
    {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->enrollmentRepository->paginateWithRelations($filters, $perPage);
    }

    public function create(array $data): Enrollment
    {
        if ($this->enrollmentRepository->existsForStudentCourse($data['student_id'], $data['course_id'])) {
            abort(422, 'Student already enrolled');
        }

        $enrollment = $this->enrollmentRepository->create($data);

        return $this->enrollmentRepository->loadRelations($enrollment, ['student', 'course']);
    }

    public function update(Enrollment $enrollment, array $data, ?int $approverId = null): Enrollment
    {
        if (isset($data['status']) && $data['status'] === EnrollmentStatus::APPROVED->value) {
            $data['can_attend'] = true;
            $data['approved_by'] = $approverId;
            $data['approved_at'] = now();
        }

        $enrollment = $this->enrollmentRepository->update($enrollment, $data);

        return $this->enrollmentRepository
            ->loadRelations($enrollment->fresh(), ['student', 'course']);
    }
}

