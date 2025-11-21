<?php

namespace Modules\LMS\Attendance\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AttendanceRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage): LengthAwarePaginator;

    public function forStudentCourse(int $studentId, int $courseId);
}

