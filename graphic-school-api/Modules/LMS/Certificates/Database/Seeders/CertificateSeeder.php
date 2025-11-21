<?php

namespace Modules\LMS\Certificates\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Certificates\Models\Certificate;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        // Get completed enrollments
        $completedEnrollments = Enrollment::where('status', 'approved')
            ->where('progress_percentage', '>=', 100)
            ->where('certificate_issued', false)
            ->with(['course', 'student'])
            ->take(5)
            ->get();

        foreach ($completedEnrollments as $enrollment) {
            if (!$enrollment->course->has_certificate) {
                continue;
            }

            Certificate::create([
                'course_id' => $enrollment->course_id,
                'student_id' => $enrollment->student_id,
                'enrollment_id' => $enrollment->id,
                'issued_date' => now()->subDays(rand(1, 30)),
                'is_verified' => true,
            ]);

            $enrollment->update(['certificate_issued' => true]);
        }

        $this->command->info('Certificates seeded successfully!');
    }
}

