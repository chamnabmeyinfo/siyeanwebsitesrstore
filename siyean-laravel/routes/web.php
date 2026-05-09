<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\StoreMenuController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\WebsiteAccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyBridgeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Middleware\SetLocale;
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

Route::middleware(['auth', 'owner'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('products/import', [ProductController::class, 'importForm'])->name('products.import.form');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        Route::post('products/{product}/adjust', [ProductController::class, 'adjust'])->name('products.adjust');
        Route::resource('products', ProductController::class)->except(['show']);

        Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store');

        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');

        Route::get('store-menu', [StoreMenuController::class, 'index'])->name('store-menu.index');
        Route::post('store-menu', [StoreMenuController::class, 'store'])->name('store-menu.store');
        Route::post('store-menu/save', [StoreMenuController::class, 'save'])->name('store-menu.save');
        Route::delete('store-menu/{storeMenu}', [StoreMenuController::class, 'destroy'])->name('store-menu.destroy');
    });

// Storefront (Laravel-native, replaces legacy /siyean store).
Route::middleware(SetLocale::class)->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('storefront.home');
    Route::get('/products', [ProductCatalogController::class, 'index'])->name('storefront.products');
    Route::get('/products/{slug}', [ProductCatalogController::class, 'show'])->name('storefront.products.show');
    Route::get('/contact', [ContactController::class, 'index'])->name('storefront.contact');

    Route::get('/cart', [CartController::class, 'index'])->name('storefront.cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('storefront.cart.add');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('storefront.cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('storefront.cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('storefront.checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('storefront.checkout.store');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('storefront.checkout.success');

    Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('storefront.locale');

    Route::middleware('auth')->group(function (): void {
        Route::get('/account/orders', [WebsiteAccountController::class, 'orders'])->name('storefront.account.orders');
    });
});

// Legacy POS uses PHP native sessions (`$_SESSION['user_id']`). Laravel's StartSession must not run first,
// or session_start() becomes a no-op and staff login never persists across requests.
// Only catch unmatched paths under specific legacy roots so the storefront takes priority.
Route::any('/{any}', [LegacyBridgeController::class, 'handle'])
    ->where('any', '^(dashboard|inventory|sales|bookings|store|auth|api|siyean)(/.*)?$')
    ->withoutMiddleware([
        ValidateCsrfToken::class,
        StartSession::class,
        ShareErrorsFromSession::class,
    ]);
