<?php

declare(strict_types=1);

namespace App\Http\Form;

use App\Support\Str;

final class InventoryFormMapper
{
    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public static function fromPost(array $post): array
    {
        $rawSlug = trim((string) ($post['slug'] ?? ''));
        if ($rawSlug === '') {
            $rawSlug = trim((string) ($post['model'] ?? ($post['sku'] ?? '')));
        }
        $slug = Str::slugify($rawSlug ?: (string) ($post['sku'] ?? ''));

        return [
            'sku' => trim((string) ($post['sku'] ?? '')),
            'slug' => $slug,
            'model' => trim((string) ($post['model'] ?? '')),
            'storage_capacity' => (int) ($post['storage_capacity'] ?? 0),
            'color' => trim((string) ($post['color'] ?? '')),
            'cost_price' => (float) ($post['cost_price'] ?? 0),
            'list_price' => (float) ($post['list_price'] ?? 0),
            'online_price' => isset($post['online_price']) ? (float) $post['online_price'] : null,
            'quantity_on_hand' => (int) ($post['quantity'] ?? 0),
            'hero_image' => trim((string) ($post['hero_image'] ?? '')) ?: null,
            'gallery_images' => $post['gallery_images'] ?? null,
            'web_description' => $post['web_description'] ?? null,
            'visible_online' => isset($post['visible_online']) ? (int) $post['visible_online'] : 1,
        ];
    }
}
