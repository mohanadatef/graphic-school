<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    /**
     * Get all currencies
     * GET /api/admin/currencies
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $query = Currency::query();

        // Apply filters
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Order by sort_order, then name
        $query->orderBy('sort_order')->orderBy('name');

        $currencies = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $currencies->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $currencies->currentPage(),
                    'per_page' => $currencies->perPage(),
                    'total' => $currencies->total(),
                    'last_page' => $currencies->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get single currency
     * GET /api/admin/currencies/{id}
     */
    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $currency,
        ]);
    }

    /**
     * Create new currency
     * POST /api/admin/currencies
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If setting as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            DB::table('currencies')->update(['is_default' => false]);
        }

        $currency = Currency::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Currency created successfully',
            'data' => $currency,
        ], 201);
    }

    /**
     * Update currency
     * PUT /api/admin/currencies/{id}
     */
    public function update(Request $request, Currency $currency): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('currencies')->ignore($currency->id)],
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If setting as default, unset other defaults
        if (isset($validated['is_default']) && $validated['is_default']) {
            DB::table('currencies')
                ->where('id', '!=', $currency->id)
                ->update(['is_default' => false]);
        }

        $currency->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Currency updated successfully',
            'data' => $currency,
        ]);
    }

    /**
     * Delete currency
     * DELETE /api/admin/currencies/{id}
     */
    public function destroy(Currency $currency): JsonResponse
    {
        // Prevent deleting default currency
        if ($currency->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the default currency',
            ], 422);
        }

        $currency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Currency deleted successfully',
        ]);
    }

    /**
     * Get active currencies (for frontend)
     * GET /api/admin/currencies/active
     */
    public function active(): JsonResponse
    {
        $currencies = Currency::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }
}

