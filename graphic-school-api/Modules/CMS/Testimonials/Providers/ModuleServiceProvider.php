<?php

namespace Modules\CMS\Testimonials\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\CMS\Testimonials\Repositories\Interfaces\TestimonialRepositoryInterface;
use Modules\CMS\Testimonials\Repositories\Eloquent\TestimonialRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TestimonialRepositoryInterface::class, TestimonialRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
