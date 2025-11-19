<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithRole(array $filters, int $perPage): LengthAwarePaginator;

    public function loadRole(User $user): User;

    public function findByEmailWithRole(string $email): ?User;
}


