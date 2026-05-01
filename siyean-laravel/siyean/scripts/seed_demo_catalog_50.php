<?php

declare(strict_types=1);

/**
 * Seeds ~50 demo inventory rows for storefront / POS preview (SKU prefix DEMO-).
 * Safe to run multiple times: skips SKUs that already exist.
 *
 * Usage: php scripts/seed_demo_catalog_50.php
 */

use App\Database;
use App\InventoryRepository;

require __DIR__ . '/../vendor/autoload.php';

Database::migrate();
$db = Database::connection();
$repo = new InventoryRepository($db);

// Curated Unsplash tech / Mac-adjacent imagery (stable URLs).
$I = [
    'laptop1' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1600&q=85',
    'laptop2' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&w=1600&q=85',
    'laptop3' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&w=1600&q=85',
    'laptop4' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&w=1600&q=85',
    'desk1' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&w=1600&q=85',
    'desk2' => 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?auto=format&w=1600&q=85',
    'monitor1' => 'https://images.unsplash.com/photo-1527443224154-d7cdb3c195b7?auto=format&w=1600&q=85',
    'monitor2' => 'https://images.unsplash.com/photo-1457305237443-44c3d5a30b89?auto=format&w=1600&q=85',
    'keyboard' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&w=1600&q=85',
    'mouse' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&w=1600&q=85',
    'headphones' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&w=1600&q=85',
    'office' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&w=1600&q=85',
    'minimal' => 'https://images.unsplash.com/photo-1510519138101-570d1dca3d66?auto=format&w=1600&q=85',
];

/**
 * @return array{cost: float, list: float, online: float}
 */
function demoPrices(float $list, float $onlineDiscountRatio = 0.06): array
{
    $online = round($list * (1 - $onlineDiscountRatio), 2);
    $cost = round($list * 0.71, 2);

    return ['cost' => $cost, 'list' => $list, 'online' => $online];
}

/**
 * @param array<string, string> $images
 * @return array<int, string>
 */
function galleryFrom(array $images, string ...$keys): array
{
    $out = [];
    foreach ($keys as $k) {
        if (isset($images[$k])) {
            $out[] = $images[$k];
        }
    }

    return array_values(array_unique($out));
}

/** @return list<array<string, mixed>> */
function buildDemoCatalog(array $I): array
{
    $rows = [];
    $n = 0;

    $push = static function (array $row) use (&$rows, &$n): void {
        $n++;
        $sku = sprintf('DEMO-%03d', $n);
        $rows[] = array_merge([
            'sku' => $sku,
            'slug' => 'catalog-' . strtolower($sku),
            'quantity_on_hand' => random_int(2, 14),
            'visible_online' => 1,
        ], $row);
    };

    // —— MacBook Air (16) ——
    $airCfg = [
        ['MacBook Air 13" M3', 'Midnight', 256, 1099],
        ['MacBook Air 13" M3', 'Starlight', 512, 1299],
        ['MacBook Air 13" M3', 'Space Gray', 512, 1299],
        ['MacBook Air 13" M3', 'Silver', 1024, 1499],
        ['MacBook Air 13" M4', 'Midnight', 512, 1399],
        ['MacBook Air 13" M4', 'Sky Blue', 512, 1399],
        ['MacBook Air 13" M4', 'Silver', 1024, 1599],
        ['MacBook Air 15" M3', 'Starlight', 512, 1499],
        ['MacBook Air 15" M3', 'Midnight', 1024, 1699],
        ['MacBook Air 15" M4', 'Space Gray', 512, 1599],
        ['MacBook Air 15" M4', 'Silver', 1024, 1799],
        ['MacBook Air 13" M2', 'Space Gray', 256, 999],
        ['MacBook Air 13" M2', 'Silver', 512, 1199],
        ['MacBook Air 13" M2', 'Midnight', 512, 1199],
        ['MacBook Air 15" M2', 'Starlight', 512, 1399],
        ['MacBook Air 15" M2', 'Midnight', 512, 1399],
    ];
    foreach ($airCfg as [$model, $color, $storage, $list]) {
        $p = demoPrices((float) $list);
        $push([
            'model' => $model,
            'storage_capacity' => $storage,
            'color' => $color,
            'cost_price' => $p['cost'],
            'list_price' => $p['list'],
            'online_price' => $p['online'],
            'hero_image' => $I['laptop1'],
            'gallery_images' => galleryFrom($I, 'laptop1', 'laptop2', 'minimal'),
            'web_description' => "Ultra-thin everyday Mac with silent fanless design, Liquid Retina display, and all-day battery. Perfect for school, travel, and light creative work. Includes SR Mac Shop inspection checklist and 90-day hardware confidence window.",
        ]);
    }

    // —— MacBook Pro (14) ——
    $proCfg = [
        ['MacBook Pro 14" M4 Pro', 'Space Black', 512, 1999],
        ['MacBook Pro 14" M4 Pro', 'Silver', 1024, 2399],
        ['MacBook Pro 14" M4 Max', 'Space Black', 1024, 3199],
        ['MacBook Pro 14" M4 Max', 'Silver', 2048, 3799],
        ['MacBook Pro 14" M3 Pro', 'Space Gray', 512, 1849],
        ['MacBook Pro 14" M3 Max', 'Silver', 1024, 2899],
        ['MacBook Pro 16" M4 Pro', 'Silver', 1024, 2799],
        ['MacBook Pro 16" M4 Max', 'Space Black', 2048, 4299],
        ['MacBook Pro 16" M3 Max', 'Space Gray', 1024, 3499],
        ['MacBook Pro 14" M3 Pro', 'Space Gray', 1024, 2199],
        ['MacBook Pro 16" M4 Pro', 'Silver', 512, 2599],
        ['MacBook Pro 16" M4 Max', 'Silver', 8192, 5199],
    ];
    foreach ($proCfg as [$model, $color, $storage, $list]) {
        $p = demoPrices((float) $list);
        $push([
            'model' => $model,
            'storage_capacity' => $storage,
            'color' => $color,
            'cost_price' => $p['cost'],
            'list_price' => $p['list'],
            'online_price' => $p['online'],
            'hero_image' => $I['laptop4'],
            'gallery_images' => galleryFrom($I, 'laptop4', 'laptop3', 'office'),
            'web_description' => "Pro-grade performance with Liquid Retina XDR, advanced thermal design, and studio-ready I/O. Ideal for video, code, and 3D. Each unit is wiped, battery-tested, and photographed before listing.",
        ]);
    }

    // —— iMac / Mac mini / Mac Studio (10) ——
    $desktopCfg = [
        ['iMac 24" M4', 'Blue', 512, 1549, $I['desk1']],
        ['iMac 24" M4', 'Silver', 1024, 1849, $I['desk2']],
        ['iMac 24" M3', 'Pink', 256, 1349, $I['desk1']],
        ['iMac 24" M3', 'Green', 512, 1549, $I['desk2']],
        ['Mac mini M4 Pro', 'Silver', 512, 1399, $I['minimal']],
        ['Mac mini M4', 'Silver', 256, 699, $I['minimal']],
        ['Mac mini M2 Pro', 'Silver', 1024, 1499, $I['desk1']],
        ['Mac Studio M4 Max', 'Silver', 1024, 2299, $I['desk2']],
        ['Mac Studio M2 Ultra', 'Silver', 2048, 4199, $I['desk2']],
        ['Mac Pro (tower) M2 Ultra', 'Silver', 4096, 7499, $I['office']],
    ];
    foreach ($desktopCfg as [$model, $color, $storage, $list, $hero]) {
        $p = demoPrices((float) $list);
        $push([
            'model' => $model,
            'storage_capacity' => $storage,
            'color' => $color,
            'cost_price' => $p['cost'],
            'list_price' => $p['list'],
            'online_price' => $p['online'],
            'hero_image' => $hero,
            'gallery_images' => galleryFrom($I, 'desk1', 'desk2', 'monitor1'),
            'web_description' => "Desktop Mac power for editors, developers, and small studios. Expandable connectivity and whisper-quiet operation under load. Demo listing includes power cable and factory restore — ask about trade-ins.",
        ]);
    }

    // —— Displays & peripherals (8) ——
    $accCfg = [
        ['Apple Studio Display 27"', 'Silver', 0, 1799, $I['monitor1'], '5K Retina reference monitor with True Tone, Thunderbolt hub, and six-speaker spatial audio. Excellent match for MacBook Pro docking setups.'],
        ['LG UltraFine 5K 27"', 'Black', 0, 1399, $I['monitor2'], 'Color-critical 5K panel with Thunderbolt 3 downstream charging for Mac laptops. Popular with designers who need P3 coverage and macOS HiDPI scaling.'],
        ['CalDigit TS4 Thunderbolt Dock', 'Silver', 0, 429, $I['minimal'], '18 ports including 2.5GbE, SD, DisplayPort, and 98W host charging. One-cable desk workflow for MacBook Pro users who run dual displays.'],
        ['Magic Keyboard with Touch ID', 'Black Keys', 0, 179, $I['keyboard'], 'Full-size layout with Touch ID sensor for fast unlock on Apple silicon Macs. Rechargeable via Lightning; pairs instantly out of the box.'],
        ['Magic Trackpad', 'White', 0, 149, $I['minimal'], 'Force Touch surface with Multi-Touch gestures — ideal alongside Magic Keyboard for editing timelines and spreadsheets.'],
        ['Magic Mouse', 'White', 0, 89, $I['mouse'], 'Compact rechargeable mouse with Multi-Touch surface for gestures. Lightweight travel companion for MacBook owners.'],
        ['AirPods Max', 'Space Gray', 0, 549, $I['headphones'], 'Premium over-ear headphones with Adaptive EQ and spatial audio on Apple Music. Great for focused work sessions and travel days.'],
        ['AirPods Pro (2nd gen)', 'White', 0, 249, $I['headphones'], 'Active noise cancellation and Transparency mode with USB-C case. Seamless switching across iPhone, iPad, and Mac signed into the same Apple ID.'],
    ];
    foreach ($accCfg as [$model, $color, $storage, $list, $hero, $desc]) {
        $p = demoPrices((float) $list, 0.05);
        $push([
            'model' => $model,
            'storage_capacity' => $storage,
            'color' => $color,
            'cost_price' => $p['cost'],
            'list_price' => $p['list'],
            'online_price' => $p['online'],
            'hero_image' => $hero,
            'gallery_images' => galleryFrom($I, 'keyboard', 'minimal', 'monitor2'),
            'web_description' => $desc,
            'quantity_on_hand' => random_int(4, 22),
        ]);
    }

    // Pad to 50 if loops changed — currently should be 16+12+10+8 = 46
    while (count($rows) < 50) {
        $idx = count($rows) + 1;
        $list = 899 + ($idx * 17);
        $p = demoPrices((float) $list);
        $push([
            'model' => 'MacBook Pro 14" M3 Pro (Open Box)',
            'storage_capacity' => 512,
            'color' => $idx % 2 === 0 ? 'Silver' : 'Space Gray',
            'cost_price' => $p['cost'],
            'list_price' => $p['list'],
            'online_price' => $p['online'],
            'hero_image' => $I['laptop3'],
            'gallery_images' => galleryFrom($I, 'laptop3', 'laptop1', 'office'),
            'web_description' => "Open-box savings on a current-gen MacBook Pro. Minor packaging wear only; machine passes full diagnostics and carries remainder of eligible coverage where applicable. SR Mac Shop sticker series #{$idx}.",
        ]);
    }

    return $rows;
}

$catalog = buildDemoCatalog($I);

$created = 0;
$skipped = 0;
foreach ($catalog as $row) {
    if ($repo->findBySku($row['sku'])) {
        $skipped++;

        continue;
    }
    $repo->create($row);
    $created++;
}

echo 'Demo catalog seed finished.' . PHP_EOL;
echo "Created: {$created}" . PHP_EOL;
echo "Skipped (already in DB): {$skipped}" . PHP_EOL;
echo 'SKUs use prefix DEMO-001 … DEMO-050 (safe to delete later by SKU pattern).' . PHP_EOL;
