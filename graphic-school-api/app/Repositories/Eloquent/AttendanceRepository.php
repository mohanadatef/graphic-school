<?php

namespace App\Repositories\Eloquent;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttendanceRepository extends BaseRepository implements AttendanceRepositoryInterface
{
    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    public function paginateWithRelations(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with([
            'session.course:id,title',
            'student:id,name,email',
        ]);

        if (! empty($filters['course_id'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('course_id', (int) $filters['course_id']);
            });
        }

        if (! empty($filters['session_id'])) {
            $query->where('session_id', (int) $filters['session_id']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function forStudentCourse(int $studentId, int $courseId)
    {
        return $this->query()
            ->where('student_id', $studentId)
            ->whereHas('session', fn ($q) => $q->where('course_id', $courseId))
            ->with('session:id,course_id,title,session_date')
            ->orderByDesc('created_at')
            ->get();
    }
}


