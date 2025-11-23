<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Sessions\Models\Session;

class SessionTranslation extends Model
{
    protected $fillable = [
        'session_id',
        'locale',
        'title',
        'note',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}

