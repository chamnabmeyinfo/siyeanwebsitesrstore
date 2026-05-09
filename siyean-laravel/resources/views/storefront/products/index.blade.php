@extends('storefront.layouts.storefront')

@section('title', __('storefront.products.title'))

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl md:text-3xl font-bold mb-6">{{ __('storefront.products.title') }}</h1>

        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('storefront.products') }}"
               class="px-4 py-2 rounded-full text-sm font-medium border {{ $activeCategory === '' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white border-slate-200 hover:bg-slate-100' }}">
                {{ __('storefront.products.all') }}
            </a>
            @foreach ($categories as $slug => $label)
                <a href="{{ route('storefront.products', ['category' => $slug]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium border {{ $activeCategory === $slug ? 'bg-slate-900 text-white border-slate-900' : 'bg-white border-slate-200 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($products->isEmpty())
            <p class="text-slate-500 py-12 text-center">{{ __('storefront.products.empty') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </section>
@endsection
