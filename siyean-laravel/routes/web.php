<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\WebsiteAccountController;
use App\Http\Controllers\LegacyBridgeController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('auth/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('auth/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('auth/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('auth/register', [RegisteredUserController::class, 'store']);
    Route::get('auth/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('auth/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('auth/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('auth/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::get('auth/account', [WebsiteAccountController::class, 'show'])->name('account');
    Route::post('auth/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::any('/{any?}', [LegacyBridgeController::class, 'handle'])
    ->where('any', '.*')
    ->withoutMiddleware([ValidateCsrfToken::class]);
