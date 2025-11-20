<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->makeModel();
    }

    /**
     * Make model instance
     */
    abstract protected function makeModel(): Model;

    /**
     * Get query builder
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Find by ID
     */
    public function find(int $id): ?Model
    {
        return $this->query()->find($id);
    }

    /**
     * Find or fail
     */
    public function findOrFail(int $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * Get all
     */
    public function all(): Collection
    {
        return $this->query()->get();
    }

    /**
     * Create
     */
    public function create(array $data): Model
    {
        return $this->query()->create($data);
    }

    /**
     * Update
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    /**
     * Delete
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Paginate
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage);
    }
}

