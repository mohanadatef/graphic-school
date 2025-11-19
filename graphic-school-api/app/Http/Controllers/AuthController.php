<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request)
    {
        Log::info('User registration attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        try {
            $result = $this->authService->register($request->validated());

            Log::info('User registered successfully', [
                'user_id' => $result['user']->id,
                'email' => $result['user']->email,
            ]);

            return response()->json([
                'user' => UserResource::make($result['user']),
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function login(LoginRequest $request)
    {
        Log::info('User login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        try {
            $result = $this->authService->login($request->validated());

            Log::info('User logged in successfully', [
                'user_id' => $result['user']->id,
                'email' => $result['user']->email,
                'role' => $result['user']->role_name ?? $result['user']->role?->name,
            ]);

            return response()->json([
                'user' => UserResource::make($result['user']),
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            Log::warning('User login failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            throw $e;
        }
    }

    public function logout(LogoutRequest $request)
    {
        $user = $request->user();
        
        Log::info('User logout', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        $this->authService->logout($user);

            return response()->json(['message' => trans_db('auth.logout_success')]);
    }
}
