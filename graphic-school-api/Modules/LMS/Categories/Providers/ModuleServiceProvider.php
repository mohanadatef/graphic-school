<?php

namespace Modules\LMS\Categories\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\LMS\Categories\Repositories\Interfaces\CategoryRepositoryInterface;
use Modules\LMS\Categories\Repositories\Eloquent\CategoryRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}

