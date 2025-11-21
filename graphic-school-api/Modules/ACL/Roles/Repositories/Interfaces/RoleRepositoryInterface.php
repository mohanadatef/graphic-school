<?php

namespace Modules\ACL\Roles\Repositories\Interfaces;

use Modules\ACL\Roles\Models\Role;
use App\Support\Repositories\BaseRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface as SharedRoleRepositoryInterface;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface extends BaseRepositoryInterface, SharedRoleRepositoryInterface
{
    public function listWithPermissions(): Collection;

    public function syncPermissions(Role $role, array $permissionSlugs): void;

    public function loadPermissions(Role $role): Role;

    public function findByName(string $name): ?Role;
}

