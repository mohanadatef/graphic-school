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

        Course::where('session_count', '>', 0)->get()->each(function (Course $course) use ($dayMap) {
            $course->sessions()->delete();

            $days = $course->days_of_week ?? ['sat'];
            $sessionsNeeded = $course->session_count ?? 8;
            $current = $course->start_date ? Carbon::parse($course->start_date) : Carbon::now();
            $order = 1;

            while ($order <= $sessionsNeeded) {
                foreach ($days as $day) {
                    if (! isset($dayMap[$day])) {
                        continue;
                    }

                    if ($order > $sessionsNeeded) {
                        break;
                    }

                    $date = $current->copy()->next($dayMap[$day]);
                    if ($date->lessThan($current)) {
                        $date = $current->copy()->next($dayMap[$day]);
                    }

                    Session::create([
                        'course_id' => $course->id,
                        'title' => "{$course->title} - Session {$order}",
                        'session_order' => $order,
                        'session_date' => $date->toDateString(),
                        'start_time' => '18:00:00',
                        'end_time' => '20:00:00',
                        'status' => SessionStatus::SCHEDULED->value,
                    ]);

                    $order++;
                }

                $current->addWeek();
            }
        });
    }
}
