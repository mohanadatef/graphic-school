<?php

namespace Modules\LMS\Sessions\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Sessions\Repositories\Interfaces\SessionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SessionRepository extends BaseRepository implements SessionRepositoryInterface
{
    /**
     * Make model instance
     * Follows Liskov Substitution Principle - returns Model, not concrete Session
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Session();
    }

    public function paginateForAdmin(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with('course:id,title');

        if (! empty($filters['course_id'])) {
            $query->where('course_id', (int) $filters['course_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('session_date')->paginate($perPage);
    }

    /**
     * Delete sessions by course
     */
    public function deleteByCourse(Course $course): void
    {
        $this->deleteByCourseId($course->id);
    }

    /**
     * Delete sessions by course ID (helper method for shared interface compatibility)
     */
    public function deleteByCourseId(int $courseId): void
    {
        $this->query()->where('course_id', $courseId)->delete();
    }

    public function loadWithCourse(Session $session): Session
    {
        return $session->load('course');
    }

    public function upcomingForHome(int $limit): Collection
    {
        return $this->query()
            ->with('course')
            ->whereDate('session_date', '>=', Carbon::today())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->take($limit)
            ->get();
    }
}

