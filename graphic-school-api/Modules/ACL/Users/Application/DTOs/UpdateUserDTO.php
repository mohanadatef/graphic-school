<?php

namespace Modules\ACL\Users\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class UpdateUserDTO extends BaseDTO
{
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?int $roleId = null;
    public ?string $phone = null;
    public ?string $avatarPath = null;
    public ?string $address = null;
    public ?string $bio = null;
    public ?bool $isActive = null;

    public function validate(): void
    {
        // Additional validation logic if needed
        if ($this->password !== null && strlen($this->password) < 8) {
            throw new \App\Exceptions\DomainException('Password must be at least 8 characters', ['password' => 'Password must be at least 8 characters'], 422);
        }
    }

    /**
     * Get only changed fields
     */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null);
    }
}

