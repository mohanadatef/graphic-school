<?php

namespace App\Contracts\Services;

/**
 * Interface for password hashing service
 * Follows Dependency Inversion Principle - UseCases depend on abstraction, not concrete implementation
 */
interface PasswordHasherInterface
{
    /**
     * Hash a password
     */
    public function hash(string $password): string;

    /**
     * Check a password against a hash
     */
    public function check(string $password, string $hash): bool;
}

