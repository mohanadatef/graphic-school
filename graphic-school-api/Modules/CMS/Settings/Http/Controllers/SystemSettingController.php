<?php

namespace Modules\CMS\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CMS\Settings\Services\SystemSettingService;
use Modules\CMS\Settings\Http\Requests\UpdateSystemSettingRequest;
use Illuminate\Http\JsonResponse;

class SystemSettingController extends Controller
{
    public function __construct(private SystemSettingService $settingService)
    {
    }

    /**
     * Get all settings
     */
    public function index(): JsonResponse
    {
        $groups = [
            'general' => $this->settingService->getByGroup('general'),
            'colors' => $this->settingService->getByGroup('colors'),
            'sections' => $this->settingService->getByGroup('sections'),
            'languages' => $this->settingService->getByGroup('languages'),
            'currency' => $this->settingService->getByGroup('currency'),
        ];

        return response()->json($groups);
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): JsonResponse
    {
        $settings = $this->settingService->getByGroup($group);

        return response()->json($settings);
    }

    /**
     * Get public settings (for frontend)
     */
    public function getPublic(): JsonResponse
    {
        $settings = $this->settingService->getPublicSettings();

        return response()->json($settings);
    }

    /**
     * Update settings
     */
    public function update(UpdateSystemSettingRequest $request): JsonResponse
    {
        $this->settingService->update(
            $request->validated(),
            $request->file('logo')
        );

        return response()->json(['message' => 'Settings updated successfully']);
    }

    /**
     * Update colors
     */
    public function updateColors(UpdateSystemSettingRequest $request): JsonResponse
    {
        $this->settingService->updateColors($request->validated('colors'));

        return response()->json(['message' => 'Colors updated successfully']);
    }

    /**
     * Update section visibility
     */
    public function updateSectionVisibility(UpdateSystemSettingRequest $request): JsonResponse
    {
        $this->settingService->updateSectionVisibility(
            $request->input('section'),
            $request->boolean('visible')
        );

        return response()->json(['message' => 'Section visibility updated']);
    }
}

