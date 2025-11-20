<?php

namespace Modules\ACL\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ACL\Users\Http\Requests\CourseAttendanceRequest;
use Modules\ACL\Users\Http\Requests\CourseSessionsRequest;
use Modules\ACL\Users\Http\Requests\EnrollCourseRequest;
use Modules\ACL\Users\Http\Requests\ListMyCoursesRequest;
use Modules\ACL\Users\Http\Requests\ListSessionsRequest;
use Modules\ACL\Users\Http\Requests\ProfileRequest;
use Modules\ACL\Users\Http\Requests\ReviewCourseRequest;
use Modules\ACL\Users\Http\Requests\UpdateProfileRequest;
use Modules\LMS\Attendance\Http\Resources\AttendanceResource;
use Modules\LMS\CourseReviews\Http\Resources\CourseReviewResource;
use Modules\LMS\Enrollments\Http\Resources\EnrollmentResource;
use Modules\LMS\Sessions\Http\Resources\SessionResource;
use Modules\ACL\Users\Http\Resources\UserResource;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Services\StudentService;

class StudentController extends Controller
{
    public function __construct(private StudentService $studentService)
    {
    }

    public function myCourses(ListMyCoursesRequest $request)
    {
        return EnrollmentResource::collection(
            $this->studentService->myCourses($request->user())
        );
    }

    public function courseSessions(CourseSessionsRequest $request, Course $course)
    {
        $sessions = $this->studentService->courseSessions($request->user(), $course);

        return SessionResource::collection($sessions);
    }

    public function sessions(ListSessionsRequest $request)
    {
        $sessions = $this->studentService->sessions(
            $request->user(),
            $request->validated(),
            $request->integer('per_page', 10)
        );

        return SessionResource::collection($sessions);
    }

    public function courseAttendance(CourseAttendanceRequest $request, Course $course)
    {
        return AttendanceResource::collection(
            $this->studentService->courseAttendance($request->user(), $course)
        );
    }

    public function profile(ProfileRequest $request)
    {
        return UserResource::make($this->studentService->profile($request->user()));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->studentService->updateProfile(
            $request->user(),
            $request->validated(),
            $request->file('avatar')
        );

        return UserResource::make($user);
    }

    public function enroll(EnrollCourseRequest $request, Course $course)
    {
        $enrollment = $this->studentService->enroll($request->user(), $course);

        return EnrollmentResource::make($enrollment)
            ->response()
            ->setStatusCode(201);
    }

    public function reviewCourse(ReviewCourseRequest $request, Course $course)
    {
        $review = $this->studentService->reviewCourse(
            $request->user(),
            $course,
            $request->validated()
        );

        return CourseReviewResource::make($review);
    }
}

