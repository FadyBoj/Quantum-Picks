<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['guest'])->group(function() {
    Route::controller(UserController::class)->group(function(){

        Route::post('/register','register')->middleware('registerValidation');
        Route::post('/login','login');
        Route::get('/login','notDone')->name('login');
    });
});


Route::get('/hi',function (){
    return "nothing";
});