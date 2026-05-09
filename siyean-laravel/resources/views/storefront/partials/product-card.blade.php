@php
    $img = $product->hero_image ?: (is_array($product->gallery_images) ? ($product->gallery_images[0] ?? null) : null);
    $out = $product->stock_status === 'out_of_stock';
@endphp
<article class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
    <a href="{{ route('storefront.products.show', $product->slug) }}" class="block aspect-square bg-slate-100 overflow-hidden">
        @if ($img)
            <img src="{{ $img }}" alt="{{ $product->model }}" class="w-full h-full object-cover" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-400 text-4xl">🖥️</div>
        @endif
    </a>
    <div class="p-4 flex flex-col gap-2 flex-1">
        <div class="flex items-center justify-between gap-2">
            <span class="text-[11px] uppercase tracking-wide text-slate-500">{{ $product->category_label }}</span>
            @include('storefront.partials.stock-badge', ['product' => $product])
        </div>
        <h3 class="font-semibold text-slate-900 leading-tight">
            <a href="{{ route('storefront.products.show', $product->slug) }}">{{ $product->model }}</a>
        </h3>
        <div class="text-sm text-slate-500">
            @if ($product->storage_capacity) {{ $product->storage_capacity }}GB · @endif{{ $product->color }}
        </div>
        <div class="mt-auto flex items-center justify-between gap-2 pt-2">
            <div class="font-bold text-lg">{{ config('shop.currency_symbol') }}{{ number_format($product->display_price, 2) }}</div>
            <form method="POST" action="{{ route('storefront.cart.add') }}" data-cart-add>
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="qty" value="1">
                <button type="submit" {{ $out ? 'disabled' : '' }}
                        class="px-3 py-2 text-xs font-semibold rounded-md {{ $out ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                    {{ __('storefront.products.add') }}
                </button>
            </form>
        </div>
    </div>
</article>
