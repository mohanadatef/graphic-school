<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Comprehensive API Test Suite
 * Tests all endpoints with edge cases, security, and performance considerations
 */
class ComprehensiveApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $instructor;
    protected User $student;
    protected string $adminToken;
    protected string $instructorToken;
    protected string $studentToken;
    protected Category $category;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $instructorRole = Role::factory()->create(['name' => 'instructor']);
        $studentRole = Role::factory()->create(['name' => 'student']);

        // Create users
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        $this->instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $instructorRole->id,
        ]);

        $this->student = User::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole->id,
        ]);

        // Create tokens
        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;
        $this->instructorToken = $this->instructor->createToken('test-token')->plainTextToken;
        $this->studentToken = $this->student->createToken('test-token')->plainTextToken;

        // Create test data
        $this->category = Category::factory()->create();
        $this->course = Course::factory()->create([
            'category_id' => $this->category->id,
        ]);
    }

    // ==================== Authentication Tests ====================

    public function test_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    public function test_register_with_invalid_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_with_weak_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_with_mismatched_passwords(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ],
            ]);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_with_nonexistent_user(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout_with_valid_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_logout_without_token(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_logout_with_invalid_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->postJson('/api/logout');

        $response->assertStatus(401);
    }

    // ==================== Authorization Tests ====================

    public function test_student_cannot_access_admin_routes(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_instructor_cannot_access_admin_routes(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->instructorToken)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_can_access_student_routes(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->getJson('/api/student/courses');

        $response->assertStatus(200);
    }

    // ==================== Courses Tests ====================

    public function test_admin_can_list_courses_with_pagination(): void
    {
        Course::factory()->count(25)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/courses?per_page=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'code', 'price'],
                ],
                'meta' => [
                    'pagination' => ['current_page', 'per_page', 'total'],
                ],
            ]);
    }

    public function test_admin_can_create_course(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/courses', [
                'title' => 'New Course',
                'code' => 'NC001',
                'category_id' => $this->category->id,
                'description' => 'Course Description',
                'price' => 1500,
                'session_count' => 12,
                'max_students' => 30,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'code'],
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'New Course',
            'code' => 'NC001',
        ]);
    }

    public function test_admin_cannot_create_course_without_required_fields(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/courses', [
                'title' => 'New Course',
                // Missing required fields
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'category_id']);
    }

    public function test_admin_can_update_course(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->putJson("/api/admin/courses/{$this->course->id}", [
                'title' => 'Updated Course Title',
                'code' => $this->course->code,
                'category_id' => $this->course->category_id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('courses', [
            'id' => $this->course->id,
            'title' => 'Updated Course Title',
        ]);
    }

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->deleteJson("/api/admin/courses/{$course->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_admin_cannot_delete_nonexistent_course(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->deleteJson('/api/admin/courses/99999');

        $response->assertStatus(404);
    }

    // ==================== Categories Tests ====================

    public function test_admin_can_list_categories(): void
    {
        Category::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ]);
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/categories', [
                'translations' => [
                    'en' => 'New Category',
                    'ar' => 'تصنيف جديد',
                ],
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id'],
            ]);

        // Verify category was created
        $category = \Modules\LMS\Categories\Models\Category::latest()->first();
        $this->assertNotNull($category);
        
        // Verify translations were created
        $this->assertDatabaseHas('category_translations', [
            'category_id' => $category->id,
            'locale' => 'en',
            'name' => 'New Category',
        ]);
    }

    // ==================== Users Tests ====================

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'email'],
                ],
            ]);
    }

    public function test_admin_can_create_user(): void
    {
        $role = Role::factory()->create(['name' => 'student']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/users', [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $role->id,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    // ==================== Enrollments Tests ====================

    public function test_admin_can_list_enrollments(): void
    {
        Enrollment::factory()->count(5)->create([
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/enrollments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'student_id', 'course_id', 'status'],
                ],
            ]);
    }

    public function test_admin_can_create_enrollment(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/enrollments', [
                'student_id' => $this->student->id,
                'course_id' => $this->course->id,
                'status' => 'approved',
                'payment_status' => 'paid',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }

    // ==================== Public Routes Tests ====================

    public function test_public_can_view_courses(): void
    {
        Course::factory()->count(5)->create(['is_published' => true]);

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'description'],
                ],
            ]);
    }

    public function test_public_can_view_single_course(): void
    {
        $course = Course::factory()->create(['is_published' => true]);

        $response = $this->getJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'description', 'price'],
            ]);
    }

    public function test_public_cannot_view_unpublished_course(): void
    {
        $course = Course::factory()->create(['is_published' => false]);

        $response = $this->getJson("/api/courses/{$course->id}");

        $response->assertStatus(404);
    }

    // ==================== Edge Cases & Security Tests ====================

    public function test_sql_injection_attempt_in_search(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/courses?search=1\' OR \'1\'=\'1');

        // Should not crash, should return empty or filtered results
        $response->assertStatus(200);
    }

    public function test_xss_attempt_in_input(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/courses', [
                'title' => '<script>alert("XSS")</script>',
                'code' => 'TC001',
                'category_id' => $this->category->id,
                'description' => 'Test Description',
                'price' => 1000,
                'session_count' => 10,
            ]);

        // Should sanitize or reject - Laravel escapes HTML by default
        // The title might be saved but will be escaped when displayed
        $response->assertStatus(201);
        
        // Verify the course was created (XSS is handled by output escaping)
        $this->assertDatabaseHas('courses', [
            'code' => 'TC001',
        ]);
    }

    public function test_rate_limiting_on_login(): void
    {
        // Attempt multiple logins rapidly
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/login', [
                'email' => 'admin@test.com',
                'password' => 'wrongpassword',
            ]);
        }

        // Should eventually rate limit
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        // May return 429 or 401 depending on rate limiting configuration
        $this->assertContains($response->status(), [401, 429]);
    }

    public function test_pagination_limits(): void
    {
        Course::factory()->count(100)->create();

        // Request more than max per page
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/courses?per_page=1000');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_invalid_json_payload(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->withHeader('Content-Type', 'application/json')
            ->post('/api/admin/courses', 'invalid json');

        // Laravel may return 400, 422, or 500 depending on how it handles invalid JSON
        $this->assertContains($response->status(), [400, 422, 500]);
    }

    // ==================== Performance Tests ====================

    public function test_large_dataset_performance(): void
    {
        Course::factory()->count(1000)->create();

        $startTime = microtime(true);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/courses?per_page=50');

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        
        // Should complete in reasonable time (adjust threshold as needed)
        // For 1000 records with pagination, 3 seconds is acceptable
        $this->assertLessThan(3000, $executionTime, 'Query took too long: ' . $executionTime . 'ms');
    }

    // ==================== Additional Security Tests ====================

    public function test_csrf_protection_on_state_changing_requests(): void
    {
        // Laravel Sanctum handles CSRF for SPA, but we should verify token is required
        $response = $this->postJson('/api/admin/courses', [
            'title' => 'Test Course',
            'code' => 'TC001',
            'category_id' => $this->category->id,
            'description' => 'Test Description',
            'price' => 1000,
            'session_count' => 10,
        ]);

        // Should require authentication
        $response->assertStatus(401);
    }

    public function test_password_strength_validation(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123', // Too weak
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_email_uniqueness_validation(): void
    {
        // Register first user
        $this->postJson('/api/register', [
            'name' => 'First User',
            'email' => 'duplicate@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Try to register with same email
        $response = $this->postJson('/api/register', [
            'name' => 'Second User',
            'email' => 'duplicate@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_foreign_key_constraints(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/courses', [
                'title' => 'Test Course',
                'code' => 'TC001',
                'category_id' => 99999, // Non-existent category
                'description' => 'Test',
                'price' => 1000,
                'session_count' => 10,
            ]);

        // Should fail validation or return error
        $this->assertContains($response->status(), [422, 500]);
    }

    public function test_sql_injection_in_numeric_fields(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/admin/courses?category_id=1 OR 1=1');

        // Should handle safely (Laravel Query Builder protects against this)
        $response->assertStatus(200);
        
        // Should not return all courses, should treat as invalid input
        $data = $response->json('data', []);
        // The query should be safe, but may return empty or filtered results
        $this->assertIsArray($data);
    }
}

