<?php

namespace Database\Seeders;

use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentPaymentStatus;
use Modules\LMS\Courses\Models\Course;
use Modules\ACL\Users\Models\User;
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

        foreach ($students as $student) {
            // Enroll each student in 2-3 random courses
            $studentCourses = $courses->random(rand(2, min(3, $courses->count())));
            
            foreach ($studentCourses as $course) {
                $status = EnrollmentStatus::APPROVED;
                $paymentStatus = EnrollmentPaymentStatus::PAID;
                $paidAmount = $course->price;
                
                // Some enrollments might be pending or partially paid
                if (rand(1, 10) <= 2) {
                    $status = EnrollmentStatus::PENDING;
                    $paymentStatus = EnrollmentPaymentStatus::NOT_PAID;
                    $paidAmount = 0;
                } elseif (rand(1, 10) <= 3) {
                    $paymentStatus = EnrollmentPaymentStatus::PARTIALLY_PAID;
                    $paidAmount = $course->price * 0.5;
                }

                Enrollment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'payment_status' => $paymentStatus->value,
                        'paid_amount' => $paidAmount,
                        'total_amount' => $course->price,
                        'status' => $status->value,
                        'can_attend' => $status === EnrollmentStatus::APPROVED,
                        'approved_by' => $admin?->id,
                        'approved_at' => $status === EnrollmentStatus::APPROVED ? now()->subDays(rand(1, 30)) : null,
                    ]
                );
            }
        }

        $this->command->info('Enrollments seeded successfully!');
    }
}

