<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\CMS\Testimonials\Models\Testimonial;

class TestimonialTranslation extends Model
{
    protected $fillable = [
        'testimonial_id',
        'locale',
        'comment',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function testimonial()
    {
        return $this->belongsTo(Testimonial::class);
    }
}

