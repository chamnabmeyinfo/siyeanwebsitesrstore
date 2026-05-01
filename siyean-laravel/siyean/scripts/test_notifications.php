<?php

declare(strict_types=1);

use App\NotificationService;

require __DIR__ . '/../vendor/autoload.php';

$defaultConfig = require __DIR__ . '/../config/app.example.php';
$customConfigPath = __DIR__ . '/../config/app.php';
$appConfig = $defaultConfig;
if (file_exists($customConfigPath)) {
    $custom = require $customConfigPath;
    $appConfig = array_replace_recursive($defaultConfig, $custom);
}

$notifications = new NotificationService($appConfig['notifications'] ?? []);

$bookingSample = [
    'id' => 0,
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '+10000000000',
    'quantity' => 1,
    'deposit_amount' => 100,
    'preferred_date' => date('Y-m-d', strtotime('+1 day')),
    'preferred_time' => '12:00',
    'status' => 'pending',
    'notes' => 'Sample booking generated from test script.',
];

$productSample = [
    'sku' => 'TEST-SKU',
    'model' => 'Demo Product',
];

$notifications->notifyBookingCreated($bookingSample, $productSample);

echo "Test notification dispatched. Check your configured channels.\n";

