<?php

namespace Modules\CMS\Sliders\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\CMS\Sliders\Repositories\Interfaces\SliderRepositoryInterface;
use Modules\CMS\Sliders\Repositories\Eloquent\SliderRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SliderRepositoryInterface::class, SliderRepository::class);
    }

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__ . '/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}