<?php

namespace Database\Seeders;

use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\Courses\Models\Course;
use App\Models\Group;
use Modules\ACL\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::where('is_published', true)->get();
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->get();
        $admin = User::whereHas('role', fn ($q) => $q->where('name', 'admin'))->first();

        if ($courses->isEmpty() || $students->isEmpty()) {
            return;
        }

        $enrollmentCount = 0;

        foreach ($courses as $course) {
            // Get first group for the course (if exists)
            $group = $course->groups()->first();
            
            // عدد الطلاب المسجلين في كل كورس: 60-80% من max_students
            $maxEnrollments = (int)($course->max_students * (rand(60, 80) / 100));
            $maxEnrollments = max(5, min($maxEnrollments, $students->count())); // على الأقل 5
            
            $courseStudents = $students->random(min($maxEnrollments, $students->count()));
            
            foreach ($courseStudents as $student) {
                // تجنب التسجيل المكرر
                if (Enrollment::where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->exists()) {
                    continue;
                }
                
                // تحديد الحالة بناءً على تاريخ الكورس
                $courseStartDate = $course->start_date ? Carbon::parse($course->start_date) : Carbon::now();
                
                // 80% approved, 15% pending, 5% rejected
                $rand = rand(1, 100);
                
                if ($rand <= 80) {
                    $status = EnrollmentStatus::APPROVED;
                    $canAttend = true;
                    $approvedAt = $courseStartDate->copy()->subDays(rand(1, 30));
                } elseif ($rand <= 95) {
                    $status = EnrollmentStatus::PENDING;
                    $canAttend = false;
                    $approvedAt = null;
                } else {
                    $status = EnrollmentStatus::REJECTED;
                    $canAttend = false;
                    $approvedAt = null;
                }
                
                // تاريخ التسجيل: قبل تاريخ بدء الكورس بـ 1-60 يوم
                $enrollmentDate = $courseStartDate->copy()->subDays(rand(1, 60));
                
                Enrollment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'group_id' => $group?->id,
                        'status' => $status->value,
                        'can_attend' => $canAttend,
                        'approved_by' => $status === EnrollmentStatus::APPROVED ? $admin?->id : null,
                        'approved_at' => $approvedAt,
                        'created_at' => $enrollmentDate,
                        'updated_at' => $enrollmentDate->copy()->addDays(rand(0, 10)),
                    ]
                );
                
                $enrollmentCount++;
            }
        }

        $this->command->info("Enrollments seeded: {$enrollmentCount} enrollments");
    }
}
