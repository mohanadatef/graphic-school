<?php

namespace App\Services;

use App\Enums\SessionStatus;
use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\Contracts\SessionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private SessionRepositoryInterface $sessionRepository
    ) {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->courseRepository->paginateWithRelations($filters, $perPage);
    }

    public function create(array $data, ?UploadedFile $image = null): Course
    {
        if ($image) {
            $data['image_path'] = $image->store('courses', 'public');
        }

        unset($data['image']);

        $data['slug'] = Str::slug($data['title']);
        $data['code'] = $data['code'] ?? 'GS-' . Str::upper(Str::random(6));

        $course = $this->courseRepository->create($data);

        $this->courseRepository->syncInstructors(
            $course,
            $data['instructors'] ?? [],
            $data['supervisors'] ?? []
        );

        if ($course->auto_generate_sessions && $course->start_date && $course->session_count) {
            $this->generateSessionsForCourse(
                $course,
                $course->session_count,
                $course->days_of_week ?? [],
                $course->start_date
            );
        }

        return $course->load('instructors', 'sessions');
    }

    public function update(Course $course, array $data, ?UploadedFile $image = null, bool $regenerateSessions = false): Course
    {
        if ($image) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }

            $data['image_path'] = $image->store('courses', 'public');
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        unset($data['image'], $data['regenerate_sessions']);

        $course = $this->courseRepository->update($course, $data);

        $this->courseRepository->syncInstructors(
            $course,
            $data['instructors'] ?? [],
            $data['supervisors'] ?? []
        );

        if ($regenerateSessions) {
            $this->sessionRepository->deleteByCourse($course);
            $this->generateSessionsForCourse(
                $course,
                $course->session_count,
                $course->days_of_week ?? [],
                $course->start_date
            );
        }

        return $this->courseRepository->loadRelations($course, ['instructors', 'sessions']);
    }

    public function delete(Course $course): void
    {
        $this->courseRepository->delete($course);
    }

    public function assignInstructors(Course $course, array $data): Course
    {
        $this->courseRepository->syncInstructors(
            $course,
            $data['instructors'] ?? [],
            $data['supervisors'] ?? []
        );

        return $this->courseRepository->loadRelations($course, ['instructors']);
    }

    public function generateSessions(Course $course, array $data): Course
    {
        $this->sessionRepository->deleteByCourse($course);

        $this->generateSessionsForCourse(
            $course,
            (int) $data['session_count'],
            $data['days_of_week'],
            $data['start_date']
        );

        $this->courseRepository->update($course, [
            'session_count' => $data['session_count'],
            'days_of_week' => $data['days_of_week'],
            'start_date' => $data['start_date'],
        ]);

        return $this->courseRepository->loadRelations($course, ['sessions']);
    }

    protected function generateSessionsForCourse(Course $course, int $sessions, array $daysOfWeek, ?string $startDate): void
    {
        if (empty($daysOfWeek) || ! $startDate) {
            return;
        }

        $dayMap = [
            'mon' => Carbon::MONDAY,
            'tue' => Carbon::TUESDAY,
            'wed' => Carbon::WEDNESDAY,
            'thu' => Carbon::THURSDAY,
            'fri' => Carbon::FRIDAY,
            'sat' => Carbon::SATURDAY,
            'sun' => Carbon::SUNDAY,
        ];

        $current = Carbon::parse($startDate);
        $created = 0;
        $order = 1;

        $startTime = $course->default_start_time ?? '10:00';
        $endTime = $course->default_end_time ?? '12:00';

        while ($created < $sessions) {
            foreach ($daysOfWeek as $day) {
                if (! isset($dayMap[$day])) {
                    continue;
                }

                $currentDate = $current->copy()->next($dayMap[$day]);

                if ($currentDate->lessThan(Carbon::parse($startDate))) {
                    $currentDate = Carbon::parse($startDate)->next($dayMap[$day]);
                }

                if ($created >= $sessions) {
                    break;
                }

                $this->sessionRepository->create([
                    'course_id' => $course->id,
                    'title' => "{$course->title} - Session {$order}",
                    'session_order' => $order,
                    'session_date' => $currentDate->toDateString(),
                    'start_time' => $startTime ? "{$startTime}:00" : null,
                    'end_time' => $endTime ? "{$endTime}:00" : null,
                    'status' => SessionStatus::SCHEDULED->value,
                ]);

                $order++;
                $created++;
            }

            $current->addWeek();
        }
    }
}


