<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ApiResponse
{
    /**
     * Success response
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'status' => $status,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'Error',
        array $errors = [],
        int $status = 400,
        mixed $data = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'errors' => !empty($errors) ? $errors : null,
            'status' => $status,
        ];

        return response()->json($response, $status);
    }

    /**
     * Validation error response
     */
    public static function validationError(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return self::error($message, $errors, 422);
    }

    /**
     * Not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, [], 404);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, [], 401);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, [], 403);
    }

    /**
     * Created response
     */
    public static function created(
        mixed $data = null,
        string $message = 'Created successfully'
    ): JsonResponse {
        return self::success($data, $message, 201);
    }

    /**
     * No content response
     */
    public static function noContent(string $message = 'Success'): JsonResponse
    {
        return self::success(null, $message, 204);
    }

    /**
     * Paginated response
     */
    public static function paginated(
        LengthAwarePaginator|ResourceCollection $paginator,
        string $message = 'Success'
    ): JsonResponse {
        // For ResourceCollection, get the resolved data array directly
        if ($paginator instanceof ResourceCollection) {
            // Get the underlying collection and resolve each resource
            $data = $paginator->collection->map(function ($item) {
                if (method_exists($item, 'resolve')) {
                    return $item->resolve(request());
                }
                return $item instanceof \Illuminate\Http\Resources\Json\JsonResource
                    ? $item->toArray(request())
                    : $item;
            })->values()->toArray();
        } else {
            // For LengthAwarePaginator, get items and resolve if they're resources
            $items = $paginator->items();
            $data = array_map(function ($item) {
                if (method_exists($item, 'resolve')) {
                    return $item->resolve(request());
                }
                return $item instanceof \Illuminate\Http\Resources\Json\JsonResource
                    ? $item->toArray(request())
                    : $item;
            }, $items);
        }

        $meta = [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];

        return self::success($data, $message, 200, $meta);
    }

    /**
     * Collection response
     */
    public static function collection(
        Collection|array $data,
        string $message = 'Success',
        array $meta = []
    ): JsonResponse {
        return self::success($data, $message, 200, $meta);
    }
}

