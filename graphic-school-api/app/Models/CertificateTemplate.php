<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\CertificateTemplateFactory;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'background_image',
        'layout',
        'font_main',
        'font_headings',
        'is_active',
    ];

    protected $casts = [
        'layout' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function newFactory()
    {
        return CertificateTemplateFactory::new();
    }

    public function certificates()
    {
        return $this->hasMany(\Modules\LMS\Certificates\Models\Certificate::class, 'certificate_template_id');
    }
}

