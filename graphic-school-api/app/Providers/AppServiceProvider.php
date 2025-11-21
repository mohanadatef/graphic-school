<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\PasswordHasherInterface;
use App\Contracts\Services\FileStorageInterface;
use App\Contracts\Services\TransactionManagerInterface;
use App\Services\PasswordHasherService;
use App\Services\FileStorageService;
use App\Support\Database\TransactionManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations (Dependency Inversion Principle)
        $this->app->singleton(PasswordHasherInterface::class, PasswordHasherService::class);
        $this->app->singleton(FileStorageInterface::class, FileStorageService::class);
        $this->app->singleton(TransactionManagerInterface::class, TransactionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
