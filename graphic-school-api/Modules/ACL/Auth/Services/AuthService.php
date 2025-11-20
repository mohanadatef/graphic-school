<?php

namespace Modules\ACL\Auth\Services;

use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Repositories\Contracts\RoleRepositoryInterface;
use Modules\ACL\Users\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        Log::debug('Starting user registration', ['email' => $data['email']]);

        $studentRole = $this->roleRepository->findByName('student');

        if (! $studentRole) {
            Log::error('Student role not found during registration');
            abort(422, trans_db('auth.student_role_not_found', [], null, 'messages'));
        }

        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role_id' => $studentRole->id,
        ]);

        Log::debug('User created', ['user_id' => $user->id]);

        $token = $user->createToken('api-token', ['access-public'])->plainTextToken;

        Log::info('User registration completed', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return ['user' => $user->fresh('role'), 'token' => $token];
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(array $credentials): array
    {
        Log::debug('Starting login process', ['email' => $credentials['email']]);

        $user = $this->userRepository->findByEmailWithRole($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            Log::warning('Invalid login credentials', ['email' => $credentials['email']]);
            abort(401, trans_db('auth.invalid_credentials'));
        }

        if (! $user->is_active) {
            Log::warning('Login attempt for disabled account', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            abort(403, trans_db('auth.account_disabled'));
        }

        $scopes = $user->isAdmin() || $user->isInstructor()
            ? ['access-dashboard', 'access-public']
            : ['access-public'];

        $token = $user->createToken('api-token', $scopes)->plainTextToken;

        Log::info('Login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'scopes' => $scopes,
        ]);

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        Log::debug('Processing logout', ['user_id' => $user->id]);

        $token = $user->token();
        $token?->revoke();

        Log::info('User logged out successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}

