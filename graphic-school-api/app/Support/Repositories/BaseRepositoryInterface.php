<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface BaseRepositoryInterface
{
    /**
     * Get query builder
     */
    public function query(): Builder;

    /**
     * Find by ID
     */
    public function find(int $id): ?Model;

    /**
     * Find or fail
     */
    public function findOrFail(int $id): Model;

    /**
     * Get all
     */
    public function all(): Collection;

    /**
     * Create
     */
    public function create(array $data): Model;

    /**
     * Update
     */
    public function update(Model $model, array $data): Model;

    /**
     * Delete
     */
    public function delete(Model $model): bool;

    /**
     * Paginate
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}

