<?php

namespace Modules\ACL\Auth\Application\UseCases;

use App\Support\BaseUseCase;
use Modules\ACL\Auth\Domain\Events\UserLoggedOut;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Event;

class LogoutUserUseCase extends BaseUseCase
{
    protected function handle(mixed $input): mixed
    {
        // Input can be user only, or [user, token] array
        if (is_array($input) && count($input) === 2) {
            [$user, $token] = $input;
        } else {
            /** @var Authenticatable $user */
            $user = $input;
            $token = null;
        }

        // If we have the current token, delete it specifically
        // Otherwise, delete all tokens for the user
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            // Fallback: delete all tokens (shouldn't happen in normal flow)
            $user->tokens()->delete();
        }

        // Dispatch domain event
        Event::dispatch(new UserLoggedOut(
            $user->id,
            $user->email
        ));

        return null;
    }
}

