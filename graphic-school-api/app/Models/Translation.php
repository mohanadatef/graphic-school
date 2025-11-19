<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'locale',
        'value',
        'group',
    ];

    public static function getTranslation(string $key, string $locale, string $group = 'messages'): ?string
    {
        $translation = self::where('key', $key)
            ->where('locale', $locale)
            ->where('group', $group)
            ->first();

        return $translation?->value;
    }

    public static function getTranslations(string $locale, string $group = 'messages'): array
    {
        return self::where('locale', $locale)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
