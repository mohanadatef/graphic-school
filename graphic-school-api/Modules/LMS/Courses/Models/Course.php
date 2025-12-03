<?php

namespace Modules\LMS\Courses\Models;

use Modules\LMS\Categories\Models\Category;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\GroupSession;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\CourseReviews\Models\CourseReview;
use App\Models\Group;
use App\Models\SessionTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CourseFactory::new();
    }

    protected $fillable = [
        'title',
        'slug',
        'code',
        'category_id',
        'description',
        'image_path',
        'price',
        'start_date',
        'end_date',
        'session_count',
        'days_of_week',
        'duration_weeks',
        'max_students',
        'auto_generate_sessions',
        'is_published',
        'is_hidden',
        'status',
        'delivery_type',
        'default_start_time',
        'default_end_time',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'days_of_week' => 'array',
        'auto_generate_sessions' => 'bool',
        'is_published' => 'bool',
        'is_hidden' => 'bool',
        'default_start_time' => 'string',
        'default_end_time' => 'string',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructor', 'course_id', 'instructor_id')
            ->withPivot('is_supervisor')
            ->withTimestamps();
    }

    /**
     * Groups that belong to this course
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Session templates for this course
     */
    public function sessionTemplates()
    {
        return $this->hasMany(SessionTemplate::class);
    }

    /**
     * Group sessions through groups (for backward compatibility)
     */
    public function groupSessions()
    {
        return $this->hasManyThrough(GroupSession::class, Group::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function testimonials()
    {
        return $this->hasMany(CourseReview::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }


    /**
     * Translation relationships
     * Note: CourseTranslation model must exist in app/Models/CourseTranslation.php
     */
    public function translations()
    {
        return $this->hasMany(\App\Models\CourseTranslation::class, 'course_id');
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(\App\Models\CourseTranslation::class, 'course_id')
            ->where('locale', $locale);
    }

    /**
     * Get translated title
     */
    public function getTranslatedTitleAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        try {
            $translation = $this->translations()->where('locale', $locale)->first();
            return $translation?->title ?? $this->title ?? $this->translations()->first()?->title;
        } catch (\Exception $e) {
            // If translations table doesn't exist or model has issues, return default title
            return $this->title;
        }
    }

    /**
     * Get translated description
     */
    public function getTranslatedDescriptionAttribute(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        try {
            $translation = $this->translations()->where('locale', $locale)->first();
            return $translation?->description ?? $this->description ?? $this->translations()->first()?->description;
        } catch (\Exception $e) {
            // If translations table doesn't exist or model has issues, return default description
            return $this->description;
        }
    }

    /**
     * Get the next upcoming group session for this course
     */
    public function nextGroupSession()
    {
        return $this->groupSessions()
            ->where('session_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->first();
    }

    /**
     * @deprecated Use CourseEndDateCalculatorService instead
     * This method violates Single Responsibility Principle - calculation logic should be in Domain Service
     * Kept for backward compatibility only
     */
    public function calculateEndDate(): ?\Carbon\Carbon
    {
        // Delegate to Domain Service
        $calculator = app(\Modules\LMS\Courses\Domain\Services\CourseEndDateCalculatorService::class);
        return $calculator->calculateEndDate(
            $this->start_date,
            $this->session_count,
            $this->days_of_week ?? []
        );
    }
}

