<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $allowed = array_keys((array) config('shop.locales', ['en' => 'EN']));
        if (in_array($locale, $allowed, true)) {
            $request->session()->put('locale', $locale);
        }

        return back();
    }
}
