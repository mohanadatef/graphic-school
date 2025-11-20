<?php

namespace Modules\CMS\Contacts\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\CMS\Contacts\Repositories\Interfaces\ContactMessageRepositoryInterface;
use Modules\CMS\Contacts\Repositories\Eloquent\ContactMessageRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ContactMessageRepositoryInterface::class, ContactMessageRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
