<?php

namespace Modules\ACL\Roles\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
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
        $roles = $this->roleService->list();
        return ApiResponse::collection(
            RoleResource::collection($roles)->resolve(request()),
            'Roles retrieved successfully'
        );
    }

    public function show(Role $role)
    {
        return ApiResponse::success(
            RoleResource::make($role->load('permissions'))->resolve(request()),
            'Role retrieved successfully'
        );
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());

        return ApiResponse::created(
            RoleResource::make($role->load('permissions'))->resolve(request()),
            'Role created successfully'
        );
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role = $this->roleService->update($role, $request->validated());

        return ApiResponse::success(
            RoleResource::make($role)->resolve(request()),
            'Role updated successfully'
        );
    }

    public function destroy(Role $role)
    {
        $this->roleService->delete($role);

        return ApiResponse::success(
            null,
            'Role deleted successfully'
        );
    }
}

