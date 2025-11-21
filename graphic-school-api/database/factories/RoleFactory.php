<?php

namespace Database\Factories;

use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(),
            'is_system' => false,
            'is_active' => true,
        ];
    }
}

