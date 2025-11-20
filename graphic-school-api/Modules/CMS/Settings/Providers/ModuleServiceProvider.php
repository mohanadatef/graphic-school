<?php

namespace Modules\CMS\Settings\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\CMS\Settings\Repositories\Interfaces\SettingRepositoryInterface;
use Modules\CMS\Settings\Repositories\Eloquent\SettingRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
