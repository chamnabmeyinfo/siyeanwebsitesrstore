<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\WebsiteAccountController;
use App\Http\Controllers\LegacyBridgeController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::get('account', [WebsiteAccountController::class, 'show'])->name('account');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Legacy POS uses PHP native sessions (`$_SESSION['user_id']`). Laravel's StartSession must not run first,
// or session_start() becomes a no-op and staff login never persists across requests.
Route::any('/{any?}', [LegacyBridgeController::class, 'handle'])
    ->where('any', '.*')
    ->withoutMiddleware([
        ValidateCsrfToken::class,
        StartSession::class,
        ShareErrorsFromSession::class,
    ]);
