<?php

namespace Modules\LMS\Attendance\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\LMS\Attendance\Repositories\Interfaces\AttendanceRepositoryInterface;
use Modules\LMS\Attendance\Repositories\Eloquent\AttendanceRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
