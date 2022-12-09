<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->to('/telescope');
    }
    return view('welcome');
});

Route::get('/ping', function () {
    return json_encode([
        "message" => "pong"
    ], 200);
});

Route::post('/login', function (Request $request){

    $validate = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if ($validate->fails()) {
        return redirect()->to('/');
    }

    if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
        return redirect()->to('/telescope');
    }

    return redirect()->to('/');

})->name('login');

Route::get('/logout', function(){
    if (!Auth::check()) {
        return redirect()->to('/');
    }
    Auth::logout();
    return redirect()->to('/');
})->name('logout');
