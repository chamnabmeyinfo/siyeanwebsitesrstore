<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('storefront.brand')) · {{ __('storefront.brand') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|noto-sans-khmer:400,500,600" rel="stylesheet" />
    @vite(['resources/css/storefront.css', 'resources/js/storefront.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased flex flex-col">
    @php
        $cartCount = app(\App\Services\Cart::class)->count();
        $locale = app()->getLocale();
        $locales = config('shop.locales');
    @endphp

    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('storefront.home') }}" class="flex items-center gap-2 font-bold text-lg">
                <span class="inline-flex w-8 h-8 rounded-md bg-slate-900 text-white items-center justify-center text-sm">SR</span>
                <span>{{ __('storefront.brand') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('storefront.home') }}" class="hover:text-blue-600 {{ request()->routeIs('storefront.home') ? 'text-blue-600' : '' }}">🏠 {{ __('storefront.nav.home') }}</a>
                <a href="{{ route('storefront.products') }}" class="hover:text-blue-600 {{ request()->routeIs('storefront.products*') ? 'text-blue-600' : '' }}">🛍️ {{ __('storefront.nav.products') }}</a>
                <a href="{{ route('storefront.contact') }}" class="hover:text-blue-600 {{ request()->routeIs('storefront.contact') ? 'text-blue-600' : '' }}">📞 {{ __('storefront.nav.contact') }}</a>
            </nav>

            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center text-xs border border-slate-200 rounded-full overflow-hidden">
                    @foreach ($locales as $code => $label)
                        <a href="{{ route('storefront.locale', $code) }}"
                           class="px-3 py-1.5 {{ $locale === $code ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            {{ $code === 'en' ? '🇬🇧' : '🇰🇭' }} {{ $label }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('storefront.cart') }}" class="relative inline-flex items-center px-3 py-2 text-sm rounded-md border border-slate-200 hover:bg-slate-100">
                    🛒 <span class="ml-1 hidden sm:inline">{{ __('storefront.nav.cart') }}</span>
                    <span data-cart-badge class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-[11px] font-semibold rounded-full bg-blue-600 text-white {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount }}</span>
                </a>

                @auth
                    <div class="relative" data-account-menu>
                        <button type="button" class="px-3 py-2 text-sm rounded-md border border-slate-200 hover:bg-slate-100" data-account-toggle>
                            👤 <span class="hidden sm:inline">{{ __('storefront.nav.account') }}</span>
                        </button>
                        <div class="hidden absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-md shadow-lg py-1 text-sm" data-account-dropdown>
                            <a href="{{ route('account') }}" class="block px-3 py-2 hover:bg-slate-50">{{ __('storefront.nav.account') }}</a>
                            <a href="{{ route('storefront.account.orders') }}" class="block px-3 py-2 hover:bg-slate-50">{{ __('storefront.nav.orders') }}</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1 pt-1">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2 hover:bg-slate-50">{{ __('storefront.nav.sign_out') }}</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm rounded-md bg-slate-900 text-white hover:bg-slate-800">
                        {{ __('storefront.nav.sign_in') }}
                    </a>
                @endauth

                <button type="button" data-mobile-toggle class="md:hidden p-2 rounded-md border border-slate-200">
                    <span class="sr-only">Menu</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>
            </div>
        </div>
        <nav data-mobile-nav class="hidden md:hidden border-t border-slate-200 bg-white">
            <a href="{{ route('storefront.home') }}" class="block px-4 py-3 border-b border-slate-100">🏠 {{ __('storefront.nav.home') }}</a>
            <a href="{{ route('storefront.products') }}" class="block px-4 py-3 border-b border-slate-100">🛍️ {{ __('storefront.nav.products') }}</a>
            <a href="{{ route('storefront.contact') }}" class="block px-4 py-3 border-b border-slate-100">📞 {{ __('storefront.nav.contact') }}</a>
            <div class="flex gap-2 px-4 py-3">
                @foreach ($locales as $code => $label)
                    <a href="{{ route('storefront.locale', $code) }}"
                       class="px-3 py-1.5 text-xs rounded-full border {{ $locale === $code ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    @if (session('success'))
        <div class="bg-emerald-50 text-emerald-800 border-b border-emerald-200 px-4 py-2 text-sm text-center">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-rose-50 text-rose-800 border-b border-rose-200 px-4 py-2 text-sm text-center">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-rose-50 text-rose-800 border-b border-rose-200 px-4 py-2 text-sm">
            <div class="max-w-7xl mx-auto">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-slate-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <div class="text-white font-semibold text-base mb-2">{{ __('storefront.brand') }}</div>
                <p class="text-slate-400">{{ app()->getLocale() === 'km' ? config('shop.tagline_km') : config('shop.tagline_en') }}</p>
            </div>
            <div>
                <div class="text-white font-semibold mb-2">{{ __('storefront.contact.title') }}</div>
                <p>{{ config('shop.address') }}</p>
                <p>📞 <a href="tel:{{ config('shop.phone') }}" class="hover:text-white">{{ config('shop.phone') }}</a></p>
                <p>💬 <a href="https://wa.me/{{ config('shop.whatsapp') }}" class="hover:text-white">WhatsApp</a></p>
            </div>
            <div>
                <div class="text-white font-semibold mb-2">{{ __('storefront.contact.hours') }}</div>
                <p>{{ config('shop.hours') }}</p>
            </div>
        </div>
        <div class="border-t border-slate-800 text-center py-4 text-xs text-slate-500">
            © {{ date('Y') }} {{ __('storefront.brand') }}
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
