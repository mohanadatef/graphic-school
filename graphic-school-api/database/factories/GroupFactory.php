<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Batch;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'batch_id' => Batch::factory(),
            'code' => 'GROUP-' . $this->faker->randomLetter(),
            'capacity' => $this->faker->numberBetween(15, 25),
            'room' => 'Room ' . $this->faker->numberBetween(101, 205),
            'instructor_id' => null,
            'is_active' => true,
        ];
    }
}

