<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['category', 'instructors', 'sessions']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->get('search') . '%');
        }

        return response()->json(
            $query->orderByDesc('created_at')->paginate($request->integer('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('courses', 'public');
        }

        $data['slug'] = Str::slug($data['title']);
        $data['code'] = $data['code'] ?? 'GS-' . Str::upper(Str::random(6));

        $course = Course::create($data);

        $this->syncInstructors($course, $request);

        if ($course->auto_generate_sessions && $course->start_date && $course->session_count) {
            $this->generateSessionsForCourse($course, $course->session_count, $course->days_of_week ?? [], $course->start_date);
        }

        return response()->json($course->load('instructors', 'sessions'), 201);
    }

    public function show(Course $course)
    {
        return response()->json(
            $course->load('category', 'instructors', 'sessions', 'enrollments')
        );
    }

    public function update(Request $request, Course $course)
    {
        $data = $this->validatedData($request, $course->id, false);

        if ($request->hasFile('image')) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
            $data['image_path'] = $request->file('image')->store('courses', 'public');
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course->update($data);

        $this->syncInstructors($course, $request);

        if ($request->boolean('regenerate_sessions')) {
            $course->sessions()->delete();
            $this->generateSessionsForCourse(
                $course,
                $course->session_count,
                $course->days_of_week ?? [],
                $course->start_date
            );
        }

        return response()->json($course->load('instructors', 'sessions'));
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function assignInstructors(Request $request, Course $course)
    {
        $this->syncInstructors($course, $request);

        return response()->json($course->load('instructors'));
    }

    public function generateSessions(Request $request, Course $course)
    {
        $data = $request->validate([
            'session_count' => ['required', 'integer', 'min:1'],
            'days_of_week' => ['required', 'array'],
            'days_of_week.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'start_date' => ['required', 'date'],
        ]);

        $course->sessions()->delete();

        $this->generateSessionsForCourse(
            $course,
            $data['session_count'],
            $data['days_of_week'],
            $data['start_date']
        );

        $course->update([
            'session_count' => $data['session_count'],
            'days_of_week' => $data['days_of_week'],
            'start_date' => $data['start_date'],
        ]);

        return response()->json($course->load('sessions'));
    }

    protected function validatedData(Request $request, ?int $courseId = null, bool $isCreate = true): array
    {
        $rules = [
            'title' => [$isCreate ? 'required' : 'sometimes', 'string', 'max:255'],
            'category_id' => [$isCreate ? 'required' : 'sometimes', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'session_count' => ['nullable', 'integer', 'min:1'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['in:mon,tue,wed,thu,fri,sat,sun'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'auto_generate_sessions' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'is_hidden' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:draft,upcoming,running,completed,archived'],
            'code' => ['nullable', 'string', 'max:20', 'unique:courses,code,' . $courseId],
            'image' => ['nullable', 'file', 'image', 'max:2048'],
            'default_start_time' => ['nullable', 'date_format:H:i'],
            'default_end_time' => ['nullable', 'date_format:H:i', 'after:default_start_time'],
        ];

        $validated = $request->validate($rules);

        if (($validated['auto_generate_sessions'] ?? $isCreate) && $isCreate) {
            $requiredKeys = ['session_count', 'days_of_week', 'start_date'];
            foreach ($requiredKeys as $key) {
                if (empty($validated[$key])) {
                    abort(422, __('حقل :attribute مطلوب لتوليد الجلسات تلقائياً', ['attribute' => $key]));
                }
            }
        }

        return $validated;
    }

    protected function syncInstructors(Course $course, Request $request): void
    {
        $data = $request->validate([
            'instructors' => ['nullable', 'array'],
            'instructors.*' => ['exists:users,id'],
            'supervisors' => ['nullable', 'array'],
            'supervisors.*' => ['exists:users,id'],
        ]);

        $instructorIds = $data['instructors'] ?? [];
        $supervisorIds = $data['supervisors'] ?? [];

        $syncData = [];

        foreach ($instructorIds as $id) {
            $syncData[$id] = ['is_supervisor' => in_array($id, $supervisorIds, true)];
        }

        $course->instructors()->sync($syncData);
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

                Session::create([
                    'course_id' => $course->id,
                    'title' => "{$course->title} - Session {$order}",
                    'session_order' => $order,
                    'session_date' => $currentDate->toDateString(),
                    'start_time' => $startTime ? "{$startTime}:00" : null,
                    'end_time' => $endTime ? "{$endTime}:00" : null,
                    'status' => 'scheduled',
                ]);

                $order++;
                $created++;
            }

            $current->addWeek();
        }
    }
}
