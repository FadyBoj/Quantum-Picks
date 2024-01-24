<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware(['guest'])->group(function() {
    Route::controller(UserController::class)->group(function(){

        Route::post('/register','register')->middleware('registerValidation');
        Route::post('/login','login');
    });
});

Route::middleware('customAuth')->group(function(){

    Route::controller(UserController::class)->group(function(){

        Route::get('/logout','logout');
        Route::get('/some-data','notDone');

    });

});

Route::controller(ProductController::class)->group(function(){

    Route::get('/products','getProducts');
    Route::get('/product/{id}','getSingleProduct');
});


Route::controller(UserController::class)->group(function(){
    Route::post('/cart','addToCart')->middleware(['checkAuth','cartValidation']);
    Route::get('/cart','getCartItems')->middleware(['checkAuth']);
    
});