<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SocioliteController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('auth/google', [SocioliteController::class, 'googleLogin'])->name('auth.google');
Route::get('auth/googlecallback', [SocioliteController::class, 'googleAuthData'])->name('auth.googlecallback');    