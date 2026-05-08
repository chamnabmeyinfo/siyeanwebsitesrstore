<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'slug',
        'model',
        'storage_capacity',
        'color',
        'cost_price',
        'list_price',
        'online_price',
        'quantity_on_hand',
        'hero_image',
        'gallery_images',
        'web_description',
        'visible_online',
    ];

    protected $casts = [
        'storage_capacity' => 'integer',
        'cost_price' => 'decimal:2',
        'list_price' => 'decimal:2',
        'online_price' => 'decimal:2',
        'quantity_on_hand' => 'integer',
        'gallery_images' => 'array',
        'visible_online' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $product): void {
            if (empty($product->slug)) {
                $product->slug = Str::slug((string) $product->sku);
            }
        });
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getDisplayPriceAttribute(): float
    {
        return (float) ($this->online_price ?? $this->list_price);
    }
}
