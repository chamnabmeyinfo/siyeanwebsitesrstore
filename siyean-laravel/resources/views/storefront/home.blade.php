@extends('storefront.layouts.storefront')

@section('title', __('storefront.home.hero_title'))

@section('content')
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-24 grid md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-3xl md:text-5xl font-bold leading-tight">{{ __('storefront.home.hero_title') }}</h1>
                <p class="mt-4 text-slate-300 text-lg">{{ __('storefront.home.hero_subtitle') }}</p>
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('storefront.products') }}" class="inline-flex items-center px-5 py-3 rounded-md bg-white text-slate-900 font-semibold hover:bg-slate-100">
                        🛍️ {{ __('storefront.home.shop_now') }}
                    </a>
                    <a href="{{ route('storefront.contact') }}" class="inline-flex items-center px-5 py-3 rounded-md border border-slate-500 text-white hover:bg-slate-700">
                        📞 {{ __('storefront.nav.contact') }}
                    </a>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="aspect-square bg-white/10 rounded-3xl backdrop-blur flex items-center justify-center text-8xl">💻</div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <h2 class="text-2xl font-bold mb-6">{{ __('storefront.home.featured') }}</h2>
        @if ($featured->isEmpty())
            <p class="text-slate-500">{{ __('storefront.products.empty') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($featured as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>

    <section class="bg-white border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
            <h2 class="text-2xl font-bold mb-8">{{ __('storefront.home.why') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['✅', __('storefront.home.why_1_title'), __('storefront.home.why_1_body')],
                    ['💎', __('storefront.home.why_2_title'), __('storefront.home.why_2_body')],
                    ['🤝', __('storefront.home.why_3_title'), __('storefront.home.why_3_body')],
                ] as [$icon, $title, $body])
                    <div class="p-6 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-3xl mb-3">{{ $icon }}</div>
                        <h3 class="font-semibold mb-1">{{ $title }}</h3>
                        <p class="text-sm text-slate-600">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
