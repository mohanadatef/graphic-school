<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $startDate = Carbon::now()->addMonths(rand(1, 6));
        
        return [
            'program_id' => Program::factory(),
            'code' => 'BATCH-' . $this->faker->unique()->numberBetween(1000, 9999),
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addWeeks(rand(8, 16)),
            'max_students' => $this->faker->numberBetween(20, 50),
            'is_active' => true,
        ];
    }
}

