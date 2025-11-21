<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Assessments\Models\QuizAttempt;
use Modules\LMS\Attendance\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CHANGE-007: Advanced Reports Tests
 */
class AdvancedReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $instructor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role_id' => 1]);
        $this->instructor = User::factory()->create(['role_id' => 2]);
    }

    public function test_admin_can_get_top_students_by_grades(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/reports/advanced/top-students/grades');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['student_id', 'student_name', 'overall_average'],
                ],
            ]);
    }

    public function test_admin_can_get_attendance_rate_by_course(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/reports/advanced/attendance-rate/course');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['course_id', 'course_title', 'attendance_rate'],
                ],
            ]);
    }

    public function test_instructor_can_get_performance_report(): void
    {
        $response = $this->actingAs($this->instructor, 'api')
            ->getJson('/api/instructor/reports/performance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'instructor_id',
                    'instructor_name',
                    'average_grades',
                    'attendance_rate',
                    'student_ratings',
                ],
            ]);
    }

    public function test_admin_can_get_engagement_quality(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/reports/advanced/engagement-quality');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_students',
                    'lessons_completed',
                    'total_time_spent_minutes',
                ],
            ]);
    }
}

