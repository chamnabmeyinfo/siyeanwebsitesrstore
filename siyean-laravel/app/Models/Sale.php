<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'product_id',
        'customer_id',
        'quantity',
        'unit_price',
        'discount',
        'tax_rate',
        'payment_method',
        'notes',
        'sold_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'sold_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getTotalAttribute(): float
    {
        $subtotal = (float) $this->unit_price * (int) $this->quantity;
        $afterDiscount = $subtotal - (float) $this->discount;

        return round($afterDiscount + ($afterDiscount * ((float) $this->tax_rate / 100)), 2);
    }
}
