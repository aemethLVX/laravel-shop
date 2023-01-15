<?php

namespace Domain\Auth\Routing;

use App\Contracts\RouteRegistrar;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\AuthController;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

class AuthRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {
            Route::controller(SignInController::class)->group(function () {
                Route::get('/login', 'index')->name('login');
                Route::post('/login', 'handle')
                    ->middleware('throttle:auth')
                    ->name('sign.in');
            });

            Route::controller(SignUpController::class)->group(function () {
                Route::get('/sign_up', 'index')->name('sign.up');
                Route::post('/sign_up', 'handle')
                    ->middleware('throttle:auth')
                    ->name('store');
            });

            Route::controller(AuthController::class)->group(function () {
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
        });
    }
}
