<?php

namespace Modules\LMS\Curriculum\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        Route::middleware(['api'])
            ->prefix('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}

