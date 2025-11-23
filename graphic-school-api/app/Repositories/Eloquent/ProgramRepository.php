<?php

namespace App\Repositories\Eloquent;

use App\Support\Repositories\BaseRepository;
use App\Models\Program;
use App\Repositories\Interfaces\ProgramRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProgramRepository extends BaseRepository implements ProgramRepositoryInterface
{
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Program();
    }

    public function findBySlug(string $slug): ?Program
    {
        return $this->query()->where('slug', $slug)->first();
    }

    public function paginateWithFilters(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->query()->with(['translations', 'batches']);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === '1' || $filters['is_active'] === true);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('sort_order')->orderByDesc('created_at')->paginate($perPage);
    }

    public function loadRelations(Program $program, array $relations = []): Program
    {
        return $program->load($relations);
    }
}

