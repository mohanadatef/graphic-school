<?php

namespace Database\Factories;

use App\Models\Payment;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-004: Payment Factory
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $enrollment = Enrollment::factory()->create();
        
        return [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'course_id' => $enrollment->course_id,
            'amount' => fake()->randomFloat(2, 100, 5000),
            'remaining_amount' => fake()->randomFloat(2, 0, 1000),
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'online']),
            'payment_reference' => fake()->uuid(),
            'description' => fake()->sentence(),
            'payment_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'created_by' => User::factory(),
        ];
    }
}
