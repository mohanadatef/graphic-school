<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\PageBuilderPageFactory;

class PageBuilderPage extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PageBuilderPageFactory::new();
    }

    protected $fillable = [
        'academy_id',
        'slug',
        'title',
        'description',
        'language',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function academy()
    {
        return $this->belongsTo(\Modules\ACL\Users\Models\User::class, 'academy_id');
    }

    public function structure()
    {
        return $this->hasOne(PageBuilderStructure::class, 'page_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}

