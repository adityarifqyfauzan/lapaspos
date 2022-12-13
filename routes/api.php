<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\ProductManagement\Category\CategoryController;
use App\Http\Controllers\ProductManagement\ItemUnit\ItemUnitController;
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

            Route::prefix('category')->group(function () {
                Route::get('/', [CategoryController::class, 'index']);
                Route::get('/{id}', [CategoryController::class, 'show']);
                Route::post('/', [CategoryController::class, 'store']);
                Route::put('/{id}', [CategoryController::class, 'update']);
                Route::put('/status/{id}', [CategoryController::class, 'updateStatus']);
            });

            Route::prefix('item-unit')->group(function () {
                Route::get('/', [ItemUnitController::class, 'index']);
                Route::get('/{id}', [ItemUnitController::class, 'show']);
                Route::post('/', [ItemUnitController::class, 'store']);
                Route::put('/{id}', [ItemUnitController::class, 'update']);
                Route::put('/status/{id}', [ItemUnitController::class, 'updateStatus']);
            });

        });
    });

        Route::middleware(['cashier'])->group(function () {

    });

});
