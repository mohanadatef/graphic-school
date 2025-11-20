<?php

namespace Modules\LMS\Categories\Models;

use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

