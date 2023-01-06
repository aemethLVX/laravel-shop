<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Requests\SignUpFormRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_store_success(): void
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

        $this->assertDatabaseHas('users', [
            'email' => $request['email']
        ]);

        $response
            ->assertValid()
            ->assertRedirect(route('home'));
    }
}
