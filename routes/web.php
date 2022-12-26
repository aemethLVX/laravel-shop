<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'signIn')
        ->middleware('throttle:auth')
        ->name('sign.in');

    Route::get('/sign_up', 'signUp')->name('sign.up');
    Route::post('/sign_up', 'store')
        ->middleware('throttle:auth')
        ->name('store');

    Route::get('/forgot_password', 'forgotPassword')
        ->middleware('guest')
        ->name('password.forgot');
    Route::post('/forgot-password', 'requestPassword')
        ->middleware('guest')
        ->name('password.request');
    Route::get('/reset-password/{token}', 'resetPassword')
        ->middleware('guest')
        ->name('password.reset');
    Route::post('/reset-password', 'updatePassword')
        ->middleware('guest')
        ->name('password.update');

    Route::delete('/logout', 'logOut')->name('logout');

    Route::get('/auth/socialite/github', 'github')
        ->name('socialite.github');

    Route::get('/auth/socialite/github/callback', 'githubCallback')
        ->name('socialite.github.callback');
});
