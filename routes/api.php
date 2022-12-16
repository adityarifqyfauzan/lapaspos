<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\ProductManagement\Category\CategoryController;
use App\Http\Controllers\ProductManagement\Category\CategoryStatusController;
use App\Http\Controllers\ProductManagement\ItemUnit\ItemUnitController;
use App\Http\Controllers\ProductManagement\ItemUnit\ItemUnitStatusController;
use App\Http\Controllers\ProductManagement\Product\ProductController;
use App\Http\Controllers\ProductManagement\Product\ProductStatusController;
use App\Http\Controllers\ProductManagement\Stock\StockInController;
use App\Http\Controllers\ProductManagement\Supplier\SupplierController;
use App\Http\Controllers\ProductManagement\Supplier\SupplierStatusController;
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
                Route::post('in', [StockInController::class, 'create']);
                Route::put('in/{id}', [StockInController::class, 'update']);
                Route::delete('in/{id}', [StockInController::class, 'destroy']);

            });

        });
    });

        Route::middleware(['cashier'])->group(function () {

    });

});
