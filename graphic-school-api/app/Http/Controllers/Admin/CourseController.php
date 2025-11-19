<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Course\AssignCourseInstructorsRequest;
use App\Http\Requests\Admin\Course\GenerateSessionsRequest;
use App\Http\Requests\Admin\Course\ListCourseRequest;
use App\Http\Requests\Admin\Course\StoreCourseRequest;
use App\Http\Requests\Admin\Course\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;

class CourseController extends Controller
{
    public function __construct(private CourseService $courseService)
    {
    }

    public function index(ListCourseRequest $request)
    {
        $courses = $this->courseService->paginate(
            $request->validated(),
            $request->integer('per_page', 10)
        );

        return CourseResource::collection($courses);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = $this->courseService->create(
            $request->validated(),
            $request->file('image')
        );

        return CourseResource::make($course->load('instructors', 'sessions'))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Course $course)
    {
        return CourseResource::make(
            $course->load('category', 'instructors', 'sessions', 'enrollments')
        );
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course = $this->courseService->update(
            $course,
            $request->validated(),
            $request->file('image'),
            $request->boolean('regenerate_sessions')
        );

        return CourseResource::make($course->load('instructors', 'sessions'));
    }

    public function destroy(Course $course)
    {
        $this->courseService->delete($course);

        return response()->json(['message' => 'Deleted']);
    }

    public function assignInstructors(AssignCourseInstructorsRequest $request, Course $course)
    {
        $course = $this->courseService->assignInstructors($course, $request->validated());

        return CourseResource::make($course->load('instructors'));
    }

    public function generateSessions(GenerateSessionsRequest $request, Course $course)
    {
        $course = $this->courseService->generateSessions($course, $request->validated());

        return CourseResource::make($course->load('sessions'));
    }
}
