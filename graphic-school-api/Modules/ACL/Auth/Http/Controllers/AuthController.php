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

        // Return user data directly (no cross-module resource dependency)
        return $this->created([
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'role' => $result['user']->role?->name ?? null,
            ],
            'token' => $result['token'],
        ], 'User registered successfully');
    }

    public function login(LoginRequest $request, LoginUserUseCase $useCase): JsonResponse
    {
        $dto = LoginUserDTO::fromArray($request->validated());
        $result = $useCase->execute($dto);

        // Return user data directly (no cross-module resource dependency)
        return $this->success([
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'role' => $result['user']->role?->name ?? null,
            ],
            'token' => $result['token'],
        ], 'Login successful');
    }

    public function logout(LogoutRequest $request, LogoutUserUseCase $useCase)
    {
        $useCase->execute($request->user());

        return $this->success(null, 'Logged out successfully');
    }
}
