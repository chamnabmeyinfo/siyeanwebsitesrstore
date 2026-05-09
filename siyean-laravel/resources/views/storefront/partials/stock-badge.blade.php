@php
    $status = $product->stock_status;
    $map = [
        'in_stock' => ['bg-emerald-100 text-emerald-800', __('storefront.stock.in')],
        'low_stock' => ['bg-amber-100 text-amber-800', __('storefront.stock.low')],
        'out_of_stock' => ['bg-rose-100 text-rose-800', __('storefront.stock.out')],
    ];
    [$cls, $label] = $map[$status];
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $cls }}">{{ $label }}</span>
