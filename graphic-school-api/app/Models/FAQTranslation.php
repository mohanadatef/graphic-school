<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQTranslation extends Model
{
    protected $fillable = [
        'faq_id',
        'locale',
        'question',
        'answer',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function faq()
    {
        return $this->belongsTo(FAQ::class, 'faq_id');
    }
}

