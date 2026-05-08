<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with(['product', 'customer'])
            ->latest('sold_at')
            ->paginate(50);

        $totalRevenue = (float) Sale::all()->sum(fn (Sale $s) => $s->total);
        $totalSold = (int) Sale::sum('quantity');

        return view('admin.sales.index', compact('sales', 'totalRevenue', 'totalSold'));
    }

    public function create(): View
    {
        $products = Product::orderBy('model')->get();

        return view('admin.sales.form', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_method' => ['required', 'string', 'max:32'],
            'customer_name' => ['required', 'string', 'max:128'],
            'customer_email' => ['nullable', 'email', 'max:128'],
            'customer_phone' => ['nullable', 'string', 'max:32'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            if ($product->quantity_on_hand < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Not enough stock for {$product->sku}. Available: {$product->quantity_on_hand}.",
                ]);
            }

            $customer = Customer::firstOrCreate([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? null,
                'phone' => $data['customer_phone'] ?? null,
            ]);

            Sale::create([
                'product_id' => $product->id,
                'customer_id' => $customer->id,
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'discount' => $data['discount'] ?? 0,
                'tax_rate' => $data['tax_rate'] ?? 0,
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
                'sold_at' => now(),
            ]);

            $product->decrement('quantity_on_hand', $data['quantity']);
        });

        return redirect()
            ->route('admin.sales.index')
            ->with('success', 'Sale recorded.');
    }
}
