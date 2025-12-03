<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    /**
     * Get all countries
     * GET /api/admin/countries
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $query = Country::query();

        // Apply filters
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Order by sort_order, then name
        $query->orderBy('sort_order')->orderBy('name');

        $countries = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $countries->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $countries->currentPage(),
                    'per_page' => $countries->perPage(),
                    'total' => $countries->total(),
                    'last_page' => $countries->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get single country
     * GET /api/admin/countries/{id}
     */
    public function show(Country $country): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $country,
        ]);
    }

    /**
     * Create new country
     * POST /api/admin/countries
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:countries,code',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If setting as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            DB::table('countries')->update(['is_default' => false]);
        }

        $country = Country::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Country created successfully',
            'data' => $country,
        ], 201);
    }

    /**
     * Update country
     * PUT /api/admin/countries/{id}
     */
    public function update(Request $request, Country $country): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('countries')->ignore($country->id)],
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If setting as default, unset other defaults
        if (isset($validated['is_default']) && $validated['is_default']) {
            DB::table('countries')
                ->where('id', '!=', $country->id)
                ->update(['is_default' => false]);
        }

        $country->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Country updated successfully',
            'data' => $country,
        ]);
    }

    /**
     * Delete country
     * DELETE /api/admin/countries/{id}
     */
    public function destroy(Country $country): JsonResponse
    {
        // Prevent deleting default country
        if ($country->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the default country',
            ], 422);
        }

        $country->delete();

        return response()->json([
            'success' => true,
            'message' => 'Country deleted successfully',
        ]);
    }

    /**
     * Get active countries (for frontend)
     * GET /api/admin/countries/active
     */
    public function active(): JsonResponse
    {
        $countries = Country::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $countries,
        ]);
    }
}

