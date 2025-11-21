<?php

namespace Database\Seeders;

use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Sessions\Enums\SessionStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        $dayMap = [
            'mon' => Carbon::MONDAY,
            'tue' => Carbon::TUESDAY,
            'wed' => Carbon::WEDNESDAY,
            'thu' => Carbon::THURSDAY,
            'fri' => Carbon::FRIDAY,
            'sat' => Carbon::SATURDAY,
            'sun' => Carbon::SUNDAY,
        ];

        $sessionCount = 0;

        Course::where('session_count', '>', 0)->get()->each(function (Course $course) use ($dayMap, &$sessionCount) {
            // حذف الجلسات القديمة إن وجدت
            $course->sessions()->delete();

            $days = $course->days_of_week ?? ['sat'];
            $sessionsNeeded = $course->session_count ?? 8;
            $startDate = $course->start_date ? Carbon::parse($course->start_date) : Carbon::now();
            $startTime = $course->default_start_time ?? '10:00';
            $endTime = $course->default_end_time ?? '12:00';
            
            $order = 1;
            $current = $startDate->copy();

            while ($order <= $sessionsNeeded) {
                foreach ($days as $day) {
                    if (!isset($dayMap[$day])) {
                        continue;
                    }

                    if ($order > $sessionsNeeded) {
                        break;
                    }

                    // العثور على أول يوم من الأيام المحددة
                    $sessionDate = $current->copy();
                    while ($sessionDate->dayOfWeek !== $dayMap[$day]) {
                        $sessionDate->addDay();
                    }

                    // تحديد حالة الجلسة بناءً على التاريخ
                    $now = Carbon::now();
                    if ($sessionDate->isPast()) {
                        $status = SessionStatus::COMPLETED;
                    } elseif ($sessionDate->isToday()) {
                        $status = SessionStatus::SCHEDULED;
                    } else {
                        $status = SessionStatus::SCHEDULED;
                    }

                    Session::create([
                        'course_id' => $course->id,
                        'title' => "{$course->title} - Session {$order}",
                        'session_order' => $order,
                        'session_date' => $sessionDate->toDateString(),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'status' => $status->value,
                        'created_at' => $startDate->copy()->subDays(rand(1, 30)),
                        'updated_at' => $sessionDate->copy()->subDays(rand(0, 5)),
                    ]);

                    $order++;
                    $sessionCount++;
                }

                $current->addWeek();
            }
        });

        $this->command->info("Sessions seeded: {$sessionCount} sessions");
    }
}
