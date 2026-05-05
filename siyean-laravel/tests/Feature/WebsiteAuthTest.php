<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

final class WebsiteAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_loads(): void
    {
        $response = $this->get('/auth/login');

        $response->assertOk();
        $response->assertSee('Sign in', false);
    }

    public function test_users_can_authenticate(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_register(): void
    {
        // Avoid collisions with any fixed row or MySQL state if env leaked before bootstrap ran.
        $email = sprintf('register-%s@example.com', bin2hex(random_bytes(5)));

        $response = $this->post('/auth/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => $email]);
        $user = User::query()->where('email', $email)->firstOrFail();
        $this->assertAuthenticatedAs($user);
    }

    public function test_users_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/auth/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/auth/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_request_is_blocked_for_non_credential_accounts(): void
    {
        Notification::fake();

        $response = $this->post('/auth/forgot-password', ['email' => 'not-found@example.com']);

        $response->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    public function test_password_reset_request_is_temporarily_banned_for_ten_minutes_after_non_credential_attempt(): void
    {
        $email = 'blocked@example.com';

        $firstResponse = $this->post('/auth/forgot-password', ['email' => $email]);
        $firstResponse->assertSessionHasErrors('email');

        $secondResponse = $this->post('/auth/forgot-password', ['email' => $email]);
        $secondResponse->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'temporarily blocked for this account',
            session('errors')->first('email')
        );
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();

        $token = Password::broker()->createToken($user);

        $response = $this->post('/auth/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('login', absolute: false));

        $this->assertTrue(Hash::check('NewPassword456!', $user->fresh()->getAuthPassword()));
    }
}
