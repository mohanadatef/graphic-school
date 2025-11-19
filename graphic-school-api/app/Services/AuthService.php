<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

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
        $studentRole = $this->roleRepository->findByName('student');

        abort_if(! $studentRole, 422, 'Student role not found');

        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role_id' => $studentRole->id,
        ]);

        $token = $user->createToken('api-token', ['access-public'])->plainTextToken;

        return ['user' => $user->fresh('role'), 'token' => $token];
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmailWithRole($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            abort(401, 'Invalid credentials');
        }

        if (! $user->is_active) {
            abort(403, 'Account disabled');
        }

        $scopes = $user->isAdmin() || $user->isInstructor()
            ? ['access-dashboard', 'access-public']
            : ['access-public'];

        $token = $user->createToken('api-token', $scopes)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $token = $user->token();
        $token?->revoke();
    }
}


