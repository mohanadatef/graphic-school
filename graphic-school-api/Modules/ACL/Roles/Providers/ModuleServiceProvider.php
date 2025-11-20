<?php

namespace Modules\ACL\Roles\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\ACL\Roles\Repositories\Interfaces\RoleRepositoryInterface;
use Modules\ACL\Roles\Repositories\Eloquent\RoleRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}

