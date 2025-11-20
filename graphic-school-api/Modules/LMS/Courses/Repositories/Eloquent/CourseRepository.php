<?php

namespace Modules\LMS\Courses\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    /**
     * Make model instance
     * Follows Liskov Substitution Principle - returns Model, not concrete Course
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Course();
    }

    public function paginateWithRelations(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with(['category', 'instructors', 'sessions']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Load relations for a course model
     */
    public function loadRelations(Course $course, array $relations = []): Course
    {
        return $course->load($relations);
    }

    public function syncInstructors(Course $course, array $instructors, array $supervisors): void
    {

        $syncData = [];

        foreach ($instructors as $id) {
            $syncData[$id] = ['is_supervisor' => in_array($id, $supervisors, true)];
        }

        $course->instructors()->sync($syncData);
    }

    public function publicListing(array $filters): Collection
    {
        $query = $this->query()
            ->with(['category', 'instructors'])
            ->where('is_hidden', false)
            ->where('is_published', true);

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        $onlyUpcoming = array_key_exists('only_upcoming', $filters)
            ? filter_var($filters['only_upcoming'], FILTER_VALIDATE_BOOLEAN)
            : true;

        if ($onlyUpcoming) {
            $query->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '>=', now()->toDateString());
            });
        }

        return $query->orderBy('start_date')->get();
    }

    public function homeListing(int $limit): Collection
    {
        return $this->query()
            ->with('category')
            ->where('is_hidden', false)
            ->where('is_published', true)
            ->orderBy('start_date')
            ->take($limit)
            ->get();
    }
}

