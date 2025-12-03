<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Core\Localization\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    /**
     * Get all languages
     * GET /api/admin/languages
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $query = Language::query();

        // Apply filters
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Order by sort_order, then name
        $query->orderBy('sort_order')->orderBy('name');

        $languages = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $languages->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $languages->currentPage(),
                    'per_page' => $languages->perPage(),
                    'total' => $languages->total(),
                    'last_page' => $languages->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get single language
     * GET /api/admin/languages/{id}
     */
    public function show(Language $language): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $language,
        ]);
    }

    /**
     * Create new language
     * POST /api/admin/languages
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'image_path' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_rtl' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If setting as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            DB::table('languages')->update(['is_default' => false]);
        }

        $language = Language::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Language created successfully',
            'data' => $language,
        ], 201);
    }

    /**
     * Update language
     * PUT /api/admin/languages/{id}
     */
    public function update(Request $request, Language $language): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('languages')->ignore($language->id)],
            'name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'image_path' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_rtl' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If setting as default, unset other defaults
        if (isset($validated['is_default']) && $validated['is_default']) {
            DB::table('languages')
                ->where('id', '!=', $language->id)
                ->update(['is_default' => false]);
        }

        $language->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Language updated successfully',
            'data' => $language,
        ]);
    }

    /**
     * Delete language
     * DELETE /api/admin/languages/{id}
     */
    public function destroy(Language $language): JsonResponse
    {
        // Prevent deleting default language
        if ($language->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the default language',
            ], 422);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Language deleted successfully',
        ]);
    }

    /**
     * Get active languages (for frontend)
     * GET /api/admin/languages/active
     */
    public function active(): JsonResponse
    {
        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $languages,
        ]);
    }
}

