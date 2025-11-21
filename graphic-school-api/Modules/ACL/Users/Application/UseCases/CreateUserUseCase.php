<?php

namespace Modules\ACL\Users\Application\UseCases;

use App\Support\BaseUseCase;
use App\Contracts\Services\TransactionManagerInterface;
use App\Contracts\Services\PasswordHasherInterface;
use Modules\ACL\Users\Application\DTOs\CreateUserDTO;
use Modules\ACL\Users\Domain\Events\UserRegistered;
use Modules\ACL\Users\Infrastructure\Repositories\Interfaces\UserRepositoryInterface;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\Event;

/**
 * UseCase for creating a user
 * Follows SOLID Principles:
 * - SRP: Only responsible for orchestrating user creation
 * - DIP: Depends on interfaces, not concrete implementations
 */
class CreateUserUseCase extends BaseUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    protected function handle(mixed $input): User
    {
        /** @var CreateUserDTO $dto */
        $dto = $input;
        $dto->validate();

        return $this->transactionManager->transaction(function () use ($dto) {
            $data = [
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $this->passwordHasher->hash($dto->password), // DIP: Use interface
                'role_id' => $dto->roleId,
                'phone' => $dto->phone,
                'avatar_path' => $dto->avatarPath,
                'address' => $dto->address,
                'bio' => $dto->bio,
                'is_active' => $dto->isActive,
            ];

            $user = $this->userRepository->create($data);
            $user = $this->userRepository->loadRole($user);

            // Dispatch domain event
            Event::dispatch(new UserRegistered(
                $user->id,
                $user->email,
                $user->name,
                $user->role_id
            ));

            return $user;
        });
    }
}

