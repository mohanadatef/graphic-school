<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Support\Collection;

class RoleService
{
    public function __construct(private RoleRepositoryInterface $roleRepository)
    {
    }

    public function list(): Collection
    {
        return $this->roleRepository->listWithPermissions();
    }

    public function create(array $data): Role
    {
        $role = $this->roleRepository->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_system' => false,
        ]);

        $this->roleRepository->syncPermissions($role, $data['permissions'] ?? []);

        return $this->roleRepository->loadPermissions($role);
    }

    public function update(Role $role, array $data): Role
    {
        if ($role->is_system) {
            abort(422, 'لا يمكن تعديل هذا الدور');
        }

        $role = $this->roleRepository->update($role, $data);

        if (array_key_exists('permissions', $data)) {
            $this->roleRepository->syncPermissions($role, $data['permissions']);
        }

        return $this->roleRepository->loadPermissions($role);
    }

    public function delete(Role $role): void
    {
        if ($role->is_system) {
            abort(422, 'لا يمكن حذف دور أساسي');
        }

        $this->roleRepository->delete($role);
    }
}

