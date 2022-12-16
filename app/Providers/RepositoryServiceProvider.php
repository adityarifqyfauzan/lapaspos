<?php

namespace App\Providers;

use App\Repository\CategoryRepository;
use App\Repository\ItemUnitRepository;
use App\Repository\LoginHistoryRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductPriceRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\CategoryService;
use App\Services\ItemUnitService;
use App\Services\LoginHistoryService;
use App\Services\ProductCategoryService;
use App\Services\ProductPriceService;
use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\RoleService;
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
        $this->app->bind(ProductRepository::class, ProductService::class);
        $this->app->bind(ProductPriceRepository::class, ProductPriceService::class);
        $this->app->bind(ProductCategoryRepository::class, ProductCategoryService::class);
        $this->app->bind(RoleRepository::class, RoleService::class);
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
