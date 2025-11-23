<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageBuilderTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'structure',
        'is_default',
    ];

    protected $casts = [
        'structure' => 'array',
        'is_default' => 'boolean',
    ];
}

