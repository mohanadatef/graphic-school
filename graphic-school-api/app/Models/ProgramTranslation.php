<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'locale',
        'title',
        'description',
        'meta_title',
        'meta_description',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class);
    }
}

