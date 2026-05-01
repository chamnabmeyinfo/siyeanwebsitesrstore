<div class="rounded-xl border border-[#e3e3e0] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
    <div class="grid gap-6">
        <div>
            <label for="title" class="block text-sm font-medium">{{ __('Title') }}</label>
            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title', $page->title ?? '') }}"
                required
                class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
            />
            @error('title')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium">{{ __('URL slug') }}</label>
            <p class="mt-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Lowercase letters, numbers, and hyphens only. Leave blank to generate from the title.') }}</p>
            <input
                type="text"
                name="slug"
                id="slug"
                value="{{ old('slug', $page->slug ?? '') }}"
                class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 font-mono text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                autocomplete="off"
            />
            @error('slug')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="excerpt" class="block text-sm font-medium">{{ __('Excerpt') }}</label>
            <textarea
                name="excerpt"
                id="excerpt"
                rows="3"
                class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
            >{{ old('excerpt', $page->excerpt ?? '') }}</textarea>
            @error('excerpt')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="body" class="block text-sm font-medium">{{ __('Content') }}</label>
            <p class="mt-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('HTML is allowed. Sanitize untrusted input in production.') }}</p>
            <textarea
                name="body"
                id="body"
                rows="14"
                class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 font-mono text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
            >{{ old('body', $page->body ?? '') }}</textarea>
            @error('body')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="sort_order" class="block text-sm font-medium">{{ __('Sort order') }}</label>
                <input
                    type="number"
                    name="sort_order"
                    id="sort_order"
                    min="0"
                    value="{{ old('sort_order', $page->sort_order ?? 0) }}"
                    class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                />
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="published_at" class="block text-sm font-medium">{{ __('Publish date') }}</label>
                <input
                    type="datetime-local"
                    name="published_at"
                    id="published_at"
                    value="{{ old('published_at', isset($page) && $page->published_at ? $page->published_at->format('Y-m-d\TH:i') : '') }}"
                    class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                />
                @error('published_at')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="meta_title" class="block text-sm font-medium">{{ __('Meta title') }}</label>
                <input
                    type="text"
                    name="meta_title"
                    id="meta_title"
                    value="{{ old('meta_title', $page->meta_title ?? '') }}"
                    class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                />
                @error('meta_title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium">{{ __('Meta description') }}</label>
                <textarea
                    name="meta_description"
                    id="meta_description"
                    rows="2"
                    class="mt-1 block w-full rounded-md border border-[#e3e3e0] bg-white px-3 py-2 text-sm shadow-sm focus:border-[#1b1b18] focus:outline-none focus:ring-1 focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC] dark:focus:ring-[#EDEDEC]"
                >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input
                type="checkbox"
                name="is_published"
                id="is_published"
                value="1"
                class="size-4 rounded border-[#e3e3e0] text-[#1b1b18] focus:ring-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a]"
                @checked(old('is_published', $page->is_published ?? false))
            />
            <label for="is_published" class="text-sm font-medium">{{ __('Published') }}</label>
            @error('is_published')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end border-t border-[#e3e3e0] pt-6 dark:border-[#3E3E3A]">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-md bg-[#1b1b18] px-5 py-2 text-sm font-medium text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
            >
                {{ $submitLabel }}
            </button>
        </div>
    </div>
</div>
