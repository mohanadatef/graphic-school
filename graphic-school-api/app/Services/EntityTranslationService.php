<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EntityTranslationService
{
    protected string $defaultLocale = 'en';
    protected string $fallbackLocale = 'en';

    /**
     * Detect locale from request
     */
    public function detectLocale(?string $queryLocale = null, ?string $headerLocale = null, ?string $userLocale = null): string
    {
        // Priority: Query param > Header > User preference > Default
        if ($queryLocale && in_array($queryLocale, ['ar', 'en'])) {
            return $queryLocale;
        }

        if ($headerLocale) {
            // Parse Accept-Language header (e.g., "ar,en;q=0.9")
            $parsed = $this->parseAcceptLanguage($headerLocale);
            if ($parsed && in_array($parsed, ['ar', 'en'])) {
                return $parsed;
            }
        }

        if ($userLocale && in_array($userLocale, ['ar', 'en'])) {
            return $userLocale;
        }

        // Get default from settings
        $defaultLocale = \Modules\CMS\Settings\Models\Setting::where('key', 'default_locale')->value('value');
        if ($defaultLocale && in_array($defaultLocale, ['ar', 'en'])) {
            return $defaultLocale;
        }

        return $this->defaultLocale;
    }

    /**
     * Parse Accept-Language header
     */
    protected function parseAcceptLanguage(string $header): ?string
    {
        $languages = explode(',', $header);
        if (empty($languages)) {
            return null;
        }

        $first = trim(explode(';', $languages[0])[0]);
        if (str_starts_with($first, 'ar')) {
            return 'ar';
        }
        if (str_starts_with($first, 'en')) {
            return 'en';
        }

        return null;
    }

    /**
     * Load translation for an entity
     */
    public function loadEntityTranslation(Model $entity, ?string $locale = null): ?Model
    {
        $locale = $locale ?? app()->getLocale();
        $translationModel = $this->getTranslationModelName($entity);

        if (!$translationModel) {
            return null;
        }

        $cacheKey = "entity_translation.{$translationModel}.{$entity->getTable()}.{$entity->id}.{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($entity, $translationModel, $locale) {
            $foreignKey = $this->getForeignKey($entity);
            return $translationModel::where($foreignKey, $entity->id)
                ->where('locale', $locale)
                ->first();
        });
    }

    /**
     * Get translated field value with fallback
     */
    public function getTranslatedField(Model $entity, string $field, ?string $locale = null, $default = null)
    {
        $locale = $locale ?? app()->getLocale();

        // Try to get from translation
        $translation = $this->loadEntityTranslation($entity, $locale);
        if ($translation && $translation->$field) {
            return $translation->$field;
        }

        // Fallback to default locale
        if ($locale !== $this->fallbackLocale) {
            $fallbackTranslation = $this->loadEntityTranslation($entity, $this->fallbackLocale);
            if ($fallbackTranslation && $fallbackTranslation->$field) {
                return $fallbackTranslation->$field;
            }
        }

        // Fallback to entity's original field
        if ($entity->$field) {
            return $entity->$field;
        }

        return $default;
    }

    /**
     * Get translation model class name for an entity
     */
    protected function getTranslationModelName(Model $entity): ?string
    {
        $modelClass = get_class($entity);
        $modelName = class_basename($modelClass);

        $translationModels = [
            'Course' => \App\Models\CourseTranslation::class,
            'CourseModule' => \App\Models\CourseModuleTranslation::class,
            'Session' => \App\Models\SessionTranslation::class,
            'Lesson' => \App\Models\LessonTranslation::class,
            'Page' => \App\Models\PageTranslation::class,
            'FAQ' => \App\Models\FAQTranslation::class,
            'Testimonial' => \App\Models\TestimonialTranslation::class,
            'Slider' => \App\Models\SliderTranslation::class,
            'Category' => \Modules\LMS\Categories\Models\CategoryTranslation::class,
        ];

        return $translationModels[$modelName] ?? null;
    }

    /**
     * Get foreign key name for translation table
     */
    protected function getForeignKey(Model $entity): string
    {
        $modelName = class_basename(get_class($entity));
        $foreignKeys = [
            'Course' => 'course_id',
            'CourseModule' => 'module_id',
            'Session' => 'session_id',
            'Lesson' => 'lesson_id',
            'Page' => 'page_id',
            'FAQ' => 'faq_id',
            'Testimonial' => 'testimonial_id',
            'Slider' => 'slider_id',
            'Category' => 'category_id',
        ];

        return $foreignKeys[$modelName] ?? strtolower($modelName) . '_id';
    }

    /**
     * Clear translation cache for an entity
     */
    public function clearEntityTranslationCache(Model $entity, ?string $locale = null): void
    {
        $translationModel = $this->getTranslationModelName($entity);
        if (!$translationModel) {
            return;
        }

        $locales = $locale ? [$locale] : ['ar', 'en'];
        foreach ($locales as $loc) {
            Cache::forget("entity_translation.{$translationModel}.{$entity->getTable()}.{$entity->id}.{$loc}");
        }
    }

    /**
     * Merge translated fields into entity array
     */
    public function mergeTranslations(array $entityData, Model $entity, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->loadEntityTranslation($entity, $locale);

        if (!$translation) {
            // Try fallback
            if ($locale !== $this->fallbackLocale) {
                $translation = $this->loadEntityTranslation($entity, $this->fallbackLocale);
            }
        }

        if ($translation) {
            $translationArray = $translation->toArray();
            unset($translationArray['id'], $translationArray['locale'], $translationArray['created_at'], $translationArray['updated_at']);
            
            // Merge translation fields, prioritizing translation over original
            foreach ($translationArray as $key => $value) {
                if ($value !== null) {
                    $entityData[$key] = $value;
                }
            }
        }

        return $entityData;
    }

    /**
     * Save translations for an entity
     */
    public function saveTranslations(Model $entity, array $translations): void
    {
        $translationModel = $this->getTranslationModelName($entity);
        if (!$translationModel) {
            return;
        }

        $foreignKey = $this->getForeignKey($entity);

        foreach ($translations as $translationData) {
            if (!isset($translationData['locale']) || !in_array($translationData['locale'], ['ar', 'en'])) {
                continue;
            }

            $locale = $translationData['locale'];
            unset($translationData['locale']); // Remove locale from data

            // Prepare translation data
            $data = [
                $foreignKey => $entity->id,
                'locale' => $locale,
            ];

            // Add translatable fields
            foreach ($translationData as $key => $value) {
                if ($value !== null) {
                    $data[$key] = $value;
                }
            }

            // Update or create translation
            $translationModel::updateOrCreate(
                [
                    $foreignKey => $entity->id,
                    'locale' => $locale,
                ],
                $data
            );

            // Clear cache for this entity and locale
            $this->clearEntityTranslationCache($entity, $locale);
        }
    }

    /**
     * Delete all translations for an entity
     */
    public function deleteTranslations(Model $entity): void
    {
        $translationModel = $this->getTranslationModelName($entity);
        if (!$translationModel) {
            return;
        }

        $foreignKey = $this->getForeignKey($entity);
        $translationModel::where($foreignKey, $entity->id)->delete();

        // Clear cache
        $this->clearEntityTranslationCache($entity);
    }
}

