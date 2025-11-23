<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\CMS\Sliders\Models\Slider;

class SliderTranslation extends Model
{
    protected $fillable = [
        'slider_id',
        'locale',
        'title',
        'subtitle',
        'button_text',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }
}

