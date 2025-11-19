<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(
            Role::with('permissions')->orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,slug'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_system' => false,
        ]);

        $role->permissions()->sync(
            Permission::whereIn('slug', $data['permissions'] ?? [])->pluck('id')
        );

        return response()->json($role->load('permissions'), 201);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return response()->json(['message' => 'لا يمكن تعديل هذا الدور'], 422);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,slug'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $role->update($data);

        if (array_key_exists('permissions', $data)) {
            $role->permissions()->sync(
                Permission::whereIn('slug', $data['permissions'])->pluck('id')
            );
        }

        return response()->json($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return response()->json(['message' => 'لا يمكن حذف دور أساسي'], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
