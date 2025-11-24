<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LanguageSettingsController extends Controller
{
    /**
     * Get language settings
     */
    public function index(): JsonResponse
    {
        $settings = DB::table('website_settings')->first();

        if (!$settings) {
            // Create default settings
            $settings = (object) [
                'default_locale' => 'ar',
                'available_locales' => json_encode(['ar', 'en']),
                'rtl_locales' => json_encode(['ar']),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'default_locale' => $settings->default_locale ?? 'ar',
                'available_locales' => json_decode($settings->available_locales ?? '["ar","en"]', true),
                'rtl_locales' => json_decode($settings->rtl_locales ?? '["ar"]', true),
            ],
        ]);
    }

    /**
     * Update language settings
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'default_locale' => 'required|string|in:ar,en',
            'available_locales' => 'required|array|min:1',
            'available_locales.*' => 'string|in:ar,en',
            'rtl_locales' => 'nullable|array',
            'rtl_locales.*' => 'string|in:ar,en',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = DB::table('website_settings')->first();

        if (!$settings) {
            DB::table('website_settings')->insert([
                'default_locale' => $request->default_locale,
                'available_locales' => json_encode($request->available_locales),
                'rtl_locales' => json_encode($request->rtl_locales ?? ['ar']),
                'default_language' => $request->default_locale,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Fix: Add where clause to update only the first record
            DB::table('website_settings')
                ->where('id', $settings->id)
                ->update([
                    'default_locale' => $request->default_locale,
                    'available_locales' => json_encode($request->available_locales),
                    'rtl_locales' => json_encode($request->rtl_locales ?? ['ar']),
                    'default_language' => $request->default_locale,
                    'updated_at' => now(),
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Language settings updated successfully',
            'data' => [
                'default_locale' => $request->default_locale,
                'available_locales' => $request->available_locales,
                'rtl_locales' => $request->rtl_locales ?? ['ar'],
            ],
        ]);
    }
}

