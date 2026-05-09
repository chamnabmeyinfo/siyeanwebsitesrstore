<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

final class Cart
{
    private const KEY = 'cart';

    public function items(): array
    {
        return (array) session(self::KEY, []);
    }

    public function add(int $productId, int $qty = 1): void
    {
        $qty = max(1, $qty);
        $items = $this->items();
        $key = (string) $productId;
        $items[$key] = ($items[$key] ?? 0) + $qty;
        session([self::KEY => $items]);
    }

    public function update(int $productId, int $qty): void
    {
        $items = $this->items();
        $key = (string) $productId;
        if ($qty <= 0) {
            unset($items[$key]);
        } else {
            $items[$key] = $qty;
        }
        session([self::KEY => $items]);
    }

    public function remove(int $productId): void
    {
        $items = $this->items();
        unset($items[(string) $productId]);
        session([self::KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    /**
     * Hydrate cart with Product models, returning collection of
     * ['product' => Product, 'qty' => int, 'unit_price' => float, 'line_total' => float].
     */
    public function lines(): Collection
    {
        $items = $this->items();
        if ($items === []) {
            return collect();
        }
        $products = Product::whereIn('id', array_keys($items))->get()->keyBy('id');

        return collect($items)
            ->filter(fn ($qty, $id) => $products->has((int) $id))
            ->map(function ($qty, $id) use ($products) {
                $p = $products[(int) $id];
                $unit = (float) $p->display_price;
                return [
                    'product' => $p,
                    'qty' => (int) $qty,
                    'unit_price' => $unit,
                    'line_total' => round($unit * (int) $qty, 2),
                ];
            })
            ->values();
    }

    public function subtotal(): float
    {
        return (float) $this->lines()->sum('line_total');
    }
}
