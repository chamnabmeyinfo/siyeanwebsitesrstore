<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

final class WebsiteAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Login throttling persists between tests via the cache; clear any
        // accumulated counters so each test starts from a clean slate.
        RateLimiter::clear($this->throttleKeyFor('test@example.com'));
    }

    private function throttleKeyFor(string $email, string $ip = '127.0.0.1'): string
    {
        return Str::transliterate(Str::lower($email)).'|'.$ip;
    }

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

    public function test_password_reset_request_returns_neutral_status_for_unknown_account(): void
    {
        Notification::fake();

        $response = $this->post('/auth/forgot-password', ['email' => 'not-found@example.com']);

        // Same neutral response shape as a real account: a `status` flash, no
        // `errors` bag, and no notification dispatched. This prevents the form
        // from being used to enumerate registered email addresses.
        $response->assertSessionHasNoErrors();
        $this->assertStringContainsString(
            'if an account exists',
            strtolower((string) session('status'))
        );
        Notification::assertNothingSent();
    }

    public function test_password_reset_request_returns_same_neutral_status_for_known_account(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/auth/forgot-password', ['email' => $user->email]);

        $response->assertSessionHasNoErrors();
        $this->assertStringContainsString(
            'if an account exists',
            strtolower((string) session('status'))
        );
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_login_is_rate_limited_after_too_many_failed_attempts(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/auth/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'too many',
            strtolower(session('errors')->first('email'))
        );

        // Cleanup so other tests are not affected by this test's accumulated counter.
        RateLimiter::clear($this->throttleKeyFor($user->email));
    }

    public function test_register_rejects_password_shorter_than_policy(): void
    {
        $email = sprintf('weak-%s@example.com', bin2hex(random_bytes(5)));

        $response = $this->post('/auth/register', [
            'name' => 'Weak User',
            'email' => $email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_inactive_user_cannot_authenticate_with_correct_password(): void
    {
        $user = User::factory()->inactive()->create();

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Same response shape as a wrong password — that is the point: the
        // form must not reveal that the account exists but is disabled.
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'do not match',
            strtolower(session('errors')->first('email'))
        );

        // Cleanup: this test added one failed-attempt to the throttle key.
        RateLimiter::clear($this->throttleKeyFor($user->email));
    }

    public function test_last_login_at_is_recorded_after_successful_login(): void
    {
        $user = User::factory()->create(['last_login_at' => null]);

        $this->assertNull($user->last_login_at);

        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh()->last_login_at);
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
