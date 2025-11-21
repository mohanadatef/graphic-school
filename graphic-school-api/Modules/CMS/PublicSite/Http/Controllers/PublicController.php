<?php

namespace Modules\CMS\PublicSite\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\CMS\PublicSite\Http\Requests\ContactMessageRequest;
use Modules\CMS\PublicSite\Http\Requests\CourseListRequest;
use Modules\LMS\Categories\Http\Resources\CategoryResource;
use Modules\LMS\Courses\Http\Resources\CourseResource;
use Modules\CMS\Sliders\Http\Resources\SliderResource;
use Modules\CMS\Testimonials\Http\Resources\TestimonialResource;
use Modules\ACL\Users\Http\Resources\UserResource;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Modules\CMS\PublicSite\Services\PublicSiteService;

class PublicController extends Controller
{
    public function __construct(private PublicSiteService $publicSiteService)
    {
    }

    public function courses(CourseListRequest $request)
    {
        return CourseResource::collection(
            $this->publicSiteService->listCourses($request->validated())
        );
    }

    public function courseShow(Course $course)
    {
        $viewer = auth('api')->user();

        $courseDetails = $this->publicSiteService->courseDetails($course, $viewer);

        $resource = new CourseResource($courseDetails);
        
        return ApiResponse::success(
            $resource->resolve(request()),
            'Course details retrieved successfully'
        );
    }

    public function categories()
    {
        return CategoryResource::collection($this->publicSiteService->categories());
    }

    public function instructors()
    {
        return UserResource::collection($this->publicSiteService->instructors());
    }

    public function instructorShow(User $instructor)
    {
        $instructorDetails = $this->publicSiteService->instructorDetails($instructor);
        $resource = new UserResource($instructorDetails);
        
        return ApiResponse::success(
            $resource->resolve(request()),
            'Instructor details retrieved successfully'
        );
    }

    public function sliders()
    {
        return SliderResource::collection($this->publicSiteService->sliders());
    }

    public function testimonials()
    {
        return TestimonialResource::collection(
            $this->publicSiteService->testimonials()
        );
    }

    public function homeSummary()
    {
        $summary = $this->publicSiteService->homeSummary();

        return response()->json([
            'sliders' => SliderResource::collection($summary['sliders']),
            'courses' => CourseResource::collection($summary['courses']),
            'testimonials' => TestimonialResource::collection($summary['testimonials']),
            'stats' => $summary['stats'],
            'highlight_cards' => $summary['highlight_cards'],
            'learning_pillars' => $summary['learning_pillars'],
            'community_features' => $summary['community_features'],
            'upcoming_sessions' => $summary['upcoming_sessions'],
        ]);
    }

    public function settings()
    {
        $keys = [
            'site_name',
            'primary_color',
            'secondary_color',
            'email',
            'phone',
            'address',
            'about_us',
            'logo',
        ];

        return response()->json(
            $this->publicSiteService->settings($keys)
        );
    }

    public function contact(ContactMessageRequest $request)
    {
        $this->publicSiteService->storeContactMessage($request->validated());

        return response()->json(['message' => 'شكراً لتواصلك معنا']);
    }
}

