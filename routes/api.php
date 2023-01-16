<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cashier\Category\CategoryListController;
use App\Http\Controllers\Cashier\Order\OrderController;
use App\Http\Controllers\Cashier\OrderStatus\OrderStatusController;
use App\Http\Controllers\Cashier\Payment\PaymentController;
use App\Http\Controllers\Cashier\Product\ProductListController;
use App\Http\Controllers\Cashier\Report\ReportingController;
use App\Http\Controllers\Dashboard\ChartController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\Outlet\OutletController;
use App\Http\Controllers\Outlet\OutletStatusController;
use App\Http\Controllers\Order\OrderController as OrderOrderController;
use App\Http\Controllers\ProductManagement\Category\CategoryController;
use App\Http\Controllers\ProductManagement\Category\CategoryStatusController;
use App\Http\Controllers\ProductManagement\ItemUnit\ItemUnitController;
use App\Http\Controllers\ProductManagement\ItemUnit\ItemUnitStatusController;
use App\Http\Controllers\ProductManagement\Product\ProductController;
use App\Http\Controllers\ProductManagement\Product\ProductStatusController;
use App\Http\Controllers\ProductManagement\Stock\StockInController;
use App\Http\Controllers\ProductManagement\Supplier\SupplierController;
use App\Http\Controllers\ProductManagement\Supplier\SupplierStatusController;
use App\Http\Controllers\UserManagement\Role\RoleController;
use App\Http\Controllers\UserManagement\Role\RoleStatusController;
use App\Http\Controllers\UserManagement\User\UserController;
use App\Http\Controllers\UserManagement\User\UserPasswordController;
use App\Http\Controllers\UserManagement\User\UserStatusController;
use App\Jobs\TestJob;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Events\Dispatchable;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/new-password', [UserPasswordController::class, 'newPassword']);

    Route::middleware(['admin'])->group(function () {
        Route::prefix('product-management')->group(function () {

            Route::resource('category', CategoryController::class);
            Route::put('category/status/{id}', CategoryStatusController::class);

            Route::resource('item-unit', ItemUnitController::class);
            Route::put('item-unit/status/{id}', ItemUnitStatusController::class);

            Route::resource('supplier', SupplierController::class);
            Route::put('supplier/status/{id}', SupplierStatusController::class);

            Route::resource('product', ProductController::class);
            Route::put('product/status/{id}', ProductStatusController::class);

            Route::prefix('stock')->group(function () {

                // stock in
                Route::get('in', [StockInController::class, 'index']);
                Route::get('in/{id}', [StockInController::class, 'show']);
                Route::post('in', [StockInController::class, 'create']);
                Route::put('in/{id}', [StockInController::class, 'update']);
                Route::delete('in/{id}', [StockInController::class, 'destroy']);

            });

        });

        Route::prefix('user-management')->group(function () {

            Route::resource('role', RoleController::class);
            Route::put('role/status/{id}', RoleStatusController::class);

            Route::resource('user', UserController::class);
            Route::put('user/reset-password/{id}', [UserPasswordController::class, 'resetPassword']);
            Route::put('user/status/{id}', UserStatusController::class);

        });

        Route::prefix('reporting')->group(function () {
            Route::get('summary', [ReportingController::class, 'summary']);
            Route::get('product-sale', [ReportingController::class, 'productSale']);
            Route::get('transaction-summary', [ChartController::class, 'transactionSummary']);
            Route::resource('order', OrderController::class);
        });

        Route::resource('outlet', OutletController::class);
        Route::put('outlet/status/{id}', OutletStatusController::class);
    });

    Route::middleware(['cashier'])->group(function () {

        Route::get('product-list', ProductListController::class);
        Route::get('category-list', CategoryListController::class);
        Route::get('order-status', [OrderStatusController::class, 'index']);
        Route::get('order', [OrderController::class, 'index']);
        Route::get('order/{id}', [OrderController::class, 'show']);
        Route::post('order', [OrderController::class, 'createOrder']);
        Route::put('order/cancel/{id}', [OrderController::class, 'cancelOrder']);
        Route::get('payment/{id}', [PaymentController::class, 'show']);
        Route::post('payment', [PaymentController::class, 'store']);

        Route::prefix('report')->group(function () {
            Route::get('summary', [ReportingController::class, 'summary']);
            Route::get('product-sale', [ReportingController::class, 'productSale']);
        });
    });

});
