<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ACL\Users\Models\User;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'reference_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'color',
        'is_all_day',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_all_day' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReference()
    {
        switch ($this->event_type) {
            case 'session':
                return \Modules\LMS\Sessions\Models\Session::find($this->reference_id);
            default:
                return null;
        }
    }
}

