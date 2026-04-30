<?php

declare(strict_types=1);

use App\BookingRepository;
use App\Database;
use App\InventoryRepository;
use App\SaleService;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

Database::migrate();
$db = Database::connection();
$inventoryRepo = new InventoryRepository($db);
$userRepo = new UserRepository($db);
$saleService = new SaleService($db, $inventoryRepo);
$bookingRepo = new BookingRepository($db);

$inventorySamples = [
    [
        'sku' => 'MBP14-2025-M4',
        'slug' => 'macbook-pro-14-m4-max',
        'model' => 'MacBook Pro 14" M4 Max',
        'storage_capacity' => 1024,
        'color' => 'Space Black',
        'cost_price' => 2899,
        'list_price' => 3499,
        'online_price' => 3399,
        'quantity_on_hand' => 8,
        'hero_image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1527443224154-d7cdb3c195b7?auto=format&w=1200&q=80',
        ],
        'web_description' => 'High-end M4 Max model with Liquid Retina XDR display and SR MAC warranty.',
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
        'quantity_on_hand' => 12,
        'hero_image' => 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1510519138101-570d1dca3d66?auto=format&w=1200&q=80',
        ],
        'web_description' => 'Portable daily-driver laptop with all-day battery and fast SSD.',
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
        'quantity_on_hand' => 5,
        'hero_image' => 'https://images.unsplash.com/photo-1481277542470-605612bd2d61?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1523475472560-d2df97ec485c?auto=format&w=1200&q=80',
            'https://images.unsplash.com/photo-1523475472560-e06502830871?auto=format&w=1200&q=80',
        ],
        'web_description' => 'Premium Windows workstation with OLED panel and RTX graphics.',
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
        'quantity_on_hand' => 9,
        'hero_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&w=1600&q=80',
        'gallery_images' => [
            'https://images.unsplash.com/photo-1457305237443-44c3d5a30b89?auto=format&w=1200&q=80',
        ],
        'web_description' => 'Color-accurate 4K display for editing, coding, and sales floor demos.',
        'visible_online' => 1,
    ],
];

$createdInventory = 0;
$updatedInventory = 0;
foreach ($inventorySamples as $sample) {
    $existing = $inventoryRepo->findBySku($sample['sku']);
    if ($existing) {
        $inventoryRepo->update($sample['sku'], $sample);
        $updatedInventory++;
        continue;
    }
    $inventoryRepo->create($sample);
    $createdInventory++;
}

$demoUsers = [
    ['name' => 'Demo Admin', 'email' => 'demo-admin@srmacshop.com', 'password' => 'DemoAdmin@123', 'role' => 'admin'],
    ['name' => 'Demo Clerk', 'email' => 'demo-clerk@srmacshop.com', 'password' => 'DemoClerk@123', 'role' => 'clerk'],
];

$createdUsers = 0;
foreach ($demoUsers as $user) {
    if ($userRepo->findByEmail($user['email'])) {
        continue;
    }
    $userRepo->create($user['name'], $user['email'], $user['password'], $user['role']);
    $createdUsers++;
}

$existingDemoSales = (int) $db->query("SELECT COUNT(*) FROM sales WHERE notes LIKE 'DEMO-SALE-%'")->fetchColumn();
if ($existingDemoSales === 0) {
    $saleRows = [
        [
            'sku' => 'MBP14-2025-M4',
            'customer_name' => 'Sok Dara',
            'customer_email' => 'sok.dara@example.com',
            'customer_phone' => '+85512900111',
            'quantity' => 1,
            'unit_price' => 3499,
            'discount' => 150,
            'tax_rate' => 10,
            'payment_method' => 'card',
            'notes' => 'DEMO-SALE-001',
        ],
        [
            'sku' => 'MBA13-2024-M3',
            'customer_name' => 'Chan Rina',
            'customer_email' => 'chan.rina@example.com',
            'customer_phone' => '+85510900444',
            'quantity' => 2,
            'unit_price' => 1499,
            'discount' => 100,
            'tax_rate' => 10,
            'payment_method' => 'bank_transfer',
            'notes' => 'DEMO-SALE-002',
        ],
    ];

    foreach ($saleRows as $row) {
        $saleService->record($row);
    }
}

$existingDemoBookings = (int) $db->query("SELECT COUNT(*) FROM bookings WHERE notes LIKE 'DEMO-BOOKING-%'")->fetchColumn();
if ($existingDemoBookings === 0) {
    $inventoryId = static function (string $sku) use ($db): int {
        $stmt = $db->prepare('SELECT id FROM inventory WHERE sku = :sku LIMIT 1');
        $stmt->execute([':sku' => $sku]);
        return (int) ($stmt->fetchColumn() ?: 0);
    };

    $bookingRows = [
        [
            'inventory_id' => $inventoryId('DELL-XPS15-2025'),
            'customer_name' => 'Nita Srey',
            'customer_email' => 'nita.srey@example.com',
            'customer_phone' => '+85588977123',
            'quantity' => 1,
            'deposit_amount' => 300,
            'preferred_date' => date('Y-m-d', strtotime('+2 days')),
            'preferred_time' => '10:30',
            'status' => 'pending',
            'notes' => 'DEMO-BOOKING-001',
        ],
        [
            'inventory_id' => $inventoryId('MBA13-2024-M3'),
            'customer_name' => 'Vannak Lim',
            'customer_email' => 'vannak.lim@example.com',
            'customer_phone' => '+85595999111',
            'quantity' => 1,
            'deposit_amount' => 200,
            'preferred_date' => date('Y-m-d', strtotime('+3 days')),
            'preferred_time' => '14:00',
            'status' => 'confirmed',
            'notes' => 'DEMO-BOOKING-002',
        ],
    ];

    foreach ($bookingRows as $booking) {
        if ($booking['inventory_id'] <= 0) {
            continue;
        }
        $bookingRepo->create($booking);
    }
}

$inventoryCount = (int) $db->query('SELECT COUNT(*) FROM inventory')->fetchColumn();
$customerCount = (int) $db->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$salesCount = (int) $db->query('SELECT COUNT(*) FROM sales')->fetchColumn();
$bookingCount = (int) $db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$userCount = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();

echo 'Demo seed complete.' . PHP_EOL;
echo "Inventory: {$inventoryCount} (created {$createdInventory}, updated {$updatedInventory})" . PHP_EOL;
echo "Users: {$userCount} (new demo users {$createdUsers})" . PHP_EOL;
echo "Customers: {$customerCount}" . PHP_EOL;
echo "Sales: {$salesCount}" . PHP_EOL;
echo "Bookings: {$bookingCount}" . PHP_EOL;
echo 'Demo login accounts:' . PHP_EOL;
echo '- demo-admin@srmacshop.com / DemoAdmin@123' . PHP_EOL;
echo '- demo-clerk@srmacshop.com / DemoClerk@123' . PHP_EOL;
