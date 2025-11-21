<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function find(int $id): ?Model;
    public function findByEmail(string $email): ?Model;
    public function findByEmailWithRole(string $email): ?Model;
    public function create(array $data): Model;
    public function update(Model $model, array $data): Model;
    public function delete(Model $model): bool;
    public function loadRole(Model $user): Model;
    public function paginateWithRole(array $filters, int $perPage): LengthAwarePaginator;
}

