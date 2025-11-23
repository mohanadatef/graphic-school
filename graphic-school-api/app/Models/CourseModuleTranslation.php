<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Curriculum\Models\CourseModule;

class CourseModuleTranslation extends Model
{
    protected $fillable = [
        'module_id',
        'locale',
        'title',
        'description',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }
}

