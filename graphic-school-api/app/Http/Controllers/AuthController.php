<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'user' => UserResource::make($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'user' => UserResource::make($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function logout(LogoutRequest $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out']);
    }
}
