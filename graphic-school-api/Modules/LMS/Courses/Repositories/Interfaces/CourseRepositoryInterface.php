<?php

namespace Modules\LMS\Courses\Repositories\Interfaces;

use App\Support\Repositories\BaseRepositoryInterface;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage): LengthAwarePaginator;

    public function loadRelations(Course $course, array $relations = []): Course;

    public function syncInstructors(Course $course, array $instructors, array $supervisors): void;

    /**
     * @return \Illuminate\Support\Collection<int, Course>
     */
    public function publicListing(array $filters);

    /**
     * @return \Illuminate\Support\Collection<int, Course>
     */
    public function homeListing(int $limit);
}

