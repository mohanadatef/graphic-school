<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'code',
        'category_id',
        'description',
        'image_path',
        'price',
        'start_date',
        'session_count',
        'days_of_week',
        'duration_weeks',
        'max_students',
        'auto_generate_sessions',
        'is_published',
        'is_hidden',
        'status',
        'default_start_time',
        'default_end_time',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
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
}
