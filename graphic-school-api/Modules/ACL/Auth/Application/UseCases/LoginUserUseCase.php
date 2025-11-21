<?php

namespace Modules\ACL\Auth\Application\UseCases;

use App\Support\BaseUseCase;
use App\Contracts\Services\PasswordHasherInterface;
use Modules\ACL\Auth\Application\DTOs\LoginUserDTO;
use Modules\ACL\Auth\Domain\Events\UserLoggedIn;
use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Event;

/**
 * UseCase for logging in a user
 * Follows SOLID Principles:
 * - SRP: Only responsible for orchestrating user login
 * - DIP: Depends on interfaces, not concrete implementations
 */
class LoginUserUseCase extends BaseUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {
    }

    protected function handle(mixed $input): array
    {
        /** @var LoginUserDTO $dto */
        $dto = $input;
        $dto->validate();

        $user = $this->userRepository->findByEmailWithRole($dto->email);

        if (!$user || !$this->passwordHasher->check($dto->password, $user->password)) { // DIP: Use interface
            throw new \App\Exceptions\DomainException(
                'Invalid credentials',
                ['email' => 'Invalid email or password'],
                401
            );
        }

        if (!$user->is_active) {
            throw new \App\Exceptions\DomainException(
                'Account is disabled',
                ['account' => 'Your account has been disabled'],
                403
            );
        }

        $scopes = $user->isAdmin() || $user->isInstructor()
            ? ['access-dashboard', 'access-public']
            : ['access-public'];

        $token = $user->createToken('api-token', $scopes)->plainTextToken;

        // Dispatch domain event
        Event::dispatch(new UserLoggedIn(
            $user->id,
            $user->email,
            $scopes
        ));

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}

