<?php

declare(strict_types=1);

use App\Database;
use App\InventoryRepository;

require __DIR__ . '/../vendor/autoload.php';

$samples = [
    [
        'sku' => 'MBP14-2025-M4',
        'slug' => 'macbook-pro-14-m4-max',
        'model' => 'MacBook Pro 14" M4 Max',
        'storage_capacity' => 1024,
        'color' => 'Space Black',
        'cost_price' => 2899,
        'list_price' => 3499,
        'online_price' => 3399,
        'quantity_on_hand' => 6,
        'hero_image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1527443224154-d7cdb3c195b7?auto=format&w=1200&q=80',
        ],
        'web_description' => "Latest-generation M4 Max in Space Black. 14\" Liquid Retina XDR, 36GB unified memory. Includes 1-year SR MAC Shop warranty.",
        'visible_online' => 1,
    ],
    [
        'sku' => 'MBA13-2024-M3',
        'slug' => 'macbook-air-13-m3',
        'model' => 'MacBook Air 13" M3',
        'storage_capacity' => 512,
        'color' => 'Midnight',
        'cost_price' => 1199,
        'list_price' => 1599,
        'online_price' => 1499,
        'quantity_on_hand' => 10,
        'hero_image' => 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1510519138101-570d1dca3d66?auto=format&w=1200&q=80',
        ],
        'web_description' => 'Featherweight performance with all-day battery, MagSafe charging, and Touch ID. Perfect for students and creators on the move.',
        'visible_online' => 1,
    ],
    [
        'sku' => 'DELL-XPS15-2025',
        'slug' => 'dell-xps-15-oled-2025',
        'model' => 'Dell XPS 15 OLED',
        'storage_capacity' => 2048,
        'color' => 'Platinum',
        'cost_price' => 1899,
        'list_price' => 2499,
        'online_price' => 2399,
        'quantity_on_hand' => 4,
        'hero_image' => 'https://images.unsplash.com/photo-1481277542470-605612bd2d61?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1523475472560-d2df97ec485c?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1523475472560-e06502830871?auto=format&w=1200&q=80',
        ],
        'web_description' => '4K OLED touch display, NVIDIA RTX graphics, and CNC-machined chassis. Ideal creative workstation with 2-year SR MAC care.',
        'visible_online' => 1,
    ],
    [
        'sku' => 'LG-ULTRAFINE-4K',
        'slug' => 'lg-ultrafine-4k-monitor',
        'model' => 'LG UltraFine 4K Monitor',
        'storage_capacity' => 0,
        'color' => 'Black',
        'cost_price' => 499,
        'list_price' => 699,
        'online_price' => 649,
        'quantity_on_hand' => 8,
        'hero_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1457305237443-44c3d5a30b89?auto=format&w=1200&q=80',
        ],
        'web_description' => '27" Retina-ready display with Thunderbolt 3 passthrough. Perfect companion for MacBook Pro workstations.',
        'visible_online' => 1,
    ],
];

$db = Database::connection();
$repo = new InventoryRepository($db);

$created = 0;
foreach ($samples as $sample) {
    if ($repo->findBySku($sample['sku'])) {
        continue;
    }
    $repo->create($sample);
    $created++;
}

echo "Seed complete. {$created} new items added." . PHP_EOL;

