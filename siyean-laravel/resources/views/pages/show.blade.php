@extends('layouts.app')

@section('title', $page->title.' — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('pages.index') }}" class="text-sm text-[#706f6c] hover:text-[#1b1b18] hover:underline dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
                ← {{ __('Back to pages') }}
            </a>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ $page->title }}</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a
                href="{{ route('pages.edit', $page) }}"
                class="inline-flex items-center justify-center rounded-md bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
            >
                {{ __('Edit') }}
            </a>
            @if ($page->isPubliclyVisible())
                <a
                    href="{{ route('pages.display', $page) }}"
                    class="inline-flex items-center justify-center rounded-md border border-[#e3e3e0] bg-white px-4 py-2 text-sm font-medium hover:bg-[#fafafa] dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:bg-[#1f1f1f]"
                    target="_blank"
                    rel="noopener"
                >
                    {{ __('Public URL') }}
                </a>
            @endif
        </div>
    </div>

    @if (! $page->isPubliclyVisible())
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100">
            {{ __('This page is not visible to visitors until it is published (and any scheduled publish date has passed).') }}
        </div>
    @endif

    @if ($page->excerpt)
        <p class="mb-6 text-lg text-[#706f6c] dark:text-[#A1A09A]">{{ $page->excerpt }}</p>
    @endif

    <article class="max-w-none leading-relaxed [&_a]:text-[#f53003] dark:[&_a]:text-[#FF4433]">
        {!! $page->body !!}
    </article>
@endsection
