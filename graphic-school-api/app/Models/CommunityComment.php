<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACL\Users\Models\User;

class CommunityComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'body',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(CommunityReply::class, 'comment_id');
    }

    public function likes()
    {
        return $this->morphMany(CommunityLike::class, 'likeable');
    }

    public function reports()
    {
        return $this->morphMany(CommunityReport::class, 'reportable');
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function getRepliesCountAttribute(): int
    {
        return $this->replies()->count();
    }
}

