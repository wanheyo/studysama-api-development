<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;


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
    return view('welcome');
});

Route::group(['prefix' => 'studysama'], function (){

    Route::group(['prefix' => 'auth'], function (){
        Route::get('reset-password', function () {
            return view('auth.passwords.reset', [
                'token' => request()->token,
                'email' => request()->email,
            ]);
        })->name('password.reset');
        
        Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
        Route::get('password/success', function () {
            return view('auth.passwords.success');
        })->name('password.success');
    });
});



