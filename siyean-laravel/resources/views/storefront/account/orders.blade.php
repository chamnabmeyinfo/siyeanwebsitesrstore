@extends('storefront.layouts.storefront')

@section('title', __('storefront.account.orders_title'))

@section('content')
    <section class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-2xl font-bold mb-6">{{ __('storefront.account.orders_title') }}</h1>

        @if ($sales->isEmpty())
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500">
                {{ __('storefront.account.no_orders') }}
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="text-left p-3">{{ __('storefront.account.when') }}</th>
                            <th class="text-left p-3">{{ __('storefront.account.item') }}</th>
                            <th class="text-center p-3">{{ __('storefront.account.qty') }}</th>
                            <th class="text-left p-3">{{ __('storefront.account.paid') }}</th>
                            <th class="text-right p-3">{{ __('storefront.account.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr class="border-t border-slate-100">
                                <td class="p-3">{{ $sale->sold_at?->format('Y-m-d H:i') }}</td>
                                <td class="p-3">{{ $sale->product?->model }}</td>
                                <td class="p-3 text-center">{{ $sale->quantity }}</td>
                                <td class="p-3 capitalize">{{ $sale->payment_method }}</td>
                                <td class="p-3 text-right font-medium">{{ config('shop.currency_symbol') }}{{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
