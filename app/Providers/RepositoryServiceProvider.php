<?php

namespace App\Providers;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\ItemUnitRepository;
use App\Repository\LoginHistoryRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusHistoryRepository;
use App\Repository\OrderStatusRepository;
use App\Repository\OutletRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\PaymentRepository;
use App\Repository\PaymentStatusHistoryRepository;
use App\Repository\PaymentStatusRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductPriceRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductStockRepository;
use App\Repository\ReportingRepository;
use App\Repository\SupplierRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\ActivityService;
use App\Services\CategoryService;
use App\Services\ItemUnitService;
use App\Services\LoginHistoryService;
use App\Services\OrderDetailService;
use App\Services\OrderService;
use App\Services\OrderStatusHistoryService;
use App\Services\OrderStatusService;
use App\Services\OutletService;
use App\Services\PaymentMethodService;
use App\Services\PaymentService;
use App\Services\PaymentStatusHistoryService;
use App\Services\PaymentStatusService;
use App\Services\ProductCategoryService;
use App\Services\ProductPriceService;
use App\Services\ProductService;
use App\Services\ProductStockService;
use App\Services\ReportingService;
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
        $this->app->bind(ProductStockRepository::class, ProductStockService::class);
        $this->app->bind(OrderRepository::class, OrderService::class);
        $this->app->bind(OrderDetailRepository::class, OrderDetailService::class);
        $this->app->bind(OrderStatusRepository::class, OrderStatusService::class);
        $this->app->bind(OrderStatusHistoryRepository::class, OrderStatusHistoryService::class);
        $this->app->bind(PaymentRepository::class, PaymentService::class);
        $this->app->bind(PaymentMethodRepository::class, PaymentMethodService::class);
        $this->app->bind(PaymentStatusRepository::class, PaymentStatusService::class);
        $this->app->bind(PaymentStatusHistoryRepository::class, PaymentStatusHistoryService::class);
        $this->app->bind(ReportingRepository::class, ReportingService::class);
        $this->app->bind(OutletRepository::class, OutletService::class);
        $this->app->bind(ActivityRepository::class, ActivityService::class);
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
