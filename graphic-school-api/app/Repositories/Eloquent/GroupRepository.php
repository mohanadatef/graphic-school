<?php

namespace App\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GroupRepository extends BaseRepository implements GroupRepositoryInterface
{
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Group();
    }

    public function findByBatch(int $batchId): \Illuminate\Support\Collection
    {
        return $this->query()
            ->where('batch_id', $batchId)
            ->with(['translations', 'instructor', 'students', 'instructors'])
            ->orderBy('code')
            ->get();
    }

    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with(['translations', 'batch', 'instructor', 'students', 'instructors']);

        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', $filters['batch_id']);
        }

        if (!empty($filters['instructor_id'])) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === '1' || $filters['is_active'] === true);
        }

        return $query->orderBy('code')->paginate($perPage);
    }

    public function loadRelations(Group $group, array $relations = []): Group
    {
        return $group->load($relations);
    }

    public function syncStudents(Group $group, array $studentIds): void
    {
        $syncData = [];
        foreach ($studentIds as $studentId) {
            $syncData[$studentId] = ['enrolled_at' => now()];
        }
        $group->students()->sync($syncData);
    }

    public function syncInstructors(Group $group, array $instructorIds): void
    {
        $syncData = [];
        foreach ($instructorIds as $instructorId) {
            $syncData[$instructorId] = ['assigned_at' => now()];
        }
        $group->instructors()->sync($syncData);
    }
}

