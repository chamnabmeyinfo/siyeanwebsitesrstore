<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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

        // Always return the same neutral response so the form does not reveal
        // whether an account exists for the supplied address (avoids account
        // enumeration via the password reset form).
        $neutralStatus = __('If an account exists for that email, we have sent a password reset link.');

        $user = User::query()
            ->where('email', $email)
            ->first();

        if ($user instanceof User) {
            Password::sendResetLink(['email' => $email]);
        }

        return back()->with('status', $neutralStatus);
    }
}
