<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class CheckoutController extends Controller
{
    public function __construct(private readonly Cart $cart)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('storefront.cart');
        }

        return view('storefront.checkout.show', [
            'lines' => $lines,
            'subtotal' => $this->cart->subtotal(),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:128'],
            'customer_email' => ['nullable', 'email', 'max:128'],
            'customer_phone' => ['nullable', 'string', 'max:32'],
            'payment_method' => ['required', 'in:cash,card,qr'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('storefront.cart');
        }

        $taxRate = (float) config('shop.tax_rate', 0);

        $receipt = DB::transaction(function () use ($data, $lines, $taxRate) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email'] ?? null],
                ['name' => $data['customer_name'], 'phone' => $data['customer_phone'] ?? null]
            );

            $created = [];
            foreach ($lines as $line) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($line['product']->id);
                if ($product->quantity_on_hand < $line['qty']) {
                    throw ValidationException::withMessages([
                        'cart' => "Not enough stock for {$product->sku}. Available: {$product->quantity_on_hand}.",
                    ]);
                }

                $sale = Sale::create([
                    'product_id' => $product->id,
                    'customer_id' => $customer->id,
                    'quantity' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                    'discount' => 0,
                    'tax_rate' => $taxRate,
                    'payment_method' => $data['payment_method'],
                    'notes' => $data['notes'] ?? null,
                    'sold_at' => now(),
                ]);
                $product->decrement('quantity_on_hand', $line['qty']);

                $created[] = [
                    'sku' => $product->sku,
                    'model' => $product->model,
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                    'sale_id' => $sale->id,
                ];
            }

            $subtotal = (float) collect($created)->sum('line_total');
            $tax = round($subtotal * ($taxRate / 100), 2);
            $total = round($subtotal + $tax, 2);

            return [
                'order_id' => 'SR-' . str_pad((string) max(array_column($created, 'sale_id')), 6, '0', STR_PAD_LEFT),
                'placed_at' => now()->toDateTimeString(),
                'customer' => [
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'payment_method' => $data['payment_method'],
                'items' => $created,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'currency' => config('shop.currency_symbol', '$'),
            ];
        });

        $this->cart->clear();
        session()->flash('receipt', $receipt);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'receipt' => $receipt]);
        }

        return redirect()->route('storefront.checkout.success');
    }

    public function success(Request $request): View|RedirectResponse
    {
        $receipt = session('receipt');
        if (! $receipt) {
            return redirect()->route('storefront.home');
        }
        return view('storefront.checkout.success', ['receipt' => $receipt]);
    }
}
