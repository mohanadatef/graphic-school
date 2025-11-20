<?php

namespace Modules\ACL\Users\Application\UseCases;

use App\Support\BaseUseCase;
use App\Contracts\Services\TransactionManagerInterface;
use App\Contracts\Services\PasswordHasherInterface;
use Modules\ACL\Users\Application\DTOs\UpdateUserDTO;
use Modules\ACL\Users\Domain\Events\UserUpdated;
use Modules\ACL\Users\Infrastructure\Repositories\Interfaces\UserRepositoryInterface;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\Event;

/**
 * UseCase for updating a user
 * Follows SOLID Principles:
 * - SRP: Only responsible for orchestrating user update
 * - DIP: Depends on interfaces, not concrete implementations
 */
class UpdateUserUseCase extends BaseUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    protected function handle(mixed $input): User
    {
        /** @var User $user */
        /** @var UpdateUserDTO $dto */
        [$user, $dto] = $input;
        $dto->validate();

        return $this->transactionManager->transaction(function () use ($user, $dto) {
            $oldData = $user->toArray();
            $data = $dto->toArray();

            if (isset($data['password'])) {
                $data['password'] = $this->passwordHasher->hash($data['password']); // DIP: Use interface
            }

            $user = $this->userRepository->update($user, $data);
            $user = $this->userRepository->loadRole($user);

            // Dispatch domain event
            Event::dispatch(new UserUpdated(
                $user->id,
                $data
            ));

            return $user;
        });
    }
}

