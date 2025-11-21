<?php

namespace App\Contracts\Services;

/**
 * Interface for transaction management
 * Follows Dependency Inversion Principle - UseCases depend on abstraction, not concrete implementation
 */
interface TransactionManagerInterface
{
    /**
     * Execute a callback within a database transaction
     */
    public function transaction(callable $callback, int $attempts = 1): mixed;

    /**
     * Execute with lock for update
     */
    public function withLock(string $table, mixed $id, callable $callback): mixed;

    /**
     * Execute with cache lock (distributed lock)
     */
    public function withCacheLock(string $key, int $seconds, callable $callback): mixed;
}

