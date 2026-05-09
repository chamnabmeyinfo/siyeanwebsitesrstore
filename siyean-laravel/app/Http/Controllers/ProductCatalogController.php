<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProductCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $category = (string) $request->query('category', '');
        $categories = (array) config('shop.categories', []);

        $products = Product::where('visible_online', true)
            ->orderBy('model')
            ->get();

        if ($category !== '' && array_key_exists($category, $categories)) {
            $products = $products->filter(fn (Product $p) => $p->category === $category)->values();
        }

        return view('storefront.products.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $category,
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('visible_online', true)
            ->firstOrFail();

        $related = Product::where('visible_online', true)
            ->where('id', '!=', $product->id)
            ->orderByDesc('updated_at')
            ->take(4)
            ->get()
            ->filter(fn (Product $p) => $p->category === $product->category)
            ->values();

        return view('storefront.products.show', compact('product', 'related'));
    }
}
