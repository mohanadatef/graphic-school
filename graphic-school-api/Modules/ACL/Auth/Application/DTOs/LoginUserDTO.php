<?php

namespace Modules\ACL\Auth\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class LoginUserDTO extends BaseDTO
{
    public string $email;
    public string $password;

    public function validate(): void
    {
        if (empty($this->email)) {
            throw new \App\Exceptions\DomainException('Email is required', ['email' => 'Email is required'], 422);
        }

        if (empty($this->password)) {
            throw new \App\Exceptions\DomainException('Password is required', ['password' => 'Password is required'], 422);
        }
    }
}

