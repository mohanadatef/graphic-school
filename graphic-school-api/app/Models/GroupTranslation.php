<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'locale',
        'name',
        'description',
        'extras',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }
}

