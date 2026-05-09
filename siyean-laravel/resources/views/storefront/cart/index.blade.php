@extends('storefront.layouts.storefront')

@section('title', __('storefront.cart.title'))

@section('content')
    <section class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-3xl font-bold mb-6">{{ __('storefront.cart.title') }}</h1>

        @if ($lines->isEmpty())
            <div class="bg-white border border-slate-200 rounded-xl p-10 text-center">
                <p class="text-slate-500 mb-4">{{ __('storefront.cart.empty') }}</p>
                <a href="{{ route('storefront.products') }}" class="inline-block px-5 py-2.5 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700">
                    {{ __('storefront.cart.continue') }}
                </a>
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                        <tr>
                            <th class="text-left p-3">{{ __('storefront.cart.item') }}</th>
                            <th class="text-right p-3">{{ __('storefront.cart.price') }}</th>
                            <th class="text-center p-3">{{ __('storefront.cart.qty') }}</th>
                            <th class="text-right p-3">{{ __('storefront.cart.total') }}</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lines as $line)
                            @php $p = $line['product']; @endphp
                            <tr class="border-t border-slate-100">
                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        @if ($p->hero_image)
                                            <img src="{{ $p->hero_image }}" class="w-12 h-12 object-cover rounded-md">
                                        @endif
                                        <div>
                                            <a href="{{ route('storefront.products.show', $p->slug) }}" class="font-medium hover:text-blue-600">{{ $p->model }}</a>
                                            <div class="text-xs text-slate-500">{{ $p->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right p-3">{{ config('shop.currency_symbol') }}{{ number_format($line['unit_price'], 2) }}</td>
                                <td class="text-center p-3">
                                    <form method="POST" action="{{ route('storefront.cart.update', $p->id) }}" class="inline-flex items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="qty" value="{{ $line['qty'] }}" min="0" max="99"
                                               class="w-16 border border-slate-300 rounded-md px-2 py-1 text-center text-sm"
                                               onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="text-right p-3 font-medium">{{ config('shop.currency_symbol') }}{{ number_format($line['line_total'], 2) }}</td>
                                <td class="text-right p-3">
                                    <form method="POST" action="{{ route('storefront.cart.remove', $p->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs">{{ __('storefront.cart.remove') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <a href="{{ route('storefront.products') }}" class="text-blue-600 hover:underline text-sm">← {{ __('storefront.cart.continue') }}</a>
                <div class="text-right">
                    <div class="text-slate-500 text-sm">{{ __('storefront.cart.subtotal') }}</div>
                    <div class="text-2xl font-bold">{{ config('shop.currency_symbol') }}{{ number_format($subtotal, 2) }}</div>
                    <a href="{{ route('storefront.checkout') }}" class="mt-3 inline-block px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700">
                        {{ __('storefront.cart.checkout') }} →
                    </a>
                </div>
            </div>
        @endif
    </section>
@endsection
