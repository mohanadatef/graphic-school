<?php

namespace Modules\ACL\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ACL\Users\Http\Requests\CourseSessionsRequest;
use Modules\ACL\Users\Http\Requests\ListCoursesRequest;
use Modules\ACL\Users\Http\Requests\ListSessionsRequest;
use Modules\ACL\Users\Http\Requests\SessionAttendanceRequest;
use Modules\ACL\Users\Http\Requests\StoreAttendanceRequest;
use Modules\ACL\Users\Http\Requests\UpdateSessionNoteRequest;
use Modules\LMS\Courses\Http\Resources\CourseResource;
use Modules\LMS\Sessions\Http\Resources\SessionResource;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Services\InstructorService;

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

