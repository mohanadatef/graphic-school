<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\SessionRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind shared interfaces to implementations
        // These bindings allow modules to use interfaces without direct dependencies
        
        // User Repository Interface
        $this->app->bind(
            UserRepositoryInterface::class,
            \Modules\ACL\Users\Infrastructure\Repositories\Eloquent\UserRepository::class
        );

        // Role Repository Interface
        $this->app->bind(
            RoleRepositoryInterface::class,
            \Modules\ACL\Roles\Repositories\Eloquent\RoleRepository::class
        );

        // Course Repository Interface
        $this->app->bind(
            CourseRepositoryInterface::class,
            \Modules\LMS\Courses\Repositories\Eloquent\CourseRepository::class
        );

        // Category Repository Interface
        $this->app->bind(
            CategoryRepositoryInterface::class,
            \Modules\LMS\Categories\Repositories\Eloquent\CategoryRepository::class
        );

        // Session Repository Interface
        $this->app->bind(
            SessionRepositoryInterface::class,
            \Modules\LMS\Sessions\Repositories\Eloquent\SessionRepository::class
        );

        // Group Repository Interface
        $this->app->bind(
            \App\Repositories\Interfaces\GroupRepositoryInterface::class,
            \App\Repositories\Eloquent\GroupRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
