<?php

namespace Modules\ACL\Auth\Application\UseCases;

use App\Support\BaseUseCase;
use App\Contracts\Services\TransactionManagerInterface;
use App\Contracts\Services\PasswordHasherInterface;
use Modules\ACL\Auth\Application\DTOs\RegisterUserDTO;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use Modules\ACL\Auth\Domain\Events\UserRegistered;
use Modules\ACL\Users\Domain\Events\UserRegistered as UserModuleUserRegistered;
use Illuminate\Support\Facades\Event;

/**
 * UseCase for registering a user
 * Follows SOLID Principles:
 * - SRP: Only responsible for orchestrating user registration
 * - DIP: Depends on interfaces, not concrete implementations
 */
class RegisterUserUseCase extends BaseUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
        private PasswordHasherInterface $passwordHasher,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    protected function handle(mixed $input): array
    {
        /** @var RegisterUserDTO $dto */
        $dto = $input;
        $dto->validate();

        return $this->transactionManager->transaction(function () use ($dto) {
            $studentRole = $this->roleRepository->findByName('student');

            if (!$studentRole) {
                throw new \App\Exceptions\DomainException(
                    'Student role not found',
                    [],
                    422
                );
            }

            $user = $this->userRepository->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $this->passwordHasher->hash($dto->password), // DIP: Use interface
                'phone' => $dto->phone,
                'address' => $dto->address,
                'role_id' => $studentRole->id,
                'is_active' => true,
            ]);

            $user = $this->userRepository->loadRole($user);

            $token = $user->createToken('api-token', ['access-public'])->plainTextToken;

            // Dispatch domain events
            Event::dispatch(new UserRegistered(
                $user->getKey(),
                $user->email,
                $user->name,
                $user->role_id ?? null
            ));

            // Also dispatch User module event if listener exists
            if (class_exists(UserModuleUserRegistered::class)) {
                Event::dispatch(new UserModuleUserRegistered(
                    $user->getKey(),
                    $user->email,
                    $user->name,
                    $user->role_id ?? null
                ));
            }

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}

