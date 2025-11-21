<?php

namespace App\Support\Database;

use App\Contracts\Services\TransactionManagerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Transaction Manager implementation
 * Follows Dependency Inversion Principle - implements interface instead of being static
 */
class TransactionManager implements TransactionManagerInterface
{
    /**
     * Execute a callback within a database transaction
     */
    public function transaction(callable $callback, int $attempts = 1): mixed
    {
        return DB::transaction(function () use ($callback) {
            try {
                return $callback();
            } catch (Throwable $e) {
                Log::error('Transaction failed', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        }, $attempts);
    }

    /**
     * Execute with lock for update
     */
    public function withLock(string $table, mixed $id, callable $callback): mixed
    {
        return DB::transaction(function () use ($table, $id, $callback) {
            $model = DB::table($table)->where('id', $id)->lockForUpdate()->first();
            
            if (!$model) {
                throw new \App\Exceptions\DomainException("Resource not found: {$table}#{$id}", [], 404);
            }

            return $callback($model);
        });
    }

    /**
     * Execute with cache lock (distributed lock)
     */
    public function withCacheLock(string $key, int $seconds, callable $callback): mixed
    {
        $lock = Cache::lock($key, $seconds);

        try {
            return $lock->block($seconds, $callback);
        } catch (Throwable $e) {
            Log::warning('Cache lock timeout', [
                'key' => $key,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            throw new \App\Exceptions\DomainException('Operation timeout. Please try again.', [], 408);
        } finally {
            $lock->release();
        }
    }

    /**
     * Execute with both DB lock and cache lock
     */
    public function withDoubleLock(
        string $cacheKey,
        string $table,
        mixed $id,
        int $lockSeconds,
        callable $callback
    ): mixed {
        return $this->withCacheLock($cacheKey, $lockSeconds, function () use ($table, $id, $callback) {
            return $this->withLock($table, $id, $callback);
        });
    }

    /**
     * Static method for backward compatibility (deprecated - use dependency injection instead)
     * @deprecated Use dependency injection of TransactionManagerInterface instead
     */
    public static function transactionStatic(callable $callback, int $attempts = 1): mixed
    {
        $instance = app(TransactionManagerInterface::class);
        return $instance->transaction($callback, $attempts);
    }
}

