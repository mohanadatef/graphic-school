<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CHANGE-004: Payment Timeline Tests
 */
class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Course $course;
    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role_id' => 1]); // Admin role
        $this->student = User::factory()->create(['role_id' => 3]); // Student role
        $this->course = Course::factory()->create(['price' => 1000]);
        $this->enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'total_amount' => 1000,
        ]);
    }

    public function test_student_can_view_own_payments(): void
    {
        Payment::factory()->count(3)->create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrollment_id' => $this->enrollment->id,
        ]);

        $response = $this->actingAs($this->student, 'api')
            ->getJson('/api/student/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'amount', 'payment_date', 'status'],
                ],
            ]);
    }

    public function test_admin_can_create_payment(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/payments', [
                'enrollment_id' => $this->enrollment->id,
                'amount' => 500,
                'payment_date' => now()->toDateString(),
                'status' => 'completed',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('payments', [
            'enrollment_id' => $this->enrollment->id,
            'amount' => 500,
        ]);
    }

    public function test_admin_can_update_payment(): void
    {
        $payment = Payment::factory()->create([
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->putJson("/api/admin/payments/{$payment->id}", [
                'amount' => 600,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(600, $payment->fresh()->amount);
    }

    public function test_admin_can_get_payment_reports(): void
    {
        Payment::factory()->count(5)->create([
            'enrollment_id' => $this->enrollment->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/payments/reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'summary' => ['total_paid', 'total_pending', 'total_remaining'],
                ],
            ]);
    }
}

