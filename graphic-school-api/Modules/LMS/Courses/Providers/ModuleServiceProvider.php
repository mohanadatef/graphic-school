<?php

namespace Modules\LMS\Courses\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\Courses\Repositories\Eloquent\CourseRepository;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Courses\Infrastructure\Observers\CourseObserver;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        // Register observer
        Course::observe(CourseObserver::class);
    }
}
