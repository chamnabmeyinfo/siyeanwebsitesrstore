<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

final class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower((string) $request->string('email'));
        $lockKey = sprintf('password-reset:ban:%s|%s', $email, $request->ip());

        if (RateLimiter::tooManyAttempts($lockKey, 1)) {
            $seconds = RateLimiter::availableIn($lockKey);

            return back()->withErrors([
                'email' => __('Password reset is temporarily blocked for this account. Try again in :seconds seconds.', ['seconds' => $seconds]),
            ])->onlyInput('email');
        }

        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user instanceof User || blank($user->password)) {
            RateLimiter::hit($lockKey, 600);

            return back()->withErrors([
                'email' => __('Password reset is temporarily blocked for this account. Try again in 10 minutes.'),
            ])->onlyInput('email');
        }

        RateLimiter::clear($lockKey);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
