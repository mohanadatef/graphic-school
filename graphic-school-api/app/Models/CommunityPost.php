<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACL\Users\Models\User;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'batch_id',
        'group_id',
        'title',
        'body',
        'attachments',
        'is_pinned',
        'is_locked',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class);
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Batch::class);
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'post_id');
    }

    public function likes()
    {
        return $this->morphMany(CommunityLike::class, 'likeable');
    }

    public function tags()
    {
        return $this->belongsToMany(CommunityTag::class, 'community_post_tag', 'post_id', 'tag_id');
    }

    public function reports()
    {
        return $this->morphMany(CommunityReport::class, 'reportable');
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }
}

