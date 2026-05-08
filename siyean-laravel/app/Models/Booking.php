<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public const STATUSES = ['pending', 'confirmed', 'picked_up', 'cancelled'];

    protected $fillable = [
        'product_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'quantity',
        'deposit_amount',
        'preferred_date',
        'preferred_time',
        'status',
        'notes',
        'converted_sale_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'deposit_amount' => 'decimal:2',
        'preferred_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }
}
