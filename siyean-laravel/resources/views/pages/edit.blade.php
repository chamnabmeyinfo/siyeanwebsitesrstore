@extends('layouts.app')

@section('title', $page->title.' — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('pages.index') }}" class="text-sm text-[#706f6c] hover:text-[#1b1b18] hover:underline dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
                ← {{ __('Back to pages') }}
            </a>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ __('Edit page') }}</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a
                href="{{ route('pages.show', $page) }}"
                class="inline-flex items-center justify-center rounded-md border border-[#e3e3e0] bg-white px-4 py-2 text-sm font-medium hover:bg-[#fafafa] dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:bg-[#1f1f1f]"
            >
                {{ __('Preview (admin)') }}
            </a>
            <form method="post" action="{{ route('pages.destroy', $page) }}" onsubmit="return confirm(@json(__('Delete this page? This cannot be undone.')));">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-900 dark:bg-[#161615] dark:text-red-300 dark:hover:bg-red-950"
                >
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <form method="post" action="{{ route('pages.update', $page) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('pages._form', ['page' => $page, 'submitLabel' => __('Save changes')])
    </form>
@endsection
