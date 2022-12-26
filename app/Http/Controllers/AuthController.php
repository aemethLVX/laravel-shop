<?php

namespace App\Http\Controllers;

use App\Http\Requests\{ForgotPasswordRequest, ResetPasswordRequest, SignInFormRequest, SignUpFormRequest};
use App\Models\User;
use Illuminate\Auth\Events\{PasswordReset, Registered};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class AuthController extends Controller
{
    public function index(): Factory|View|Application|RedirectResponse
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

        if ($status == Password::RESET_LINK_SENT) {
            flash()->info(__($status));
            return back();
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(string $token): Factory|View|Application
    {
        return view('auth.reset_password', ['token' => $token]);
    }

    public function updatePassword(ResetPasswordRequest $request): RedirectResponse
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

        if ($status == Password::PASSWORD_RESET) {
            flash()->info(__($status));
            return redirect()->route('login');
        }

        return back()->withErrors(['email' => [__($status)]]);
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

    public function github(): SymfonyRedirectResponse|RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function githubCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::query()->updateOrCreate([
            'github_id' => $githubUser->id,
        ], [
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'password' => bcrypt(str()->random(20))
        ]);

        auth()->login($user);

        return redirect()->intended(route('home'));
    }
}
