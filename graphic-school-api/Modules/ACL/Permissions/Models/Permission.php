<?php

namespace Modules\ACL\Permissions\Models;

use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role')->withTimestamps();
    }
}

