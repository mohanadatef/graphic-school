<?php

namespace App\Http\Controllers;

use App\Http\Requests\Instructor\CourseSessionsRequest;
use App\Http\Requests\Instructor\ListCoursesRequest;
use App\Http\Requests\Instructor\ListSessionsRequest;
use App\Http\Requests\Instructor\SessionAttendanceRequest;
use App\Http\Requests\Instructor\StoreAttendanceRequest;
use App\Http\Requests\Instructor\UpdateSessionNoteRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\SessionResource;
use App\Models\Course;
use App\Models\Session;
use App\Services\InstructorService;

class InstructorController extends Controller
{
    public function __construct(private InstructorService $instructorService)
    {
    }

    public function myCourses(ListCoursesRequest $request)
    {
        $courses = $this->instructorService->myCourses($request->user());

        return CourseResource::collection($courses);
    }

    public function courseSessions(CourseSessionsRequest $request, Course $course)
    {
        $sessions = $this->instructorService->courseSessions($request->user(), $course);

        return SessionResource::collection($sessions);
    }

    public function sessions(ListSessionsRequest $request)
    {
        $sessions = $this->instructorService->sessions(
            $request->user(),
            $request->validated(),
            $request->integer('per_page', 10)
        );

        return SessionResource::collection($sessions);
    }

    public function storeAttendance(StoreAttendanceRequest $request)
    {
        $this->instructorService->storeAttendance($request->user(), $request->validated());

        return response()->json(['message' => 'Attendance saved']);
    }

    public function sessionAttendance(SessionAttendanceRequest $request, Session $session)
    {
        $students = $this->instructorService->sessionAttendance($request->user(), $session);

        return response()->json($students);
    }

    public function sessionNote(UpdateSessionNoteRequest $request, Session $session)
    {
        $session = $this->instructorService->updateSessionNote(
            $request->user(),
            $session,
            $request->validated()['note']
        );

        return SessionResource::make($session);
    }
}
