<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Models\CertificateTemplate;
use Modules\LMS\Certificates\Models\Certificate;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class Phase3CertificateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Enrollment $enrollment;
    protected CertificateTemplate $template;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);
        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;

        $this->enrollment = Enrollment::factory()->create([
            'status' => 'approved',
        ]);

        $this->template = CertificateTemplate::factory()->create([
            'is_active' => true,
        ]);
    }

    public function test_admin_can_issue_certificate(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/certificates/issue', [
                'enrollment_id' => $this->enrollment->id,
                'template_id' => $this->template->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('certificates', [
            'student_id' => $this->enrollment->student_id,
            'program_id' => $this->enrollment->program_id,
        ]);
    }

    public function test_public_can_verify_certificate(): void
    {
        $certificate = Certificate::factory()->create([
            'student_id' => $this->enrollment->student_id,
            'program_id' => $this->enrollment->program_id,
            'verification_code' => 'CERT-TEST123',
        ]);

        $response = $this->getJson('/api/certificates/verify', [
            'verification_code' => 'CERT-TEST123',
        ]);

        $response->assertStatus(200);
        $this->assertEquals($certificate->id, $response->json('data.id'));
    }
}

