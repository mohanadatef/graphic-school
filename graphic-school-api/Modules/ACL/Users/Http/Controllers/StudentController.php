<?php

namespace Modules\ACL\Users\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Http\Responses\ApiResponse;
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

class StudentController extends BaseController
{
    public function __construct(private StudentService $studentService)
    {
    }

    public function myCourses(ListMyCoursesRequest $request)
    {
        $enrollments = $this->studentService->myCourses($request->user());
        $collection = EnrollmentResource::collection($enrollments);
        
        return $this->success(
            $collection->resolve(request()),
            'My courses retrieved successfully'
        );
    }

    public function courseSessions(CourseSessionsRequest $request, Course $course)
    {
        $sessions = $this->studentService->courseSessions($request->user(), $course);
        $collection = SessionResource::collection($sessions);

        return $this->success(
            $collection->resolve(request()),
            'Course sessions retrieved successfully'
        );
    }

    public function sessions(ListSessionsRequest $request)
    {
        $sessions = $this->studentService->sessions(
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

    public function courseAttendance(CourseAttendanceRequest $request, Course $course)
    {
        $attendance = $this->studentService->courseAttendance($request->user(), $course);
        $collection = AttendanceResource::collection($attendance);
        
        return $this->success(
            $collection->resolve(request()),
            'Course attendance retrieved successfully'
        );
    }

    public function profile(ProfileRequest $request)
    {
        $user = $this->studentService->profile($request->user());
        $resource = UserResource::make($user);
        
        return $this->success(
            $resource->resolve(request()),
            'Profile retrieved successfully'
        );
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->studentService->updateProfile(
            $request->user(),
            $request->validated(),
            $request->file('avatar')
        );

        $resource = UserResource::make($user);
        
        return $this->success(
            $resource->resolve(request()),
            'Profile updated successfully'
        );
    }

    public function enroll(EnrollCourseRequest $request, Course $course)
    {
        $enrollment = $this->studentService->enroll($request->user(), $course);
        $resource = EnrollmentResource::make($enrollment);

        return $this->created(
            $resource->resolve(request()),
            'Enrolled successfully'
        );
    }

    public function reviewCourse(ReviewCourseRequest $request, Course $course)
    {
        $review = $this->studentService->reviewCourse(
            $request->user(),
            $course,
            $request->validated()
        );

        $resource = CourseReviewResource::make($review);
        
        return $this->created(
            $resource->resolve(request()),
            'Review submitted successfully'
        );
    }
}

