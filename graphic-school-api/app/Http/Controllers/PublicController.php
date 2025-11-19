<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicSite\ContactMessageRequest;
use App\Http\Requests\PublicSite\CourseListRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Services\PublicSiteService;

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

        return CourseResource::make(
            $this->publicSiteService->courseDetails($course, $viewer)
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
