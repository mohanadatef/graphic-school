<?php

namespace Modules\ACL\Users\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class CreateUserDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public string $password;
    public ?int $roleId = null;
    public ?string $phone = null;
    public ?string $avatarPath = null;
    public ?string $address = null;
    public ?string $bio = null;
    public bool $isActive = true;

    public function validate(): void
    {
        // Additional validation logic if needed
        if (empty($this->name)) {
            throw new \App\Exceptions\DomainException('Name is required', ['name' => 'Name is required'], 422);
        }

        if (empty($this->email)) {
            throw new \App\Exceptions\DomainException('Email is required', ['email' => 'Email is required'], 422);
        }

        if (empty($this->password) || strlen($this->password) < 8) {
            throw new \App\Exceptions\DomainException('Password must be at least 8 characters', ['password' => 'Password must be at least 8 characters'], 422);
        }
    }
}

