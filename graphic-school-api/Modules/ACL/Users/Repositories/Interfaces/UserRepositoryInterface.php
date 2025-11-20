<?php

namespace Modules\ACL\Users\Repositories\Interfaces;

use Modules\ACL\Users\Models\User;
use App\Support\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithRole(array $filters, int $perPage): LengthAwarePaginator;

    public function loadRole(User $user): User;

    public function findByEmailWithRole(string $email): ?User;
}

