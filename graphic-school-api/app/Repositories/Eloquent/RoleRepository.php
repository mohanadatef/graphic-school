<?php

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Support\Collection;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function listWithPermissions(): Collection
    {
        return $this->query()->with('permissions')->orderBy('name')->get();
    }

    public function syncPermissions(Role $role, array $permissionSlugs): void
    {
        $role->permissions()->sync(
            Permission::whereIn('slug', $permissionSlugs)->pluck('id')
        );
    }

    public function loadPermissions(Role $role): Role
    {
        return $role->load('permissions');
    }

    public function findByName(string $name): ?Role
    {
        return $this->query()->where('name', $name)->first();
    }
}


