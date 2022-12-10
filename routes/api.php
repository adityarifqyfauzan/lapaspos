<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HelloWorldController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/hello-world', HelloWorldController::class);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('admin')->group(function () {
        Route::get('/admin/test', function() {
            return response()->json([
                "role" => "admin"
            ]);
        });
    });

    Route::middleware('cashier')->group(function () {
        Route::get('/cashier/test', function() {
            return response()->json([
                "role" => "cashier"
            ]);
        });
    });

});
