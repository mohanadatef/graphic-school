<?php

namespace Modules\LMS\Categories\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\CategoryTranslationFactory;

class CategoryTranslation extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CategoryTranslationFactory::new();
    }

    protected $fillable = [
        'category_id',
        'locale',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

