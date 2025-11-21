<?php

namespace App\Support;

use App\Support\UseCases\UseCaseLogger;
use Throwable;

abstract class BaseQuery
{
    /**
     * Execute the query
     */
    public function execute(mixed $input = null): mixed
    {
        $queryClass = get_class($this);
        
        UseCaseLogger::start($queryClass, is_array($input) ? $input : []);

        try {
            $result = $this->handle($input);

            UseCaseLogger::success($queryClass, $result);

            return $result;
        } catch (Throwable $e) {
            UseCaseLogger::failure($queryClass, $e);
            throw $e;
        }
    }

    /**
     * Handle the query logic (to be implemented by child classes)
     */
    abstract protected function handle(mixed $input): mixed;
}

