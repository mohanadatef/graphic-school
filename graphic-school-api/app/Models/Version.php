<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Version extends Model
{
    protected $fillable = [
        'versionable_type',
        'versionable_id',
        'version',
        'data',
        'changes',
    ];

    protected $casts = [
        'data' => 'array',
        'changes' => 'array',
    ];

    public function versionable(): MorphTo
    {
        return $this->morphTo();
    }
}

