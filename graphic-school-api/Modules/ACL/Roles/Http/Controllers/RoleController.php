<?php

namespace Modules\ACL\Roles\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ACL\Roles\Http\Requests\StoreRoleRequest;
use Modules\ACL\Roles\Http\Requests\UpdateRoleRequest;
use Modules\ACL\Roles\Http\Resources\RoleResource;
use Modules\ACL\Roles\Models\Role;
use Modules\ACL\Roles\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(private RoleService $roleService)
    {
    }

    public function index()
    {
        return RoleResource::collection($this->roleService->list());
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());

        return RoleResource::make($role->load('permissions'))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role = $this->roleService->update($role, $request->validated());

        return RoleResource::make($role);
    }

    public function destroy(Role $role)
    {
        $this->roleService->delete($role);

        return response()->json(['message' => 'Deleted']);
    }
}

