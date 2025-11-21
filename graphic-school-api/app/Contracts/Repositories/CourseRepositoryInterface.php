<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface
{
    public function find(int $id): ?Model;
    public function create(array $data): Model;
    public function update(Model $model, array $data): Model;
    public function delete(Model $model): bool;
    public function loadRelations(Model $model, array $relations): Model;
    public function syncInstructors(Model $course, array $instructors, array $supervisors): void;
    public function paginateWithRelations(array $filters, int $perPage): LengthAwarePaginator;
}

