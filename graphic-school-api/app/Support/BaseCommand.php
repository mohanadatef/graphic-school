<?php

namespace App\Support;

use App\Support\UseCases\UseCaseLogger;
use App\Contracts\Services\TransactionManagerInterface;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Base Command class
 * Follows Open/Closed Principle - can be extended without modification
 * Follows Dependency Inversion Principle - child classes inject TransactionManager via constructor
 */
abstract class BaseCommand
{
    /**
     * Execute the command
     * Note: Transaction management is now handled by child classes via dependency injection
     * This allows flexibility - some Commands may not need transactions
     */
    public function execute(mixed $input = null): mixed
    {
        $commandClass = get_class($this);
        
        UseCaseLogger::start($commandClass, is_array($input) ? $input : []);

        try {
            // Transaction is now handled by child classes via TransactionManagerInterface
            // This follows OCP - child classes can choose whether to use transactions or not
            $result = $this->handle($input);

            UseCaseLogger::success($commandClass, $result);

            return $result;
        } catch (Throwable $e) {
            UseCaseLogger::failure($commandClass, $e);
            throw $e;
        }
    }

    /**
     * Handle the command logic (to be implemented by child classes)
     */
    abstract protected function handle(mixed $input): mixed;
}

