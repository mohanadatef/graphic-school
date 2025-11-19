<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    protected string $defaultLocale = 'en';
    protected string $fallbackLocale = 'en';

    public function get(string $key, array $replace = [], ?string $locale = null, string $group = 'messages'): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // Try to get from cache first
        $cacheKey = "translation.{$locale}.{$group}.{$key}";
        $translation = Cache::remember($cacheKey, 3600, function () use ($key, $locale, $group) {
            return Translation::getTranslation($key, $locale, $group);
        });

        // If not found, try fallback locale
        if (!$translation && $locale !== $this->fallbackLocale) {
            $translation = Translation::getTranslation($key, $this->fallbackLocale, $group);
        }

        // If still not found, return the key
        if (!$translation) {
            return $key;
        }

        // Replace placeholders
        foreach ($replace as $search => $value) {
            $translation = str_replace(':' . $search, $value, $translation);
        }

        return $translation;
    }

    public function getAll(string $locale = null, string $group = 'messages'): array
    {
        $locale = $locale ?? app()->getLocale();
        
        $cacheKey = "translations.{$locale}.{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($locale, $group) {
            return Translation::getTranslations($locale, $group);
        });
    }

    public function set(string $key, string $value, string $locale, string $group = 'messages'): void
    {
        Translation::updateOrCreate(
            [
                'key' => $key,
                'locale' => $locale,
                'group' => $group,
            ],
            [
                'value' => $value,
            ]
        );

        // Clear cache
        Cache::forget("translation.{$locale}.{$group}.{$key}");
        Cache::forget("translations.{$locale}.{$group}");
    }

    public function clearCache(?string $locale = null, ?string $group = null): void
    {
        if ($locale && $group) {
            Cache::forget("translations.{$locale}.{$group}");
        } else {
            // Clear all translation caches
            Cache::flush();
        }
    }

    public function paginate(array $filters, int $perPage = 15)
    {
        $query = Translation::query();

        if (isset($filters['locale'])) {
            $query->where('locale', $filters['locale']);
        }

        if (isset($filters['group'])) {
            $query->where('group', $filters['group']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('group')
            ->orderBy('key')
            ->orderBy('locale')
            ->paginate($perPage);
    }

    public function create(array $data): Translation
    {
        $translation = Translation::create([
            'key' => $data['key'],
            'locale' => $data['locale'],
            'value' => $data['value'],
            'group' => $data['group'] ?? 'messages',
        ]);

        // Clear cache
        $this->clearCache($translation->locale, $translation->group);

        return $translation;
    }

    public function update(Translation $translation, array $data): Translation
    {
        $translation->update($data);

        // Clear cache
        $this->clearCache($translation->locale, $translation->group);

        return $translation->fresh();
    }

    public function delete(Translation $translation): void
    {
        $locale = $translation->locale;
        $group = $translation->group;

        $translation->delete();

        // Clear cache
        $this->clearCache($locale, $group);
    }

    public function getGroups(): array
    {
        return Translation::distinct()
            ->pluck('group')
            ->sort()
            ->values()
            ->toArray();
    }

    public function getLocales(): array
    {
        return Translation::distinct()
            ->pluck('locale')
            ->sort()
            ->values()
            ->toArray();
    }
}

