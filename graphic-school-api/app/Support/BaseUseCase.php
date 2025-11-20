<?php

namespace App\Support;

use App\Support\UseCases\UseCaseLogger;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Base UseCase class
 * Follows Open/Closed Principle - can be extended without modification
 * Follows Dependency Inversion Principle - child classes inject TransactionManager via constructor
 */
abstract class BaseUseCase
{
    /**
     * Execute the use case
     * Note: Transaction management is now handled by child classes via dependency injection
     * This allows flexibility - some UseCases may not need transactions (queries)
     */
    public function execute(mixed $input = null): mixed
    {
        $useCaseClass = get_class($this);
        
        UseCaseLogger::start($useCaseClass, is_array($input) ? $input : []);

        try {
            // Transaction is now handled by child classes via TransactionManagerInterface
            // This follows OCP - child classes can choose whether to use transactions or not
            $result = $this->handle($input);

            UseCaseLogger::success($useCaseClass, $result);

            return $result;
        } catch (Throwable $e) {
            UseCaseLogger::failure($useCaseClass, $e);
            throw $e;
        }
    }

    /**
     * Handle the use case logic (to be implemented by child classes)
     */
    abstract protected function handle(mixed $input): mixed;
}

