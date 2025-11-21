<?php

namespace Modules\LMS\Courses\Models;

use Modules\LMS\Categories\Models\Category;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\CourseReviews\Models\CourseReview;
use Modules\LMS\Curriculum\Models\CourseModule;
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

    public function sessions()
    {
        return $this->hasMany(Session::class);
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

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    /**
     * Get the next upcoming session for this course
     */
    public function nextSession()
    {
        return $this->sessions()
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

