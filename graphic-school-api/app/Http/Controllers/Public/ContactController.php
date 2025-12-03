<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Send contact message
     * POST /api/public/contact
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Get contact email from website settings
            $settings = \App\Models\WebsiteSetting::getDefault();
            $contactEmail = $settings->contact_settings['email'] ?? config('mail.from.address');

            // Send email (if mail is configured)
            if (config('mail.default') !== 'log') {
                Mail::raw($validated['message'], function ($message) use ($validated, $contactEmail) {
                    $message->to($contactEmail)
                        ->subject($validated['subject'] ?? 'Contact Form Submission')
                        ->replyTo($validated['email'], $validated['name']);
                });
            } else {
                // Log if mail is not configured
                Log::info('Contact form submission', $validated);
            }

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending contact message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again later.',
            ], 500);
        }
    }
}

