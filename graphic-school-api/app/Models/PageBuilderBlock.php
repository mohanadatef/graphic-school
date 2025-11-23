<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageBuilderBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}

