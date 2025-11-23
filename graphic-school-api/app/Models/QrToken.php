<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public static function generate(int $sessionId, int $expiryMinutes = 5): self
    {
        // Delete old tokens for this session
        static::where('session_id', $sessionId)->delete();

        return static::create([
            'session_id' => $sessionId,
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);
    }
}

