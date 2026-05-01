@extends('layouts.app')

@section('title', __('New page').' — '.config('app.name'))

@section('content')
    <div class="mb-8">
        <a href="{{ route('pages.index') }}" class="text-sm text-[#706f6c] hover:text-[#1b1b18] hover:underline dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
            ← {{ __('Back to pages') }}
        </a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ __('New page') }}</h1>
    </div>

    <form method="post" action="{{ route('pages.store') }}" class="space-y-6">
        @csrf
        @include('pages._form', ['page' => null, 'submitLabel' => __('Create page')])
    </form>
@endsection
