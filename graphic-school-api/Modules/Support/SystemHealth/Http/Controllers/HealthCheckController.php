<?php

namespace Modules\Support\SystemHealth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Support\SystemHealth\Services\HealthCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    public function __construct(private HealthCheckService $healthService)
    {
    }

    /**
     * Public health check endpoint
     */
    public function check(): JsonResponse
    {
        $health = $this->healthService->performCheck();

        return response()->json($health);
    }

    /**
     * Get current health status
     */
    public function status(): JsonResponse
    {
        $health = $this->healthService->getStatus();

        return response()->json($health);
    }

    /**
     * Check dashboard accessibility (for external API)
     */
    public function checkDashboard(Request $request): JsonResponse
    {
        // Validate API key
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('app.support_api_key');

        if ($apiKey !== $expectedKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $result = $this->healthService->checkDashboard();

        return response()->json($result);
    }

    /**
     * Set dashboard message (for external API)
     */
    public function setMessage(Request $request): JsonResponse
    {
        // Validate API key
        $apiKey = $request->header('X-API-Key');
        $expectedKey = config('app.support_api_key');

        if ($apiKey !== $expectedKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'message' => ['required', 'string'],
            'enabled' => ['nullable', 'boolean'],
        ]);

        // Store message in system_settings
        \Modules\CMS\Settings\Models\SystemSetting::updateOrCreateSetting(
            'dashboard_message',
            [
                'message' => $request->input('message'),
                'enabled' => $request->boolean('enabled', true),
            ],
            'json',
            'system',
            false
        );

        return response()->json(['message' => 'Dashboard message updated']);
    }
}

