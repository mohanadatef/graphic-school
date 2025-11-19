<?php

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function listWithPermissions(): Collection;

    public function syncPermissions(Role $role, array $permissionSlugs): void;

    public function loadPermissions(Role $role): Role;

    public function findByName(string $name): ?Role;
}


