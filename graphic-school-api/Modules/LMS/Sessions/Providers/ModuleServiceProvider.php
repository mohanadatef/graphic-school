<?php

namespace Modules\LMS\Sessions\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\LMS\Sessions\Repositories\Interfaces\SessionRepositoryInterface;
use Modules\LMS\Sessions\Repositories\Eloquent\SessionRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SessionRepositoryInterface::class, SessionRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
