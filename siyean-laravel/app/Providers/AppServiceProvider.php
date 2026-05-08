<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function (): Password {
            $rule = Password::min(12)->mixedCase()->numbers();

            // `uncompromised()` calls the Have I Been Pwned API. Skip that during
            // unit tests so suites can run offline / deterministically.
            return $this->app->runningUnitTests() ? $rule : $rule->uncompromised();
        });
    }
}
