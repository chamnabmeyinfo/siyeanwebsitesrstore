<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('model')->get();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product()]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', compact('product'));
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->validated($request));

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Added {$product->sku}.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, $product->id));

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Updated {$product->sku}.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $sku = $product->sku;
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Deleted {$sku}.");
    }

    public function adjust(Request $request, Product $product): RedirectResponse
    {
        $delta = (int) $request->input('delta', 0);
        if ($delta === 0) {
            return back()->with('error', 'Delta must be non-zero.');
        }

        $product->increment('quantity_on_hand', $delta);

        return back()->with('success', "Adjusted {$product->sku} by {$delta}.");
    }

    public function importForm(): View
    {
        return view('admin.products.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Unable to read uploaded file.');
        }

        $headers = fgetcsv($handle) ?: [];
        $normalized = array_map(fn ($h) => strtolower(trim((string) $h)), $headers);
        $required = ['sku', 'model', 'storage_capacity', 'color', 'cost_price', 'list_price', 'quantity_on_hand'];

        foreach ($required as $field) {
            if (!in_array($field, $normalized, true)) {
                fclose($handle);
                return back()->with('error', "Missing required column: {$field}");
            }
        }

        $created = 0;
        $updated = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = [];
            foreach ($normalized as $i => $col) {
                $data[$col] = $row[$i] ?? null;
            }

            if (empty($data['sku']) || empty($data['model'])) {
                continue;
            }

            $payload = [
                'sku' => trim((string) $data['sku']),
                'slug' => !empty($data['slug']) ? Str::slug((string) $data['slug']) : Str::slug((string) $data['sku']),
                'model' => trim((string) $data['model']),
                'storage_capacity' => (int) ($data['storage_capacity'] ?? 0),
                'color' => trim((string) ($data['color'] ?? '')),
                'cost_price' => (float) ($data['cost_price'] ?? 0),
                'list_price' => (float) ($data['list_price'] ?? 0),
                'online_price' => isset($data['online_price']) && $data['online_price'] !== '' ? (float) $data['online_price'] : null,
                'quantity_on_hand' => (int) ($data['quantity_on_hand'] ?? 0),
                'hero_image' => !empty($data['hero_image']) ? trim((string) $data['hero_image']) : null,
                'gallery_images' => $this->parseGallery($data['gallery_images'] ?? null),
                'web_description' => !empty($data['web_description']) ? (string) $data['web_description'] : null,
                'visible_online' => isset($data['visible_online']) ? (bool) (int) $data['visible_online'] : true,
            ];

            $existing = Product::where('sku', $payload['sku'])->first();
            if ($existing) {
                $existing->update($payload);
                $updated++;
            } else {
                Product::create($payload);
                $created++;
            }
        }

        fclose($handle);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Import complete: {$created} added, {$updated} updated.");
    }

    public function export(): StreamedResponse
    {
        $products = Product::orderBy('model')->get();

        return response()->streamDownload(function () use ($products) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'sku', 'slug', 'model', 'storage_capacity', 'color',
                'cost_price', 'list_price', 'online_price', 'quantity_on_hand',
                'hero_image', 'gallery_images', 'web_description', 'visible_online',
            ]);
            foreach ($products as $p) {
                fputcsv($out, [
                    $p->sku, $p->slug, $p->model, $p->storage_capacity, $p->color,
                    $p->cost_price, $p->list_price, $p->online_price, $p->quantity_on_hand,
                    $p->hero_image,
                    $p->gallery_images ? implode("\n", $p->gallery_images) : null,
                    $p->web_description,
                    $p->visible_online ? 1 : 0,
                ]);
            }
            fclose($out);
        }, 'products.csv', ['Content-Type' => 'text/csv']);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $skuRule = ['required', 'string', 'max:64', "unique:products,sku" . ($ignoreId ? ",{$ignoreId}" : '')];
        $slugRule = ['nullable', 'string', 'max:128', "unique:products,slug" . ($ignoreId ? ",{$ignoreId}" : '')];

        $data = $request->validate([
            'sku' => $skuRule,
            'slug' => $slugRule,
            'model' => ['required', 'string', 'max:128'],
            'storage_capacity' => ['required', 'integer', 'min:0'],
            'color' => ['required', 'string', 'max:64'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'list_price' => ['required', 'numeric', 'min:0'],
            'online_price' => ['nullable', 'numeric', 'min:0'],
            'quantity_on_hand' => ['required', 'integer', 'min:0'],
            'hero_image' => ['nullable', 'string', 'max:512'],
            'gallery_images' => ['nullable', 'string'],
            'web_description' => ['nullable', 'string'],
            'visible_online' => ['nullable'],
        ]);

        $data['visible_online'] = $request->boolean('visible_online');
        $data['gallery_images'] = $this->parseGallery($data['gallery_images'] ?? null);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['sku']);
        }

        return $data;
    }

    private function parseGallery(string|array|null $input): ?array
    {
        if ($input === null || $input === '') {
            return null;
        }
        if (is_array($input)) {
            $parts = $input;
        } else {
            $parts = preg_split('/[\n,]+/', $input) ?: [];
        }
        $parts = array_values(array_filter(array_map('trim', $parts)));

        return $parts ?: null;
    }
}
