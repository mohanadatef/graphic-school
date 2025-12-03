<?php

namespace Modules\ACL\Auth\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\ACL\Auth\Http\Requests\LoginRequest;
use Modules\ACL\Auth\Http\Requests\LogoutRequest;
use Modules\ACL\Auth\Http\Requests\RegisterRequest;
use Modules\ACL\Auth\Application\UseCases\RegisterUserUseCase;
use Illuminate\Http\JsonResponse;
use Modules\ACL\Auth\Application\UseCases\LoginUserUseCase;
use Modules\ACL\Auth\Application\UseCases\LogoutUserUseCase;
use Modules\ACL\Auth\Application\DTOs\RegisterUserDTO;
use Modules\ACL\Auth\Application\DTOs\LoginUserDTO;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request, RegisterUserUseCase $useCase): JsonResponse
    {
        $dto = RegisterUserDTO::fromArray($request->validated());
        $result = $useCase->execute($dto);

        $user = $result['user'];
        
        // Ensure role is always a string
        $role = $this->getRoleString($user);

        // Return user data directly (no cross-module resource dependency)
        return $this->created([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
            ],
            'token' => $result['token'],
        ], 'User registered successfully');
    }

    public function login(LoginRequest $request, LoginUserUseCase $useCase): JsonResponse
    {
        $dto = LoginUserDTO::fromArray($request->validated());
        $result = $useCase->execute($dto);

        $user = $result['user'];
        
        // Ensure role is always a string
        $role = $this->getRoleString($user);

        // Return user data directly (no cross-module resource dependency)
        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
            ],
            'token' => $result['token'],
        ], 'Login successful');
    }

    /**
     * Convert role_id to role string or get role name from relationship
     * 1 → admin or super_admin
     * 2 → instructor
     * 3 → student
     */
    private function getRoleString($user): ?string
    {
        // First try to get role from relationship (preferred method)
        if ($user->relationLoaded('role') && $user->role && $user->role->name) {
            return $user->role->name;
        }

        // If role is not loaded, try to load it
        if ($user->role_id && !$user->relationLoaded('role')) {
            $user->load('role');
            if ($user->role && $user->role->name) {
                return $user->role->name;
            }
        }

        // Last resort: convert role_id to role string (fallback only)
        // Note: This is a fallback - the role relationship should always be loaded
        if ($user->role_id) {
            // Try to get role from database
            $role = \Modules\ACL\Roles\Models\Role::find($user->role_id);
            if ($role && $role->name) {
                return $role->name;
            }
        }

        return null;
    }

    public function logout(LogoutRequest $request, LogoutUserUseCase $useCase)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        
        $useCase->execute($user, $token);

        return $this->success(null, 'Logged out successfully');
    }
}
