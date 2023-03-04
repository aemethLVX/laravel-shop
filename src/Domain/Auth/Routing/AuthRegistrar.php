<?php

namespace Domain\Auth\Routing;

use App\Contracts\RouteRegistrar;
use App\Http\Controllers\Auth\{ForgotPasswordController,
    ResetPasswordController,
    SignInController,
    SignUpController,
    SocialAuthController};
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
                Route::delete('/logout', 'logOut')->name('logout');
            });

            Route::controller(SignUpController::class)->group(function () {
                Route::get('/sign_up', 'index')->name('sign.up');
                Route::post('/sign_up', 'handle')
                    ->middleware('throttle:auth')
                    ->name('store');
            });

            Route::controller(SocialAuthController::class)->group(function () {
                Route::get('/auth/socialite/{driver}', 'redirect')
                    ->name('socialite.github');
                Route::get('/auth/socialite/{driver}/callback', 'callback')
                    ->name('socialite.github.callback');
            });

            Route::controller(ForgotPasswordController::class)->group(function () {
                Route::get('/forgot_password', 'index')
                    ->middleware('guest')
                    ->name('password.forgot');
                Route::post('/forgot-password', 'handle')
                    ->middleware('guest')
                    ->name('password.request');
            });

            Route::controller(ResetPasswordController::class)->group(function () {
                Route::get('/reset-password/{token}', 'index')
                    ->middleware('guest')
                    ->name('password.reset');
                Route::post('/reset-password', 'handle')
                    ->middleware('guest')
                    ->name('password.update');
            });
        });
    }
}
