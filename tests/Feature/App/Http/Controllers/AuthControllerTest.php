<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_success(): void
    {
        $this->get(action([AuthController::class, 'index']))
            ->assertOk()
            ->assertViewIs('auth.index')
            ->assertSee('Вход в аккаунт');
    }

    public function test_sign_up_page_success(): void
    {
        $this->get(action([AuthController::class, 'signUp']))
            ->assertOk()
            ->assertViewIs('auth.sign_up')
            ->assertSee('Регистрация');
    }

    public function test_forgot_password_page_success(): void
    {
        $this->get(action([AuthController::class, 'forgotPassword']))
            ->assertOk()
            ->assertViewIs('auth.forgot_password')
            ->assertSee('Забыли пароль');
    }

    public function test_sign_in_success(): void
    {
        $password = '12345678';

        $user = UserFactory::new()->create([
            'email' => 'test@mail.ru',
            'password' => bcrypt($password),
        ]);

        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $password,
        ]);

        $response = $this->post(
            action([AuthController::class, 'signIn']),
            $request
        );

        $response->assertValid()->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_store_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequest::factory()->create([
            'email' => 'test@mail.ru',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $request['email']
        ]);

        $response = $this->post(
            action([AuthController::class, 'store']),
            $request
        );

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $request['email']
        ]);

        $user = User::query()->where('email', $request['email'])->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }

    public function test_request_password_success(): void
    {
        $email = 'test@mail.ru';

        $this->assertGuest();

        UserFactory::new()->create([
            'email' => $email,
        ]);

        $request = ForgotPasswordRequest::factory()->create([
            'email' => $email,
        ]);

        $status = Password::sendResetLink(
            $request
        );

        $this->assertEquals(Password::RESET_LINK_SENT, $status);
    }

    public function test_log_out_success(): void
    {
        $user = UserFactory::new()->create([
            'email' => 'test@mail.ru',
        ]);

        $this->actingAs($user)->delete(
            action([AuthController::class, 'logOut'])
        );

        $this->assertGuest();
    }
}
