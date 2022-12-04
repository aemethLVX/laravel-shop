<?php

namespace App\Http\Controllers;

use App\Http\Requests\{ForgotPasswordRequest, ResetPasswordRequest, SignInFormRequest, SignUpFormRequest};
use App\Models\User;
use Illuminate\Auth\Events\{PasswordReset, Registered};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function index(): Factory|View|Application
    {
        return view('auth.index');
    }

    public function signUp(): Factory|View|Application
    {
        return view('auth.sign_up');
    }

    public function forgotPassword(): Factory|View|Application
    {
        return view('auth.forgot_password');
    }

    public function requestPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['message' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(string $token): Factory|View|Application
    {
        return view('auth.reset_password', ['token' => $token]);
    }

    public function updatePassport(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(str()->random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('message', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function signIn(SignInFormRequest $request): RedirectResponse
    {
        if (!auth()->attempt($request->validated())) {
            return back()->withErrors([
                'email' => 'Неправильный логин или пароль',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('home'));
    }

    public function store(SignUpFormRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);

        event(new Registered($user));
        auth()->login($user);

        $request->session()->regenerate();
        return redirect()->intended(route('home'));
    }

    public function logOut(): RedirectResponse
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->intended(route('home'));
    }
}
