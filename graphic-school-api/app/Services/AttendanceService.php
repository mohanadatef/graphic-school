<?php

namespace App\Services;

use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttendanceService
{
    public function __construct(private AttendanceRepositoryInterface $attendanceRepository)
    {
    }

    public function paginate(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        return $this->attendanceRepository->paginateWithRelations($filters, $perPage);
    }
}

