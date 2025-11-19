<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Models\Session;
use App\Repositories\Contracts\SessionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SessionRepository extends BaseRepository implements SessionRepositoryInterface
{
    public function __construct(Session $model)
    {
        parent::__construct($model);
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

    public function deleteByCourse(Course $course): void
    {
        $course->sessions()->delete();
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


