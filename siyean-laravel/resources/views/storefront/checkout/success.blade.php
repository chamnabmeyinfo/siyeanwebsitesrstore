@extends('storefront.layouts.storefront')

@section('title', __('storefront.checkout.success_title'))

@section('content')
    <section class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
        <div class="bg-white border border-slate-200 rounded-xl p-8 text-center" id="receipt">
            <div class="text-5xl mb-3">🧾</div>
            <h1 class="text-2xl font-bold">{{ __('storefront.checkout.success_title') }}</h1>
            <p class="text-slate-500 mt-1">{{ __('storefront.checkout.success_body') }}</p>

            <div class="text-left mt-6 border-t border-slate-200 pt-4 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">{{ __('storefront.checkout.order_id') }}</span><span class="font-mono font-semibold">{{ $receipt['order_id'] }}</span></div>
                <div class="flex justify-between mt-1"><span class="text-slate-500">{{ __('storefront.checkout.placed_at') }}</span><span>{{ $receipt['placed_at'] }}</span></div>
                <div class="flex justify-between mt-1"><span class="text-slate-500">{{ __('storefront.checkout.payment') }}</span><span class="capitalize">{{ $receipt['payment_method'] }}</span></div>
            </div>

            <table class="mt-6 w-full text-sm text-left">
                <thead class="text-xs uppercase text-slate-500">
                    <tr><th class="py-2">{{ __('storefront.cart.item') }}</th><th class="py-2 text-center">{{ __('storefront.cart.qty') }}</th><th class="py-2 text-right">{{ __('storefront.cart.total') }}</th></tr>
                </thead>
                <tbody>
                    @foreach ($receipt['items'] as $i)
                        <tr class="border-t border-slate-100">
                            <td class="py-2">{{ $i['model'] }}<div class="text-xs text-slate-400">{{ $i['sku'] }}</div></td>
                            <td class="py-2 text-center">{{ $i['qty'] }}</td>
                            <td class="py-2 text-right">{{ $receipt['currency'] }}{{ number_format($i['line_total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-slate-200"><td colspan="2" class="py-2 text-right text-slate-500">{{ __('storefront.cart.subtotal') }}</td><td class="py-2 text-right">{{ $receipt['currency'] }}{{ number_format($receipt['subtotal'], 2) }}</td></tr>
                    @if ($receipt['tax'] > 0)
                        <tr><td colspan="2" class="py-1 text-right text-slate-500">{{ __('storefront.checkout.tax') }}</td><td class="py-1 text-right">{{ $receipt['currency'] }}{{ number_format($receipt['tax'], 2) }}</td></tr>
                    @endif
                    <tr class="font-bold text-base"><td colspan="2" class="py-2 text-right">{{ __('storefront.cart.total') }}</td><td class="py-2 text-right">{{ $receipt['currency'] }}{{ number_format($receipt['total'], 2) }}</td></tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-5 flex gap-3 justify-center print:hidden">
            <button onclick="window.print()" class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-100">🖨️ {{ __('storefront.checkout.print') }}</button>
            <a href="{{ route('storefront.home') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('storefront.checkout.back_home') }}</a>
        </div>
    </section>

    @push('scripts')
        <script>
            // Open receipt as a popup-style modal on first visit by scrolling focus + flash glow.
            document.getElementById('receipt')?.scrollIntoView({behavior:'smooth', block:'start'});
        </script>
    @endpush
@endsection
