@extends('storefront.layouts.storefront')

@section('title', __('storefront.contact.title'))

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-3xl font-bold mb-6">{{ __('storefront.contact.title') }}</h1>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-4 text-sm">
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <div class="text-slate-500 text-xs uppercase tracking-wide">{{ __('storefront.contact.address') }}</div>
                    <div class="font-medium mt-1">📍 {{ config('shop.address') }}</div>
                    <a href="{{ config('shop.map_url') }}" target="_blank" class="text-blue-600 text-sm hover:underline mt-2 inline-block">{{ __('storefront.contact.open_map') }} →</a>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <div class="text-slate-500 text-xs uppercase tracking-wide">{{ __('storefront.contact.phone') }}</div>
                    <a href="tel:{{ config('shop.phone') }}" class="font-medium mt-1 block">📞 {{ config('shop.phone') }}</a>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <div class="text-slate-500 text-xs uppercase tracking-wide">{{ __('storefront.contact.whatsapp') }}</div>
                    <a href="https://wa.me/{{ config('shop.whatsapp') }}" target="_blank" class="font-medium mt-1 inline-flex items-center gap-2 text-emerald-700 hover:underline">
                        💬 {{ __('storefront.contact.chat') }}
                    </a>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-5">
                    <div class="text-slate-500 text-xs uppercase tracking-wide">{{ __('storefront.contact.hours') }}</div>
                    <div class="font-medium mt-1">🕒 {{ config('shop.hours') }}</div>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden min-h-[400px]">
                <iframe src="{{ config('shop.map_embed') }}" class="w-full h-full min-h-[400px]" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
@endsection
