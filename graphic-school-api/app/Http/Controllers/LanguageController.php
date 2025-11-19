<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __construct(private TranslationService $translationService)
    {
    }

    /**
     * Set the application locale
     */
    public function setLocale(Request $request, string $locale): JsonResponse
    {
        if (!in_array($locale, ['en', 'ar'])) {
            return response()->json([
                'message' => trans_db('messages.validation_error'),
                'error' => 'Invalid locale. Supported locales: en, ar',
            ], 400);
        }

        app()->setLocale($locale);

        return response()->json([
            'message' => trans_db('messages.success'),
            'locale' => $locale,
        ])->cookie('locale', $locale, 60 * 24 * 30); // 30 days
    }

    /**
     * Get all translations for current locale
     */
    public function getTranslations(Request $request, string $group = 'messages'): JsonResponse
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? app()->getLocale();

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        $translations = $this->translationService->getAll($locale, $group);

        return response()->json([
            'locale' => $locale,
            'group' => $group,
            'translations' => $translations,
        ]);
    }

    /**
     * Get current locale
     */
    public function getLocale(): JsonResponse
    {
        return response()->json([
            'locale' => app()->getLocale(),
            'fallback_locale' => config('app.fallback_locale'),
        ]);
    }
}
