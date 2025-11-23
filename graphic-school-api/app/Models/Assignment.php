<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'batch_id',
        'group_id',
        'session_id',
        'title',
        'description',
        'due_date',
        'max_grade',
        'created_by',
        'attachments',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'max_grade' => 'decimal:2',
        'attachments' => 'array',
        'is_active' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function logs()
    {
        return $this->hasMany(AssignmentLog::class);
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now();
    }
}

