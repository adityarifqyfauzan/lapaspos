<?php

namespace App\Providers;

use App\Http\Context\Auth\AuthContext;
use App\Http\Context\Auth\AuthContextInterface;
use App\Http\Context\Category\CategoryContext;
use App\Http\Context\Category\CategoryContextInterface;
use App\Http\Context\ItemUnit\ItemUnitContext;
use App\Http\Context\ItemUnit\ItemUnitContextInterface;
use App\Http\Context\Order\OrderContext;
use App\Http\Context\Order\OrderContextInterface;
use App\Http\Context\OrderStatus\OrderStatusContext;
use App\Http\Context\OrderStatus\OrderStatusContextInterface;
use App\Http\Context\Outlet\OutletContext;
use App\Http\Context\Outlet\OutletContextInterface;
use App\Http\Context\Payment\PaymentContext;
use App\Http\Context\Payment\PaymentContextInterface;
use App\Http\Context\Product\ProductContext;
use App\Http\Context\Product\ProductContextInterface;
use App\Http\Context\Reporting\ReportingContext;
use App\Http\Context\Reporting\ReportingContextInterface;
use App\Http\Context\Role\RoleContext;
use App\Http\Context\Role\RoleContextInterface;
use App\Http\Context\Stock\StockContext;
use App\Http\Context\Stock\StockContextInterface;
use App\Http\Context\Supplier\SupplierContext;
use App\Http\Context\Supplier\SupplierContextInterface;
use App\Http\Context\User\UserContext;
use App\Http\Context\User\UserContextInterface;
use Illuminate\Support\ServiceProvider;

class ContextProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthContextInterface::class, AuthContext::class);
        $this->app->bind(CategoryContextInterface::class, CategoryContext::class);
        $this->app->bind(ItemUnitContextInterface::class, ItemUnitContext::class);
        $this->app->bind(SupplierContextInterface::class, SupplierContext::class);
        $this->app->bind(ProductContextInterface::class, ProductContext::class);
        $this->app->bind(StockContextInterface::class, StockContext::class);
        $this->app->bind(RoleContextInterface::class, RoleContext::class);
        $this->app->bind(UserContextInterface::class, UserContext::class);
        $this->app->bind(OrderContextInterface::class, OrderContext::class);
        $this->app->bind(PaymentContextInterface::class, PaymentContext::class);
        $this->app->bind(ReportingContextInterface::class, ReportingContext::class);
        $this->app->bind(OrderStatusContextInterface::class, OrderStatusContext::class);
        $this->app->bind(OutletContextInterface::class, OutletContext::class);
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
