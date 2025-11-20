<?php

namespace Modules\ACL\Users\Infrastructure\Repositories\Interfaces;

use Modules\ACL\Users\Models\User;
use App\Support\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get query builder
     */
    public function query(): Builder;

    /**
     * Paginate with role
     */
    public function paginateWithRole(array $filters, int $perPage): LengthAwarePaginator;

    /**
     * Load role relationship
     * Note: Implementation also satisfies SharedUserRepositoryInterface::loadRole(Model $user): Model
     */
    public function loadRole(User|\Illuminate\Database\Eloquent\Model $user): User|\Illuminate\Database\Eloquent\Model;

    /**
     * Find by email with role
     */
    public function findByEmailWithRole(string $email): ?User;
}

