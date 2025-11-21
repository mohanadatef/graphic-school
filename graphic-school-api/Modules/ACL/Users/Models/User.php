<?php

namespace Modules\ACL\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\ACL\Roles\Models\Role;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Attendance\Models\Attendance;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar_path',
        'address',
        'bio',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'bool',
    ];

    protected $with = ['role'];

    protected $appends = ['role_name'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function instructorCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_instructor', 'instructor_id', 'course_id')
            ->withPivot('is_supervisor')
            ->withTimestamps();
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function permissions(): array
    {
        return $this->role ? $this->role->permissions->pluck('slug')->toArray() : [];
    }

    public function hasPermission(string $slug): bool
    {
        return in_array($slug, $this->permissions(), true);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role?->name === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->role?->name === 'student';
    }

    public function getRoleNameAttribute(): ?string
    {
        return $this->role?->name;
    }
}

