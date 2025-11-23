<?php

namespace Database\Factories;

use App\Models\Invoice;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory(),
            'total_amount' => $this->faker->randomFloat(2, 1000, 10000),
            'paid_amount' => 0,
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'status' => 'unpaid',
        ];
    }
}

