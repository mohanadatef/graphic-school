<?php

namespace Modules\CMS\PublicSite\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\CMS\PublicSite\Services\PublicSiteService;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\Enrollments\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Modules\LMS\Categories\Repositories\Interfaces\CategoryRepositoryInterface;
use Modules\ACL\Users\Repositories\Interfaces\UserRepositoryInterface;
use Modules\CMS\Sliders\Repositories\Interfaces\SliderRepositoryInterface;
use Modules\CMS\Testimonials\Repositories\Interfaces\TestimonialRepositoryInterface;
use Modules\LMS\Sessions\Repositories\Interfaces\SessionRepositoryInterface;
use Modules\CMS\Settings\Repositories\Interfaces\SettingRepositoryInterface;
use Modules\CMS\Contacts\Repositories\Interfaces\ContactMessageRepositoryInterface;
use Modules\LMS\CourseReviews\Repositories\Interfaces\CourseReviewRepositoryInterface;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PublicSiteService::class, function ($app) {
            return new PublicSiteService(
                $app->make(CourseRepositoryInterface::class),
                $app->make(EnrollmentRepositoryInterface::class),
                $app->make(CategoryRepositoryInterface::class),
                $app->make(UserRepositoryInterface::class),
                $app->make(SliderRepositoryInterface::class),
                $app->make(TestimonialRepositoryInterface::class),
                $app->make(SessionRepositoryInterface::class),
                $app->make(SettingRepositoryInterface::class),
                $app->make(ContactMessageRepositoryInterface::class),
                $app->make(CourseReviewRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
