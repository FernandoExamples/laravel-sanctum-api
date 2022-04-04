<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

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

Route::view('forgot_password', 'auth.passwords.reset', ['token' => request()->get('token'), 'email' => request()->get('email') ])->name('password.reset');
Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::get('/', function () {
    return File::get(public_path() . '/index.html');
});

Route::fallback(function () {
    return File::get(public_path() . '/index.html');
});