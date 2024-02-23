<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\UserController;
use  App\Http\Controllers\AdminController;
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


//Auth routes

Route::middleware(['guest'])->group(function() {
    Route::controller(UserController::class)->group(function(){

        Route::post('/register','register')->middleware('registerValidation');
        Route::post('/login','login')->middleware('loginValidation');
    });
});


//User data routes
Route::middleware('customAuth')->group(function(){

    Route::controller(UserController::class)->group(function(){

        Route::get('/logout','logout');
        Route::get('/orders','getPreviousOrders')->middleware('userAuthorize');
        Route::post('/orders','placeOrder')->middleware('userAuthorize');
        Route::get('/orders/{id}','getSingleOrder')->middleware('userAuthorize');
        Route::post('/add-new-address','addNewAddress')->middleware('addressValidation');
        Route::post('complete-data','completeData')->middleware('completeDataValidation');
    });

});

//Admin routes
// Route::middleware([''])->group(function(){

    Route::controller(AdminController::class)->group(function(){

        Route::post('/admin/products','addProduct')->middleware('productValidation');
        Route::post('/admin/modify-product','modifyProduct')->middleware('modifyProductValidation');
        Route::post('/admin/categories','addCategory');
    
    });

// });


//Products Routes

Route::controller(ProductController::class)->group(function(){

    Route::get('/products','getProducts');
    Route::get('/product/{id}','getSingleProduct');
    // Route::get('/add-offer','addOffers');
    // Route::get('/add-products','addProducts') for adding data dynamicly from a json file

});


//Cart operations routes

Route::middleware('checkAuth')->group(function(){

    Route::controller(UserController::class)->group(function(){
        Route::post('/cart','addToCart')->middleware('cartValidation');
        Route::get('/cart','getCartItems');
        Route::delete('/cart','removeFromCart');
        Route::patch('/cart','clearCart');
        Route::get('/complete-data','completeData');
    });
});

