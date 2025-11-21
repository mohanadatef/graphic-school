<?php

namespace App\Models;

use Modules\ACL\Users\Models\User;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CHANGE-002: CMS Media Library
 */
class Media extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return MediaFactory::new();
    }

    protected $table = 'media';

    protected $fillable = [
        'name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'disk',
        'type',
        'alt_text',
        'description',
        'uploaded_by',
        'metadata',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the user who uploaded this media
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file URL
     */
    public function getUrlAttribute(): string
    {
        if ($this->disk === 'local') {
            return asset('storage/' . $this->file_path);
        }
        
        // For S3 or other disks, return full URL
        return \Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a document
     */
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Images only
     */
    public function scopeImages($query)
    {
        return $query->where('type', 'image')
            ->orWhere('mime_type', 'like', 'image/%');
    }
}
