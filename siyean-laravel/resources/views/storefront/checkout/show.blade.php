@extends('storefront.layouts.storefront')

@section('title', __('storefront.checkout.title'))

@section('content')
    <section class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-3xl font-bold mb-6">{{ __('storefront.checkout.title') }}</h1>

        <form method="POST" action="{{ route('storefront.checkout.store') }}" class="grid md:grid-cols-3 gap-6">
            @csrf
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <h2 class="font-semibold mb-3">{{ __('storefront.checkout.your_details') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-sm text-slate-600">{{ __('storefront.checkout.name') }}</span>
                            <input type="text" name="customer_name" required value="{{ old('customer_name', $user?->name) }}"
                                   class="mt-1 w-full border border-slate-300 rounded-md px-3 py-2">
                        </label>
                        <label class="block">
                            <span class="text-sm text-slate-600">{{ __('storefront.checkout.phone') }}</span>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                   class="mt-1 w-full border border-slate-300 rounded-md px-3 py-2">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="text-sm text-slate-600">{{ __('storefront.checkout.email') }}</span>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}"
                                   class="mt-1 w-full border border-slate-300 rounded-md px-3 py-2">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="text-sm text-slate-600">{{ __('storefront.checkout.notes') }}</span>
                            <textarea name="notes" rows="2" class="mt-1 w-full border border-slate-300 rounded-md px-3 py-2">{{ old('notes') }}</textarea>
                        </label>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <h2 class="font-semibold mb-3">{{ __('storefront.checkout.payment') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach ([
                            'cash' => ['💵', __('storefront.checkout.cash')],
                            'card' => ['💳', __('storefront.checkout.card')],
                            'qr' => ['📱', __('storefront.checkout.qr')],
                        ] as $val => [$icon, $label])
                            <label class="flex items-center gap-3 p-3 rounded-md border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400">
                                <input type="radio" name="payment_method" value="{{ $val }}" {{ $loop->first ? 'checked' : '' }} required class="accent-blue-600">
                                <span class="text-2xl">{{ $icon }}</span>
                                <span class="font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-3">{{ __('storefront.checkout.qr_help') }}</p>
                </div>
            </div>

            <aside class="bg-white border border-slate-200 rounded-xl p-5 h-fit">
                <h2 class="font-semibold mb-3">{{ __('storefront.checkout.order_summary') }}</h2>
                <ul class="text-sm divide-y divide-slate-100">
                    @foreach ($lines as $line)
                        <li class="py-2 flex justify-between gap-3">
                            <span class="text-slate-700">{{ $line['product']->model }} <span class="text-slate-400">× {{ $line['qty'] }}</span></span>
                            <span class="font-medium whitespace-nowrap">{{ config('shop.currency_symbol') }}{{ number_format($line['line_total'], 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3 pt-3 border-t border-slate-200 flex justify-between text-base font-bold">
                    <span>{{ __('storefront.cart.total') }}</span>
                    <span>{{ config('shop.currency_symbol') }}{{ number_format($subtotal, 2) }}</span>
                </div>
                <button type="submit" class="mt-5 w-full px-5 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700">
                    {{ __('storefront.checkout.place_order') }}
                </button>
            </aside>
        </form>
    </section>
@endsection
