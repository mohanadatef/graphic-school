<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBrandingRequest;
use App\Services\BrandingService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class BrandingController extends Controller
{
    public function __construct(private BrandingService $brandingService)
    {
    }

    /**
     * Get all branding settings
     */
    public function index(): JsonResponse
    {
        $branding = $this->brandingService->all();
        return ApiResponse::success($branding, 'Branding settings retrieved successfully');
    }

    /**
     * Get branding settings for frontend
     */
    public function frontend(): JsonResponse
    {
        $branding = $this->brandingService->loadForFrontend();
        return ApiResponse::success($branding, 'Branding settings for frontend retrieved successfully');
    }

    /**
     * Update branding settings
     */
    public function update(UpdateBrandingRequest $request): JsonResponse
    {
        // Get validated data (excluding files)
        $validated = $request->validated();
        
        // Get custom font file before unsetting
        $customFontFile = $request->file('branding.fonts.custom_file');
        
        // Remove file fields from validated data (they're handled separately)
        unset($validated['branding.logo.default'], $validated['branding.logo.dark'], $validated['branding.logo.favicon'], $validated['branding.fonts.custom_file']);

        $this->brandingService->update(
            $validated,
            $request->file('branding.logo.default'),
            $request->file('branding.logo.dark'),
            $request->file('branding.logo.favicon'),
            $customFontFile
        );

        return ApiResponse::success(
            $this->brandingService->loadForFrontend(),
            'Branding settings updated successfully'
        );
    }
}

