<?php

namespace Modules\ACL\Users\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\ACL\Users\Infrastructure\Repositories\Interfaces\UserRepositoryInterface as InfrastructureUserRepositoryInterface;
use Modules\ACL\Users\Infrastructure\Repositories\Eloquent\UserRepository as InfrastructureUserRepository;
use Modules\ACL\Users\Repositories\Interfaces\UserRepositoryInterface;
use Modules\ACL\Users\Repositories\Eloquent\UserRepository;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Users\Infrastructure\Observers\UserObserver;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Infrastructure interface (used by UseCases)
        $this->app->bind(InfrastructureUserRepositoryInterface::class, InfrastructureUserRepository::class);
        
        // Bind Module interface (used by Services and other modules)
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        // Register observer
        User::observe(UserObserver::class);
    }
}
