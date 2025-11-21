<?php

namespace App\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

abstract class BaseController extends Controller
{
    /**
     * Return success response
     */
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200, array $meta = []): JsonResponse
    {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    /**
     * Return error response
     */
    protected function error(string $message = 'Error', array $errors = [], int $status = 400): JsonResponse
    {
        return ApiResponse::error($message, $errors, $status);
    }

    /**
     * Return paginated response
     */
    protected function paginated($paginator, string $message = 'Success'): JsonResponse
    {
        return ApiResponse::paginated($paginator, $message);
    }

    /**
     * Return created response
     */
    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return ApiResponse::created($data, $message);
    }

    /**
     * Return not found response
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    protected function noContent(): JsonResponse
    {
        return ApiResponse::success(null, 'Success', 204);
    }
}

