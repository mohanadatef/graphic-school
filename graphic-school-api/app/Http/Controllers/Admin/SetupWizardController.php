<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WebsiteActivationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetupWizardController extends Controller
{
    public function __construct(
        private WebsiteActivationService $activationService
    ) {
    }

    /**
     * Get setup status
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'is_activated' => $this->activationService->isActivated(),
            'should_run_setup' => $this->activationService->shouldRunSetup(),
            'settings' => $this->activationService->getPublicSettings(),
        ]);
    }

    /**
     * Save setup step
     */
    public function saveStep(Request $request, int $step): JsonResponse
    {
        $validated = $request->validate([
            'general_info' => 'sometimes|array',
            'academy_name' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:100',
            'default_language' => 'sometimes|string|in:ar,en',
            'default_currency' => 'sometimes|string|in:EGP,SAR,AED,USD,KWD,BHD,OMR,QAR',
            'timezone' => 'sometimes|string|max:100',
            'branding' => 'sometimes|array',
            'logo' => 'sometimes|string',
            'primary_color' => 'sometimes|string|max:7',
            'secondary_color' => 'sometimes|string|max:7',
            'font_main' => 'sometimes|string|max:100',
            'font_headings' => 'sometimes|string|max:100',
            'default_theme' => 'sometimes|string|in:light,dark',
            'enabled_pages' => 'sometimes|array',
            'homepage_template' => 'sometimes|string|in:template-a,template-b',
            'email_settings' => 'sometimes|array',
            'payment_settings' => 'sometimes|array',
        ]);

        // Prepare data for service
        $data = [];
        if ($step === 1) {
            $data['general_info'] = [
                'academy_name' => $validated['academy_name'] ?? null,
                'country' => $validated['country'] ?? null,
            ];
            if (isset($validated['default_language'])) {
                $data['default_language'] = $validated['default_language'];
            }
            if (isset($validated['default_currency'])) {
                $data['default_currency'] = $validated['default_currency'];
            }
            if (isset($validated['timezone'])) {
                $data['timezone'] = $validated['timezone'];
            }
        } elseif ($step === 2) {
            $data['branding'] = $validated['branding'] ?? [];
        } elseif ($step === 3) {
            if (isset($validated['enabled_pages'])) {
                $data['enabled_pages'] = $validated['enabled_pages'];
            }
            if (isset($validated['homepage_template'])) {
                $data['homepage_template'] = $validated['homepage_template'];
            }
        } elseif ($step === 4) {
            $data['email_settings'] = $validated['email_settings'] ?? [];
        } elseif ($step === 5) {
            $data['payment_settings'] = $validated['payment_settings'] ?? [];
        }

        $settings = $this->activationService->saveStep($step, $data);

        return response()->json([
            'message' => 'Step saved successfully',
            'settings' => $settings->getPublicSettings(),
        ]);
    }

    /**
     * Activate default website (skip setup)
     */
    public function activateDefault(): JsonResponse
    {
        $settings = $this->activationService->activateDefaultWebsite();

        return response()->json([
            'message' => 'Default website activated successfully',
            'settings' => $settings->getPublicSettings(),
        ]);
    }

    /**
     * Complete setup wizard
     */
    public function complete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'general_info' => 'sometimes|array',
            'branding' => 'sometimes|array',
            'default_language' => 'sometimes|string|in:ar,en',
            'default_currency' => 'sometimes|string|in:EGP,SAR,AED,USD,KWD,BHD,OMR,QAR',
            'timezone' => 'sometimes|string|max:100',
            'enabled_pages' => 'sometimes|array',
            'homepage_template' => 'sometimes|string|in:template-a,template-b',
            'email_settings' => 'sometimes|array',
            'payment_settings' => 'sometimes|array',
        ]);

        $settings = $this->activationService->completeSetup($validated);

        return response()->json([
            'message' => 'Setup completed successfully',
            'settings' => $settings->getPublicSettings(),
        ]);
    }

    /**
     * Reset website to default
     */
    public function resetToDefault(): JsonResponse
    {
        $settings = $this->activationService->resetToDefault();

        return response()->json([
            'message' => 'Website reset to default successfully',
            'settings' => $settings->getPublicSettings(),
        ]);
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Get email settings from website settings
            $settings = \App\Models\WebsiteSetting::getDefault();
            $emailSettings = $settings->email_settings ?? [];

            if (empty($emailSettings['smtp_host'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'SMTP settings not configured',
                ], 400);
            }

            // Configure mail settings temporarily
            config([
                'mail.mailers.smtp.host' => $emailSettings['smtp_host'] ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => $emailSettings['smtp_port'] ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.username' => $emailSettings['smtp_username'] ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $emailSettings['smtp_password'] ?? config('mail.mailers.smtp.password'),
                'mail.mailers.smtp.encryption' => $emailSettings['smtp_encryption'] ?? config('mail.mailers.smtp.encryption'),
            ]);

            // Send test email
            \Illuminate\Support\Facades\Mail::raw('This is a test email from Graphic School Setup Wizard.', function ($message) use ($validated) {
                $message->to($validated['email'])
                    ->subject('Test Email from Graphic School');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 500);
        }
    }
}

