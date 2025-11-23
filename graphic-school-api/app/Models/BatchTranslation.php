<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'locale',
        'name',
        'description',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(\App\Models\Batch::class);
    }
}

