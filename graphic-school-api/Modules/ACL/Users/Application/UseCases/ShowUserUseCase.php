<?php

namespace Modules\ACL\Users\Application\UseCases;

use App\Support\BaseQuery;
use Modules\ACL\Users\Infrastructure\Repositories\Interfaces\UserRepositoryInterface;
use Modules\ACL\Users\Models\User;

class ShowUserUseCase extends BaseQuery
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    protected function handle(mixed $input): User
    {
        /** @var User $user */
        $user = $input;

        return $this->userRepository->loadRole($user);
    }
}

