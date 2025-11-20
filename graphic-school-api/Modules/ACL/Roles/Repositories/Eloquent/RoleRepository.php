<?php

namespace Modules\ACL\Roles\Repositories\Eloquent;

use Modules\ACL\Permissions\Models\Permission;
use Modules\ACL\Roles\Models\Role;
use Modules\ACL\Roles\Repositories\Interfaces\RoleRepositoryInterface;
use App\Support\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * Make model instance
     * Follows Liskov Substitution Principle - returns Model, not concrete Role
     */
    protected function makeModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Role();
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

    public function all(): \Illuminate\Support\Collection
    {
        return $this->query()->get();
    }
}

