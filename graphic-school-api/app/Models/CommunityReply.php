<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACL\Users\Models\User;

class CommunityReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'body',
    ];

    public function comment()
    {
        return $this->belongsTo(CommunityComment::class, 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
}

