<?php

namespace App\Services;

use App\Contracts\Services\PasswordHasherInterface;
use Illuminate\Support\Facades\Hash;

/**
 * Password hashing service implementation
 * Follows Single Responsibility Principle - only responsible for password hashing
 */
class PasswordHasherService implements PasswordHasherInterface
{
    /**
     * Hash a password
     */
    public function hash(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Check a password against a hash
     */
    public function check(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }
}

