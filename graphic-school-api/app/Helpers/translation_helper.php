<?php

if (!function_exists('trans_db')) {
    /**
     * Get translation from database
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @param string $group
     * @return string
     */
    function trans_db(string $key, array $replace = [], ?string $locale = null, string $group = 'messages'): string
    {
        return app(\App\Services\TranslationService::class)->get($key, $replace, $locale, $group);
    }
}

