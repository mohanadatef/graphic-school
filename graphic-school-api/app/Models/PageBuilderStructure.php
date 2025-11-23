<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageBuilderStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'structure',
    ];

    protected $casts = [
        'structure' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(PageBuilderPage::class, 'page_id');
    }
}

