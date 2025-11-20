<?php

namespace Modules\ACL\Auth\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class RegisterUserDTO extends BaseDTO
{
    public string $name;
    public string $email;
    public string $password;
    public string $passwordConfirmation;
    public ?string $phone = null;
    public ?string $address = null;

    public function validate(): void
    {
        if (empty($this->name)) {
            throw new \App\Exceptions\DomainException('Name is required', ['name' => 'Name is required'], 422);
        }

        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \App\Exceptions\DomainException('Valid email is required', ['email' => 'Valid email is required'], 422);
        }

        if (empty($this->password) || strlen($this->password) < 8) {
            throw new \App\Exceptions\DomainException('Password must be at least 8 characters', ['password' => 'Password must be at least 8 characters'], 422);
        }

        if ($this->password !== $this->passwordConfirmation) {
            throw new \App\Exceptions\DomainException('Passwords do not match', ['password' => 'Passwords do not match'], 422);
        }
    }
}

