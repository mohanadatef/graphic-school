<?php

namespace Modules\Support\Tickets\Models;

use Modules\ACL\Users\Models\User;
use Modules\Support\Tickets\Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return SupportTicketFactory::new();
    }

    protected $fillable = [
        'user_id',
        'type', // bug, change_request, new_feature
        'title',
        'description',
        'status', // open, in_progress, resolved, closed
        'priority', // low, medium, high, urgent
        'assigned_to',
        'attachments',
        'updates', // Status update history
    ];

    protected $casts = [
        'attachments' => 'array',
        'updates' => 'array',
    ];

    /**
     * Get the user who created the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user assigned to the ticket
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeForStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Filter by priority
     */
    public function scopeForPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }
}

