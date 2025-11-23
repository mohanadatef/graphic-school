<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'extras',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'extras' => 'array',
    ];

    /**
     * Relationships
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}

