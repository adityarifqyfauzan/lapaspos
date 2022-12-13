<?php

namespace App\Providers;

use App\Repository\CategoryRepository;
use App\Repository\ItemUnitRepository;
use App\Repository\LoginHistoryRepository;
use App\Repository\SupplierRepository;
use App\Repository\UserRepository;
use App\Services\CategoryService;
use App\Services\ItemUnitService;
use App\Services\LoginHistoryService;
use App\Services\SupplierService;
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
        $this->app->bind(CategoryRepository::class, CategoryService::class);
        $this->app->bind(ItemUnitRepository::class, ItemUnitService::class);
        $this->app->bind(SupplierRepository::class, SupplierService::class);
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
