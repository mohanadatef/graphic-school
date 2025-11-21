<?php

namespace Modules\ACL\Auth\Application\UseCases;

use App\Support\BaseUseCase;
use Modules\ACL\Auth\Domain\Events\UserLoggedOut;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Event;

class LogoutUserUseCase extends BaseUseCase
{
    protected function handle(mixed $input): void
    {
        /** @var Authenticatable $user */
        $user = $input;

        $token = $user->token();
        $token?->revoke();

        // Dispatch domain event
        Event::dispatch(new UserLoggedOut(
            $user->id,
            $user->email
        ));
    }
}

