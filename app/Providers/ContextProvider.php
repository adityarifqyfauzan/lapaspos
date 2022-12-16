<?php

namespace App\Providers;

use App\Http\Context\Auth\AuthContext;
use App\Http\Context\Auth\AuthContextInterface;
use App\Http\Context\Category\CategoryContext;
use App\Http\Context\Category\CategoryContextInterface;
use App\Http\Context\ItemUnit\ItemUnitContext;
use App\Http\Context\ItemUnit\ItemUnitContextInterface;
use App\Http\Context\Product\ProductContext;
use App\Http\Context\Product\ProductContextInterface;
use App\Http\Context\Stock\StockContext;
use App\Http\Context\Stock\StockContextInterface;
use App\Http\Context\Supplier\SupplierContext;
use App\Http\Context\Supplier\SupplierContextInterface;
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
