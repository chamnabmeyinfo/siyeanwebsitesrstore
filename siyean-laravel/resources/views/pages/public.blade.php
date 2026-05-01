@extends('layouts.app')

@section('title', ($page->meta_title ?: $page->title).' — '.config('app.name'))

@push('meta')
    @if ($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
@endpush

@section('content')
    <article class="mx-auto max-w-3xl">
        <header class="mb-8 border-b border-[#e3e3e0] pb-8 dark:border-[#3E3E3A]">
            <h1 class="text-3xl font-semibold tracking-tight">{{ $page->title }}</h1>
            @if ($page->excerpt)
                <p class="mt-3 text-lg text-[#706f6c] dark:text-[#A1A09A]">{{ $page->excerpt }}</p>
            @endif
        </header>
        <div class="max-w-none leading-relaxed [&_a]:text-[#f53003] dark:[&_a]:text-[#FF4433]">
            {!! $page->body !!}
        </div>
    </article>
@endsection
