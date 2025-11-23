<?php

namespace App\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use App\Models\Batch;
use App\Repositories\Interfaces\BatchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BatchRepository extends BaseRepository implements BatchRepositoryInterface
{
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Batch();
    }

    public function findByProgram(int $programId): \Illuminate\Support\Collection
    {
        return $this->query()
            ->where('program_id', $programId)
            ->with(['translations', 'groups'])
            ->orderBy('start_date')
            ->get();
    }

    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with(['translations', 'program', 'groups']);

        if (!empty($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === '1' || $filters['is_active'] === true);
        }

        if (!empty($filters['start_date_from'])) {
            $query->where('start_date', '>=', $filters['start_date_from']);
        }

        if (!empty($filters['start_date_to'])) {
            $query->where('start_date', '<=', $filters['start_date_to']);
        }

        return $query->orderBy('start_date')->paginate($perPage);
    }

    public function loadRelations(Batch $batch, array $relations = []): Batch
    {
        return $batch->load($relations);
    }
}

