<?php

namespace Modules\Operations\Analytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitable_type',
        'visitable_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Get the parent visitable model (course, instructor, etc.)
     */
    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Get visits for a specific type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('visitable_type', $type);
    }

    /**
     * Scope: Get visits in date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('visited_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Get unique visits (by IP or user_id)
     */
    public function scopeUnique($query)
    {
        return $query->selectRaw('DISTINCT COALESCE(user_id, ip_address) as identifier');
    }
}

