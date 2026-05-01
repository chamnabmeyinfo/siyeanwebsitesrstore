<?php

declare(strict_types=1);

use App\Database;
use App\BookingRepository;
use App\Http\AuthGate;
use App\Http\HttpKernel;
use App\Http\ViewRenderer;
use App\Http\WebContainer;
use App\InventoryRepository;
use App\NotificationService;
use App\ReportService;
use App\SaleService;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

session_start();

Database::migrate();
$db = Database::connection();

$inventory = new InventoryRepository($db);
$sales = new SaleService($db, $inventory);
$report = new ReportService($db, $sales);
$users = new UserRepository($db);
$bookingsRepo = new BookingRepository($db);

$defaultConfig = require __DIR__ . '/../config/app.example.php';
$customConfigPath = __DIR__ . '/../config/app.php';
$appConfig = $defaultConfig;
if (file_exists($customConfigPath)) {
    $custom = require $customConfigPath;
    $appConfig = array_replace_recursive($defaultConfig, $custom);
}
$notifications = new NotificationService($appConfig['notifications'] ?? []);

$container = new WebContainer($inventory, $sales, $report, $users, $bookingsRepo, $notifications);
$auth = new AuthGate($users);
$view = new ViewRenderer($auth);
$kernel = new HttpKernel($container, $auth, $view);

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$kernel->dispatch($method, $requestPath);
