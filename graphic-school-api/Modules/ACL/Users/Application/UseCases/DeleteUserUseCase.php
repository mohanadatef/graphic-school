<?php

namespace Modules\ACL\Users\Application\UseCases;

use App\Support\BaseUseCase;
use App\Support\Database\TransactionManager;
use Modules\ACL\Users\Domain\Events\UserDeleted;
use Modules\ACL\Users\Infrastructure\Repositories\Interfaces\UserRepositoryInterface;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\Event;

class DeleteUserUseCase extends BaseUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    protected function handle(mixed $input): void
    {
        /** @var User $user */
        $user = $input;

        if ($user->isAdmin()) {
            throw new \App\Exceptions\DomainException(
                'Cannot delete admin user',
                [],
                422
            );
        }

        TransactionManager::transaction(function () use ($user) {
            $userId = $user->id;
            $userEmail = $user->email;

            $this->userRepository->delete($user);

            // Dispatch domain event
            Event::dispatch(new UserDeleted($userId, $userEmail));
        });
    }
}

