<?php

namespace Modules\Core\Localization\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\Core\Localization\Models\Language;
use Modules\Core\Localization\Services\TranslationService;
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
     * If group is 'all' or not provided, returns all groups merged
     */
    public function getTranslations(Request $request, ?string $group = null): JsonResponse
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? app()->getLocale();

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        // If group is 'all' or null, return all groups merged
        if ($group === 'all' || $group === null) {
            $allGroups = $this->translationService->getGroups();
            $translations = [];
            
            foreach ($allGroups as $grp) {
                $groupTranslations = $this->translationService->getAll($locale, $grp);
                // Merge with group prefix: group.key => value
                foreach ($groupTranslations as $key => $value) {
                    $translations["{$grp}.{$key}"] = $value;
                }
            }
            
            // Also include default 'messages' group at root level for backward compatibility
            $messages = $this->translationService->getAll($locale, 'messages');
            $translations = array_merge($messages, $translations);

            return ApiResponse::success([
                'locale' => $locale,
                'translations' => $translations,
            ], 'Translations retrieved successfully');
        }

        // Single group
        $translations = $this->translationService->getAll($locale, $group);

        return ApiResponse::success([
            'locale' => $locale,
            'group' => $group,
            'translations' => $translations,
        ], 'Translations retrieved successfully');
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

    /**
     * Get all available locales from database
     */
    public function getAvailableLocales(): JsonResponse
    {
        // Try to get from languages table first
        $languages = Language::active()
            ->ordered()
            ->get();
        
        // If languages table has data, use it
        if ($languages->isNotEmpty()) {
            $availableLocales = $languages->map(function ($language) {
                return [
                    'code' => $language->code,
                    'name' => $language->name,
                    'native_name' => $language->native_name,
                    'image_path' => $language->image_path,
                    'image_url' => $language->image_url,
                ];
            })->toArray();
            
            return ApiResponse::success(
                ['locales' => $availableLocales],
                'Available locales retrieved successfully'
            );
        }
        
        // Fallback: get from translations table
        $locales = $this->translationService->getLocales();
        
        // If no locales in database, return default locales
        if (empty($locales)) {
            $locales = ['en', 'ar'];
        }
        
        // Map locales to language names
        $localeNames = [
            'en' => 'English',
            'ar' => 'العربية',
            'fr' => 'Français',
            'es' => 'Español',
            'de' => 'Deutsch',
        ];
        
        $availableLocales = array_map(function ($locale) use ($localeNames) {
            return [
                'code' => $locale,
                'name' => $localeNames[$locale] ?? ucfirst($locale),
                'native_name' => $localeNames[$locale] ?? ucfirst($locale),
                'image_path' => null,
                'image_url' => null,
            ];
        }, $locales);
        
        return ApiResponse::success(
            ['locales' => $availableLocales],
            'Available locales retrieved successfully'
        );
    }
}

