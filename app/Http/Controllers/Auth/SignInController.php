<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\{SignInFormRequest};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\RedirectResponse;

class SignInController
{
    public function index(): Factory|View|Application|RedirectResponse
    {
        return view('auth.index');
    }

    public function handle(SignInFormRequest $request): RedirectResponse
    {
        if (!auth()->attempt($request->validated())) {
            return back()->withErrors([
                'email' => 'Неправильный логин или пароль',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('home'));
    }
}