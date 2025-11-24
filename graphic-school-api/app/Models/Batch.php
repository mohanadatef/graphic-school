<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Database\Factories\BatchFactory;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'code',
        'name',
        'start_date',
        'end_date',
        'status', // upcoming, active, completed
        'max_students',
        'is_active',
        'extras',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'max_students' => 'integer',
        'extras' => 'array',
    ];

    protected static function newFactory()
    {
        return BatchFactory::new();
    }

    /**
     * Relationships
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function schedules()
    {
        return $this->hasMany(BatchSchedule::class);
    }

    public function sessions()
    {
        return $this->hasMany(\Modules\LMS\Sessions\Models\Session::class, 'batch_id');
    }

    public function assignments()
    {
        return $this->hasMany(\Modules\LMS\Assessments\Models\Assignment::class, 'batch_id');
    }

    public function translations()
    {
        return $this->hasMany(BatchTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(BatchTranslation::class)
            ->where('locale', $locale);
    }

    /**
     * Get translated attribute
     */
    public function getTranslated(string $key, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);
        
        if ($translation && $translation->$key) {
            return $translation->$key;
        }
        
        // Fallback to default locale (en)
        $fallbackTranslation = $this->translations->firstWhere('locale', config('app.fallback_locale', 'en'));
        return $fallbackTranslation->$key ?? null;
    }

    /**
     * Boot method - Business logic
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate schedule placeholder when batch is created
        static::created(function ($batch) {
            try {
                // Create a default schedule placeholder only if batch_schedules table exists
                if (Schema::hasTable('batch_schedules')) {
                    $batch->schedules()->create([
                        'day_of_week' => 'monday',
                        'start_time' => '09:00',
                        'end_time' => '17:00',
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail if schedule creation fails (table might not exist yet)
                Log::warning('Failed to create batch schedule: ' . $e->getMessage());
            }
        });

        // Auto-update status based on dates
        static::saving(function ($batch) {
            $now = now();
            if ($batch->start_date && $batch->end_date) {
                if ($now < $batch->start_date) {
                    $batch->status = 'upcoming';
                } elseif ($now >= $batch->start_date && $now <= $batch->end_date) {
                    $batch->status = 'active';
                } else {
                    $batch->status = 'completed';
                }
            }
        });

        // Validate: A batch cannot be active for more than 1 program
        static::saving(function ($batch) {
            // Skip validation if batch is being created (no id yet) or if dates are missing
            if (!$batch->exists || !$batch->start_date || !$batch->program_id) {
                return;
            }
            
            if ($batch->status === 'active') {
                $activeBatches = static::where('id', '!=', $batch->id)
                    ->where('program_id', '!=', $batch->program_id)
                    ->where('status', 'active')
                    ->where(function ($query) use ($batch) {
                        $endDate = $batch->end_date ?? '9999-12-31';
                        $query->where(function ($q) use ($batch, $endDate) {
                            $q->whereBetween('start_date', [$batch->start_date, $endDate])
                                ->orWhereBetween('end_date', [$batch->start_date, $endDate])
                                ->orWhere(function ($subQ) use ($batch, $endDate) {
                                    $subQ->where('start_date', '<=', $batch->start_date)
                                         ->where('end_date', '>=', $endDate);
                                });
                        });
                    })
                    ->exists();

                if ($activeBatches) {
                    throw new \Exception('A batch cannot be active for more than one program at the same time.');
                }
            }
        });
    }

    /**
     * Auto-create attendance slots for each session
     */
    public function createAttendanceSlots()
    {
        foreach ($this->sessions as $session) {
            // Create attendance records for all students in groups
            foreach ($this->groups as $group) {
                foreach ($group->students as $student) {
                    \Modules\LMS\Attendance\Models\Attendance::firstOrCreate([
                        'session_id' => $session->id,
                        'student_id' => $student->id,
                    ], [
                        'status' => 'absent', // Default status
                        'checked_in_at' => null,
                    ]);
                }
            }
        }
    }
}

