<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->query('sort', 'sort_order');
        $direction = strtolower((string) $request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowed = ['sort_order', 'title', 'updated_at', 'created_at'];
        if (! in_array($sort, $allowed, true)) {
            $sort = 'sort_order';
        }

        $pages = Page::query()
            ->orderBy($sort, $direction)
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view('pages.index', compact('pages', 'sort', 'direction'));
    }

    public function create(): View
    {
        return view('pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $page = Page::create($data);

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', __('Page created.'));
    }

    public function show(Page $page): View
    {
        return view('pages.show', compact('page'));
    }

    public function edit(Page $page): View
    {
        return view('pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        if (array_key_exists('sort_order', $data) && $data['sort_order'] === null) {
            $data['sort_order'] = 0;
        }

        $page->update($data);

        return redirect()
            ->route('pages.edit', $page)
            ->with('success', __('Page updated.'));
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('pages.index')
            ->with('success', __('Page deleted.'));
    }

    /**
     * Published page for visitors (short URL under /p/{slug}).
     */
    public function display(Page $page): View
    {
        if (! $page->isPubliclyVisible()) {
            abort(404);
        }

        return view('pages.public', compact('page'));
    }
}
