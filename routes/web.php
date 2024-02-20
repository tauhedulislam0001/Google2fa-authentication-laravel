<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\UserAuthenticationController;
use Illuminate\Support\Facades\Route;

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

Auth::routes();

// ======================= verify user by qr code =======================

Route::get('/verify-2fa', [LoginController::class, 'show2faForm'])->name('verify-2fa');
Route::post('/verify-2fa/post', [LoginController::class, 'verify2fa'])->name('verify2fa');
Route::get('/custome/login', [LoginController::class, 'loginForm'])->name('custom.login');
Route::post('/custome/login/post', [LoginController::class, 'storeLogin'])->name('login.post');

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/userlist', [UserAuthenticationController::class, 'index'])->name('user.view');
    Route::get('/user/edit/{id}', [UserAuthenticationController::class, 'edit'])->name('user.edit');
    Route::post('/user/update/{id}', [UserAuthenticationController::class, 'update'])->name('user.update');
});
