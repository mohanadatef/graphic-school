<?php

namespace Modules\ACL\Roles\Models;

use Modules\ACL\Permissions\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return RoleFactory::new();
    }

    protected $fillable = [
        'name',
        'description',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'bool',
        'is_active' => 'bool',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }
}

