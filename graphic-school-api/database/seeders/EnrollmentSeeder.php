<?php

namespace Database\Seeders;

use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentPaymentStatus;
use Modules\LMS\Courses\Models\Course;
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
                $courseStartDate = Carbon::parse($course->start_date);
                $now = Carbon::now();
                
                // 70% approved, 15% pending, 10% partially paid, 5% rejected
                $rand = rand(1, 100);
                
                if ($rand <= 70) {
                    $status = EnrollmentStatus::APPROVED;
                    $paymentStatus = EnrollmentPaymentStatus::PAID;
                    $paidAmount = $course->price;
                    $canAttend = true;
                    $approvedAt = $courseStartDate->copy()->subDays(rand(1, 30));
                } elseif ($rand <= 85) {
                    $status = EnrollmentStatus::PENDING;
                    $paymentStatus = EnrollmentPaymentStatus::NOT_PAID;
                    $paidAmount = 0;
                    $canAttend = false;
                    $approvedAt = null;
                } elseif ($rand <= 95) {
                    $status = EnrollmentStatus::APPROVED;
                    $paymentStatus = EnrollmentPaymentStatus::PARTIALLY_PAID;
                    $paidAmount = $course->price * (rand(30, 70) / 100);
                    $canAttend = true;
                    $approvedAt = $courseStartDate->copy()->subDays(rand(1, 30));
                } else {
                    $status = EnrollmentStatus::REJECTED;
                    $paymentStatus = EnrollmentPaymentStatus::REJECTED;
                    $paidAmount = 0;
                    $canAttend = false;
                    $approvedAt = null;
                }
                
                // تاريخ التسجيل: قبل تاريخ بدء الكورس بـ 1-60 يوم
                $enrollmentDate = $courseStartDate->copy()->subDays(rand(1, 60));
                
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'payment_status' => $paymentStatus->value,
                    'paid_amount' => $paidAmount,
                    'total_amount' => $course->price,
                    'status' => $status->value,
                    'can_attend' => $canAttend,
                    'approved_by' => $status === EnrollmentStatus::APPROVED ? $admin?->id : null,
                    'approved_at' => $approvedAt,
                    'created_at' => $enrollmentDate,
                    'updated_at' => $enrollmentDate->copy()->addDays(rand(0, 10)),
                ]);
                
                $enrollmentCount++;
            }
        }

        $this->command->info("Enrollments seeded: {$enrollmentCount} enrollments");
    }
}
