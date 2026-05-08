<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreMenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class StoreMenuController extends Controller
{
    public function index(): View
    {
        $items = StoreMenuItem::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.store-menu.index', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:64'],
            'href' => ['required', 'string', 'max:255'],
        ]);

        $maxOrder = (int) StoreMenuItem::max('sort_order');

        StoreMenuItem::create([
            'label' => $data['label'],
            'href' => $data['href'],
            'sort_order' => $maxOrder + 10,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.store-menu.index')->with('success', 'Menu link added.');
    }

    public function save(Request $request): RedirectResponse
    {
        $orderRaw = (string) $request->input('menu_order', '');
        $orderedIds = array_values(array_filter(
            array_map('intval', explode(',', $orderRaw)),
            static fn (int $id) => $id > 0
        ));

        $itemsPost = $request->input('items', []);
        if (!is_array($itemsPost)) {
            $itemsPost = [];
        }

        $position = 10;
        foreach ($orderedIds as $id) {
            $payload = $itemsPost[$id] ?? [];
            if (!is_array($payload)) {
                continue;
            }

            $item = StoreMenuItem::find($id);
            if (!$item) {
                continue;
            }

            $item->update([
                'label' => trim((string) ($payload['label'] ?? $item->label)) ?: $item->label,
                'href' => trim((string) ($payload['href'] ?? $item->href)) ?: $item->href,
                'is_active' => isset($payload['is_active']),
                'sort_order' => $position,
            ]);

            $position += 10;
        }

        return redirect()->route('admin.store-menu.index')->with('success', 'Menu saved.');
    }

    public function destroy(StoreMenuItem $storeMenu): RedirectResponse
    {
        $storeMenu->delete();

        return redirect()->route('admin.store-menu.index')->with('success', 'Menu link removed.');
    }
}
