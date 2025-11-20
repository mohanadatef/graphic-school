<?php

namespace Modules\LMS\Sessions\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SessionRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForAdmin(array $filters, int $perPage): LengthAwarePaginator;

    public function deleteByCourse(Course $course): void;

    public function loadWithCourse(Session $session): Session;

    public function upcomingForHome(int $limit): Collection;
}

