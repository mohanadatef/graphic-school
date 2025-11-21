<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            $response = $this->handleApiException($request, $e);
            
            // Add CORS headers to error responses
            return $this->addCorsHeaders($request, $response);
        }

        return parent::render($request, $e);
    }

    /**
     * Add CORS headers to response
     */
    protected function addCorsHeaders($request, $response)
    {
        $origin = $request->header('Origin');
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:5173',
            'http://localhost:8080',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:5173',
            'http://127.0.0.1:8080',
            'http://graphic-school.test',
            'http://graphic-school-api.test',
            'http://graphic-school-frontend.test',
            env('FRONTEND_URL'),
        ];

        $allowedOrigins = array_filter($allowedOrigins);
        
        $allowedOrigin = '*';
        if ($origin && (in_array($origin, $allowedOrigins) || app()->environment(['local', 'development', 'testing']))) {
            $allowedOrigin = $origin;
        }

        return $response
            ->header('Access-Control-Allow-Origin', $allowedOrigin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Accept-Language, X-CSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Expose-Headers', 'Authorization, X-Total-Count, X-Page, X-Per-Page');
    }

    /**
     * Handle API exceptions
     */
    protected function handleApiException($request, Throwable $e)
    {
        // Log exception
        $this->logException($e);

        // Validation Exception
        if ($e instanceof ValidationException) {
            return ApiResponse::validationError(
                $e->errors(),
                $e->getMessage() ?: 'Validation failed'
            );
        }

        // Authentication Exception
        if ($e instanceof AuthenticationException) {
            return ApiResponse::unauthorized('Authentication required');
        }

        // Authorization Exception
        if ($e instanceof AuthorizationException) {
            return ApiResponse::forbidden($e->getMessage() ?: 'Forbidden');
        }

        // Model Not Found
        if ($e instanceof ModelNotFoundException) {
            return ApiResponse::notFound('Resource not found');
        }

        // HTTP Not Found
        if ($e instanceof NotFoundHttpException) {
            return ApiResponse::notFound('Endpoint not found');
        }

        // Method Not Allowed
        if ($e instanceof MethodNotAllowedHttpException) {
            return ApiResponse::error(
                'Method not allowed',
                [],
                405
            );
        }

        // HTTP Exception
        if ($e instanceof HttpException) {
            return ApiResponse::error(
                $e->getMessage() ?: 'HTTP Error',
                [],
                $e->getStatusCode()
            );
        }

        // Database Query Exception
        if ($e instanceof QueryException) {
            return $this->handleDatabaseException($e);
        }

        // Domain Exceptions
        if ($e instanceof \App\Exceptions\DomainException) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->getErrors(),
                $e->getStatusCode()
            );
        }

        // Default: Internal Server Error
        return $this->handleGenericException($e);
    }

    /**
     * Handle database exceptions
     */
    protected function handleDatabaseException(QueryException $e): JsonResponse
    {
        $code = $e->getCode();

        // Integrity constraint violation
        if ($code === '23000') {
            return ApiResponse::error(
                'Database integrity constraint violation',
                [],
                409
            );
        }

        // Connection error
        if (str_contains($e->getMessage(), 'Connection')) {
            return ApiResponse::error(
                'Database connection error',
                [],
                503
            );
        }

        // Default database error
        return ApiResponse::error(
            config('app.debug') ? $e->getMessage() : 'Database error occurred',
            config('app.debug') ? ['query' => $e->getSql(), 'bindings' => $e->getBindings()] : [],
            500
        );
    }

    /**
     * Handle generic exceptions
     */
    protected function handleGenericException(Throwable $e): JsonResponse
    {
        $message = config('app.debug')
            ? $e->getMessage()
            : 'An error occurred';

        $errors = config('app.debug')
            ? [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]
            : [];

        return ApiResponse::error($message, $errors, 500);
    }

    /**
     * Log exception with details
     */
    protected function logException(Throwable $e): void
    {
        $context = [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request' => [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => auth()->id(),
            ],
        ];

        // Log based on severity
        if ($e instanceof ValidationException || $e instanceof AuthenticationException) {
            Log::warning('API Exception', $context);
        } elseif ($e instanceof \App\Exceptions\DomainException) {
            Log::info('Domain Exception', $context);
        } else {
            Log::error('Unhandled Exception', $context);
        }
    }
}
