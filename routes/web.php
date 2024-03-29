<?php

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

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
    return view('index');
})->name('home');




Route::controller(UserController::class)->group(function(){
    Route::get('/auth/google','googleAuth');
    Route::get('/auth/google/callback','googleCallback');
});

//Required Auth routes

Route::middleware('customAuth')->group(function(){

    Route::get('/complete-data', function () {

        $user = Auth::guard('api')->user();
        
        if($user->complete)
        throw new CustomException("Not found",404);

        return view('index');
    })->name('completeData');
    
});


