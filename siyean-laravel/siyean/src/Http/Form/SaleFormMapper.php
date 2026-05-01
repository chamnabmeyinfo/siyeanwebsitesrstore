<?php

declare(strict_types=1);

namespace App\Http\Form;

final class SaleFormMapper
{
    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public static function fromPost(array $post): array
    {
        return [
            'sku' => trim((string) ($post['sku'] ?? '')),
            'customer_name' => trim((string) ($post['customer_name'] ?? '')),
            'customer_email' => trim((string) ($post['customer_email'] ?? '')),
            'customer_phone' => trim((string) ($post['customer_phone'] ?? '')),
            'quantity' => (int) ($post['quantity'] ?? 1),
            'unit_price' => (float) ($post['unit_price'] ?? 0),
            'discount' => (float) ($post['discount'] ?? 0),
            'tax_rate' => (float) ($post['tax_rate'] ?? 0),
            'payment_method' => trim((string) ($post['payment_method'] ?? 'cash')),
            'notes' => trim((string) ($post['notes'] ?? '')),
        ];
    }
}
