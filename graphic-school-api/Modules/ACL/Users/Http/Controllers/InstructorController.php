<?php

namespace Modules\ACL\Users\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Http\Responses\ApiResponse;
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

class InstructorController extends BaseController
{
    public function __construct(private InstructorService $instructorService)
    {
    }

    public function myCourses(ListCoursesRequest $request)
    {
        $courses = $this->instructorService->myCourses($request->user());
        $collection = CourseResource::collection($courses);

        return $this->success(
            $collection->resolve(request()),
            'My courses retrieved successfully'
        );
    }

    public function courseSessions(CourseSessionsRequest $request, Course $course)
    {
        $sessions = $this->instructorService->courseSessions($request->user(), $course);
        $collection = SessionResource::collection($sessions);

        return $this->success(
            $collection->resolve(request()),
            'Course sessions retrieved successfully'
        );
    }

    public function sessions(ListSessionsRequest $request)
    {
        $sessions = $this->instructorService->sessions(
            $request->user(),
            $request->validated(),
            $request->integer('per_page', 10)
        );

        // If paginated, use paginated response
        if (method_exists($sessions, 'currentPage')) {
            return $this->paginated($sessions, 'Sessions retrieved successfully');
        }

        $collection = SessionResource::collection($sessions);
        return $this->success(
            $collection->resolve(request()),
            'Sessions retrieved successfully'
        );
    }

    public function storeAttendance(StoreAttendanceRequest $request)
    {
        $this->instructorService->storeAttendance($request->user(), $request->validated());

        return $this->success(null, 'Attendance saved successfully');
    }

    public function sessionAttendance(SessionAttendanceRequest $request, Session $session)
    {
        $students = $this->instructorService->sessionAttendance($request->user(), $session);

        return $this->success($students, 'Session attendance retrieved successfully');
    }

    public function sessionNote(UpdateSessionNoteRequest $request, Session $session)
    {
        $session = $this->instructorService->updateSessionNote(
            $request->user(),
            $session,
            $request->validated()['note']
        );

        $resource = SessionResource::make($session);
        
        return $this->success(
            $resource->resolve(request()),
            'Session note updated successfully'
        );
    }
}

