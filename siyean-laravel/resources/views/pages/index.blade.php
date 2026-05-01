@extends('layouts.app')

@section('title', __('Pages').' — '.config('app.name'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('Pages') }}</h1>
            <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Create and manage site pages.') }}</p>
        </div>
        <a
            href="{{ route('pages.create') }}"
            class="inline-flex items-center justify-center rounded-md bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
        >
            {{ __('New page') }}
        </a>
    </div>

    @if ($pages->isEmpty())
        <div
            class="rounded-xl border border-dashed border-[#e3e3e0] bg-white p-12 text-center dark:border-[#3E3E3A] dark:bg-[#161615]"
        >
            <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ __('No pages yet.') }}</p>
            <a href="{{ route('pages.create') }}" class="mt-4 inline-block text-sm font-medium text-[#f53003] underline dark:text-[#FF4433]">
                {{ __('Create your first page') }}
            </a>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-[#e3e3e0] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
            <table class="min-w-full divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                <thead class="bg-[#FDFDFC] dark:bg-[#0a0a0a]">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A]">
                            <a href="{{ route('pages.index', ['sort' => 'title', 'direction' => $sort === 'title' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="hover:underline">
                                {{ __('Title') }}
                            </a>
                        </th>
                        <th scope="col" class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#706f6c] sm:table-cell dark:text-[#A1A09A]">
                            {{ __('Slug') }}
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A]">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="hidden px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#706f6c] lg:table-cell dark:text-[#A1A09A]">
                            <a href="{{ route('pages.index', ['sort' => 'sort_order', 'direction' => $sort === 'sort_order' && $direction === 'asc' ? 'desc' : 'asc']) }}" class="hover:underline">
                                {{ __('Order') }}
                            </a>
                        </th>
                        <th scope="col" class="relative px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A]">
                            <span class="sr-only">{{ __('Actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @foreach ($pages as $page)
                        <tr class="hover:bg-[#fafafa] dark:hover:bg-[#1a1a1a]">
                            <td class="px-4 py-3 text-sm font-medium">
                                <a href="{{ route('pages.edit', $page) }}" class="hover:underline">{{ $page->title }}</a>
                            </td>
                            <td class="hidden px-4 py-3 text-sm text-[#706f6c] sm:table-cell dark:text-[#A1A09A]">
                                <code class="rounded bg-[#f4f4f4] px-1.5 py-0.5 text-xs dark:bg-[#2a2a2a]">{{ $page->slug }}</code>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($page->isPubliclyVisible())
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                        {{ __('Published') }}
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-900 dark:bg-amber-950 dark:text-amber-200">
                                        {{ __('Draft') }}
                                    </span>
                                @endif
                            </td>
                            <td class="hidden px-4 py-3 text-right text-sm text-[#706f6c] lg:table-cell dark:text-[#A1A09A]">
                                {{ $page->sort_order }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                @if ($page->isPubliclyVisible())
                                    <a href="{{ route('pages.display', $page) }}" class="text-[#f53003] hover:underline dark:text-[#FF4433]" target="_blank" rel="noopener">
                                        {{ __('View') }}
                                    </a>
                                @else
                                    <a href="{{ route('pages.show', $page) }}" class="text-[#706f6c] hover:underline dark:text-[#A1A09A]">
                                        {{ __('Preview') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $pages->links() }}
        </div>
    @endif
@endsection
