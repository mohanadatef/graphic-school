<?php

namespace Modules\LMS\Enrollments\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EnrollmentRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage): LengthAwarePaginator;

    public function existsForStudentCourse(int $studentId, int $courseId): bool;

    public function loadRelations(Enrollment $enrollment, array $relations = []): Enrollment;

    public function getStatusForStudentCourse(int $studentId, int $courseId): ?string;
}

