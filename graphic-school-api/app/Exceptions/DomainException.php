<?php

namespace App\Exceptions;

use Exception;

class DomainException extends Exception
{
    protected array $errors = [];
    protected int $statusCode = 422;

    public function __construct(
        string $message = '',
        array $errors = [],
        int $statusCode = 422,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

