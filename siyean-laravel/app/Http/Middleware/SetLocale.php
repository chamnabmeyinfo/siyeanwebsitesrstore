<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale', 'en'));
        $allowed = array_keys((array) config('shop.locales', ['en' => 'EN']));
        if (! in_array($locale, $allowed, true)) {
            $locale = 'en';
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
