<?php

namespace Modules\Operations\Logging\Database\Factories;

use Modules\Operations\Logging\Models\ActivityLog;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-008: Activity Log Factory
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        $actions = ['created', 'updated', 'deleted'];
        $modelTypes = [
            'Modules\LMS\Courses\Models\Course',
            'Modules\LMS\Enrollments\Models\Enrollment',
            'Modules\ACL\Users\Models\User',
        ];

        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement($actions),
            'model_type' => fake()->randomElement($modelTypes),
            'model_id' => fake()->numberBetween(1, 100),
            'old_values' => null,
            'new_values' => ['name' => fake()->name(), 'status' => 'active'],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'url' => fake()->url(),
            'method' => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'description' => fake()->sentence(),
        ];
    }
}
