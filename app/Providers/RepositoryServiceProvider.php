<?php

namespace App\Providers;

use App\Repository\LoginHistoryRepository;
use App\Repository\UserRepository;
use App\Services\LoginHistoryService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, UserService::class);
        $this->app->bind(LoginHistoryRepository::class, LoginHistoryService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
