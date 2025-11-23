<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class Phase3PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected string $adminToken;
    protected string $studentToken;
    protected Invoice $invoice;
    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $studentRole = Role::factory()->create(['name' => 'student']);

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        $this->student = User::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole->id,
        ]);

        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;
        $this->studentToken = $this->student->createToken('test-token')->plainTextToken;

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'approved',
        ]);

        $this->invoice = Invoice::factory()->create([
            'enrollment_id' => $enrollment->id,
            'total_amount' => 5000,
            'status' => 'unpaid',
        ]);

        $this->paymentMethod = PaymentMethod::factory()->create([
            'type' => 'cash',
            'is_active' => true,
        ]);
    }

    public function test_student_can_process_payment(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->postJson('/api/student/invoices/pay', [
                'invoice_id' => $this->invoice->id,
                'payment_method_id' => $this->paymentMethod->id,
                'amount' => 5000,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_transactions', [
            'invoice_id' => $this->invoice->id,
            'status' => 'success',
        ]);
    }

    public function test_admin_can_mark_invoice_as_paid(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson("/api/admin/invoices/{$this->invoice->id}/mark-paid", [
                'payment_method_id' => $this->paymentMethod->id,
                'amount' => 5000,
                'reference_code' => 'TEST-123',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_transactions', [
            'invoice_id' => $this->invoice->id,
            'status' => 'success',
        ]);
    }
}

