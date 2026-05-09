@extends('storefront.layouts.storefront')

@section('title', $product->model)

@section('content')
    @php
        $gallery = is_array($product->gallery_images) ? $product->gallery_images : [];
        $hero = $product->hero_image ?: ($gallery[0] ?? null);
        $out = $product->stock_status === 'out_of_stock';
    @endphp
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <a href="{{ route('storefront.products') }}" class="text-sm text-slate-500 hover:text-slate-900">← {{ __('storefront.products.title') }}</a>

        <div class="mt-4 grid md:grid-cols-2 gap-8">
            <div>
                <div class="aspect-square bg-slate-100 rounded-xl overflow-hidden">
                    @if ($hero)
                        <img src="{{ $hero }}" alt="{{ $product->model }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-7xl text-slate-400">🖥️</div>
                    @endif
                </div>
                @if (count($gallery) > 1)
                    <div class="mt-3 grid grid-cols-4 gap-2">
                        @foreach ($gallery as $g)
                            <img src="{{ $g }}" alt="" class="aspect-square object-cover rounded-md border border-slate-200">
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs uppercase tracking-wide text-slate-500">{{ $product->category_label }}</span>
                    @include('storefront.partials.stock-badge', ['product' => $product])
                </div>
                <h1 class="text-3xl font-bold mt-2">{{ $product->model }}</h1>
                <div class="mt-2 text-2xl font-bold text-slate-900">{{ config('shop.currency_symbol') }}{{ number_format($product->display_price, 2) }}</div>

                <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
                    @if ($product->storage_capacity)
                        <div><dt class="text-slate-500">{{ __('storefront.products.storage') }}</dt><dd class="font-medium">{{ $product->storage_capacity }} GB</dd></div>
                    @endif
                    @if ($product->color)
                        <div><dt class="text-slate-500">{{ __('storefront.products.color') }}</dt><dd class="font-medium">{{ $product->color }}</dd></div>
                    @endif
                    <div><dt class="text-slate-500">{{ __('storefront.products.sku') }}</dt><dd class="font-medium">{{ $product->sku }}</dd></div>
                </dl>

                @if ($product->web_description)
                    <div class="prose prose-slate max-w-none mt-6 text-slate-700 whitespace-pre-line">{{ $product->web_description }}</div>
                @endif

                <form method="POST" action="{{ route('storefront.cart.add') }}" class="mt-6 flex items-center gap-3" data-cart-add>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label class="text-sm text-slate-600">{{ __('storefront.cart.qty') }}</label>
                    <input type="number" name="qty" value="1" min="1" max="{{ max(1, $product->quantity_on_hand) }}"
                           class="w-20 border border-slate-300 rounded-md px-2 py-2 text-center">
                    <button type="submit" {{ $out ? 'disabled' : '' }}
                            class="px-5 py-3 rounded-md font-semibold {{ $out ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                        🛒 {{ __('storefront.products.add') }}
                    </button>
                </form>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-xl font-bold mb-4">{{ __('storefront.products.related') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($related as $r)
                        @include('storefront.partials.product-card', ['product' => $r])
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
