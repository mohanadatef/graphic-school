<?php

namespace Modules\LMS\Enrollments\Services;

use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

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
        Log::info('Creating enrollment', [
            'student_id' => $data['student_id'],
            'course_id' => $data['course_id'],
        ]);

        if ($this->enrollmentRepository->existsForStudentCourse($data['student_id'], $data['course_id'])) {
            Log::warning('Enrollment already exists', [
                'student_id' => $data['student_id'],
                'course_id' => $data['course_id'],
            ]);
            abort(422, 'Student already enrolled');
        }

        $enrollment = $this->enrollmentRepository->create($data);

        Log::info('Enrollment created successfully', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'status' => $enrollment->status,
        ]);

        return $this->enrollmentRepository->loadRelations($enrollment, ['student', 'course']);
    }

    public function update(Enrollment $enrollment, array $data, ?int $approverId = null): Enrollment
    {
        Log::info('Updating enrollment', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'status_change' => $data['status'] ?? null,
        ]);

        if (isset($data['status']) && $data['status'] === EnrollmentStatus::APPROVED->value) {
            $data['can_attend'] = true;
            $data['approved_by'] = $approverId;
            $data['approved_at'] = now();

            Log::info('Enrollment approved', [
                'enrollment_id' => $enrollment->id,
                'approved_by' => $approverId,
            ]);
        }

        $enrollment = $this->enrollmentRepository->update($enrollment, $data);

        Log::info('Enrollment updated successfully', [
            'enrollment_id' => $enrollment->id,
            'status' => $enrollment->status,
        ]);

        return $this->enrollmentRepository
            ->loadRelations($enrollment->fresh(), ['student', 'course']);
    }
}

