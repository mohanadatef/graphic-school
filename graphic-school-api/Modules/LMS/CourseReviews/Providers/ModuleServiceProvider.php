<?php

namespace Modules\LMS\CourseReviews\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\LMS\CourseReviews\Repositories\Interfaces\CourseReviewRepositoryInterface;
use Modules\LMS\CourseReviews\Repositories\Eloquent\CourseReviewRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CourseReviewRepositoryInterface::class, CourseReviewRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}

