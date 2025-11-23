<?php

namespace Modules\CMS\PublicSite\Http\Controllers;

use App\Support\Controllers\BaseController;
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

class PublicController extends BaseController
{
    public function __construct(private PublicSiteService $publicSiteService)
    {
    }

    public function courses(CourseListRequest $request)
    {
        $courses = $this->publicSiteService->listCourses($request->validated());
        $collection = CourseResource::collection($courses);
        
        return $this->success(
            $collection->resolve(request()),
            'Courses retrieved successfully'
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
        $categories = $this->publicSiteService->categories();
        $collection = CategoryResource::collection($categories);
        
        return $this->success(
            $collection->resolve(request()),
            'Categories retrieved successfully'
        );
    }

    public function instructors()
    {
        $instructors = $this->publicSiteService->instructors();
        $collection = UserResource::collection($instructors);
        
        return $this->success(
            $collection->resolve(request()),
            'Instructors retrieved successfully'
        );
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
        $sliders = $this->publicSiteService->sliders();
        $collection = SliderResource::collection($sliders);
        
        return $this->success(
            $collection->resolve(request()),
            'Sliders retrieved successfully'
        );
    }

    public function testimonials()
    {
        $testimonials = $this->publicSiteService->testimonials();
        $collection = TestimonialResource::collection($testimonials);
        
        return $this->success(
            $collection->resolve(request()),
            'Testimonials retrieved successfully'
        );
    }

    public function homeSummary()
    {
        $summary = $this->publicSiteService->homeSummary();

        return $this->success([
            'sliders' => SliderResource::collection($summary['sliders'])->resolve(request()),
            'courses' => CourseResource::collection($summary['courses'])->resolve(request()),
            'testimonials' => TestimonialResource::collection($summary['testimonials'])->resolve(request()),
            'stats' => $summary['stats'],
            'highlight_cards' => $summary['highlight_cards'],
            'learning_pillars' => $summary['learning_pillars'],
            'community_features' => $summary['community_features'],
            'upcoming_sessions' => $summary['upcoming_sessions'],
        ], 'Home summary retrieved successfully');
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

        return $this->success(
            $this->publicSiteService->settings($keys),
            'Settings retrieved successfully'
        );
    }

    public function contact(ContactMessageRequest $request)
    {
        $this->publicSiteService->storeContactMessage($request->validated());

        return $this->success(null, 'شكراً لتواصلك معنا');
    }
}

