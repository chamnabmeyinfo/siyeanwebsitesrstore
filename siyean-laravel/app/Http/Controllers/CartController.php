<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CartController extends Controller
{
    public function __construct(private readonly Cart $cart)
    {
    }

    public function index(): View
    {
        return view('storefront.cart.index', [
            'lines' => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        if ($product->stock_status === 'out_of_stock') {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => __('storefront.stock.out')], 422);
            }
            return back()->with('error', __('storefront.stock.out'));
        }

        $this->cart->add((int) $product->id, (int) ($data['qty'] ?? 1));

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'count' => $this->cart->count()]);
        }
        return redirect()->route('storefront.cart')->with('success', __('storefront.cart.added'));
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $data = $request->validate(['qty' => ['required', 'integer', 'min:0', 'max:99']]);
        $this->cart->update($product, (int) $data['qty']);
        return back();
    }

    public function remove(int $product): RedirectResponse
    {
        $this->cart->remove($product);
        return back();
    }
}
