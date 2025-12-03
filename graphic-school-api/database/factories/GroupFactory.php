<?php

namespace Database\Factories;

use App\Models\Group;
use Modules\LMS\Courses\Models\Course;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'code' => 'GROUP-' . strtoupper(fake()->unique()->bothify('####')),
            'name' => fake()->words(2, true) . ' Group',
            'capacity' => fake()->numberBetween(15, 25),
            'room' => 'Room ' . fake()->numberBetween(101, 205),
            'instructor_id' => null,
            'is_active' => true,
            'extras' => null,
        ];
    }
}

