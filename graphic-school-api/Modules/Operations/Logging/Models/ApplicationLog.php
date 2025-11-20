<?php

namespace Modules\Operations\Logging\Models;

use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationLog extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'level',
        'message',
        'context',
        'channel',
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

