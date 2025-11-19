<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\CourseAttendanceRequest;
use App\Http\Requests\Student\CourseSessionsRequest;
use App\Http\Requests\Student\EnrollCourseRequest;
use App\Http\Requests\Student\ListMyCoursesRequest;
use App\Http\Requests\Student\ListSessionsRequest;
use App\Http\Requests\Student\ProfileRequest;
use App\Http\Requests\Student\ReviewCourseRequest;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\CourseReviewResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\SessionResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\Session;
use App\Models\User;
use App\Services\StudentService;

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
