<?php

namespace Modules\CMS\Contacts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'bool',
    ];
}

