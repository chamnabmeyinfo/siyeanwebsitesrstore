<?php

declare(strict_types=1);

use App\Database;
use App\InventoryRepository;
use App\ReportService;
use App\SaleService;
use App\UserRepository;
use App\BookingRepository;
use App\NotificationService;

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

$GLOBALS['user_repo'] = $users;
$GLOBALS['booking_repo'] = $bookingsRepo;
$GLOBALS['notification_service'] = $notifications;

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

if (!isPublicRoute($method, $requestPath) && !currentUser()) {
    redirect('/login');
}

switch (true) {
    case $method === 'GET' && $requestPath === '/login':
        if (currentUser()) {
            redirect('/');
        }
        render('auth_login.php', ['layout' => 'auth']);
        break;

    case $method === 'POST' && $requestPath === '/login':
        handleLogin();
        break;

    case $method === 'POST' && $requestPath === '/logout':
        requireAuth();
        handleLogout();
        break;

    case $method === 'GET' && $requestPath === '/':
        requireAuth();
        render('dashboard.php', [
            'summary' => $report->summary(),
            'inventory' => array_slice($report->inventorySnapshot(), 0, 5),
        ]);
        break;

    case $method === 'GET' && $requestPath === '/inventory':
        requireAuth();
        render('inventory.php', ['items' => $inventory->all()]);
        break;

    case $method === 'GET' && $requestPath === '/inventory/new':
        requireRole();
        render('inventory_form.php', ['mode' => 'create', 'item' => null]);
        break;

    case $method === 'GET' && $requestPath === '/inventory/edit':
        requireRole();
        $sku = trim($_GET['sku'] ?? '');
        $record = $inventory->findBySku($sku);
        if (!$record) {
            setFlash('error', 'Inventory item not found.');
            redirect('/inventory');
        }
        render('inventory_form.php', ['mode' => 'edit', 'item' => $record]);
        break;

    case $method === 'POST' && $requestPath === '/inventory/store':
        requireRole();
        handleInventoryCreate($inventory);
        break;

    case $method === 'POST' && $requestPath === '/inventory/update':
        requireRole();
        handleInventoryUpdate($inventory);
        break;

    case $method === 'POST' && $requestPath === '/inventory/delete':
        requireRole(['admin']);
        handleInventoryDelete($inventory);
        break;

    case $method === 'POST' && $requestPath === '/inventory/adjust':
        requireRole();
        handleInventoryAdjust($inventory);
        break;

    case $method === 'GET' && $requestPath === '/inventory/import':
        requireRole(['admin']);
        render('inventory_import.php');
        break;

    case $method === 'POST' && $requestPath === '/inventory/import':
        requireRole(['admin']);
        handleInventoryImport($inventory);
        break;

    case $method === 'GET' && $requestPath === '/inventory/export':
        requireRole(['admin']);
        exportInventoryCsv($inventory);
        break;

    case $method === 'GET' && $requestPath === '/sales':
        requireAuth();
        render('sales.php', [
            'summary' => $report->summary(),
            'rows' => $sales->sales(),
        ]);
        break;

    case $method === 'GET' && $requestPath === '/sales/new':
        requireRole();
        render('sale_form.php', ['items' => $inventory->all()]);
        break;

    case $method === 'POST' && $requestPath === '/sales/create':
        requireRole();
        handleSaleCreate($sales);
        break;

    case $method === 'GET' && $requestPath === '/bookings':
        requireRole();
        render('bookings.php', ['bookings' => bookingRepository()->findAll(), 'layout' => 'admin']);
        break;

    case $method === 'POST' && $requestPath === '/bookings/status':
        requireRole();
        handleBookingStatus();
        break;

    case $method === 'GET' && $requestPath === '/store':
        render('store.php', ['items' => $inventory->all(true), 'layout' => 'store']);
        break;

    case $method === 'GET' && str_starts_with($requestPath, '/store/product'):
        handleStoreProduct($inventory);
        break;

    case $method === 'POST' && $requestPath === '/store/book':
        handleStoreBooking($inventory);
        break;

    default:
        http_response_code(404);
        render('404.php', ['layout' => 'store']);
        break;
}

function basePath(string $template): string
{
    return __DIR__ . '/../templates/' . $template;
}

function render(string $template, array $data = []): void
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    $view = basePath($template);
    $layout = $data['layout'] ?? 'admin';
    unset($data['layout']);
    $currentUser = currentUser();
    extract($data, EXTR_SKIP);
    $layoutFile = match ($layout) {
        'store' => 'layout_store.php',
        'auth' => 'layout_auth.php',
        default => 'layout.php',
    };
    include basePath($layoutFile);
}

function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}

function setFlash(string $status, string $message): void
{
    $_SESSION['flash'] = compact('status', 'message');
}

function userRepository(): UserRepository
{
    return $GLOBALS['user_repo'];
}

function bookingRepository(): BookingRepository
{
    return $GLOBALS['booking_repo'];
}

function notificationService(): NotificationService
{
    return $GLOBALS['notification_service'];
}

function currentUser(): ?array
{
    static $cached;
    if ($cached !== null) {
        return $cached;
    }
    if (!isset($_SESSION['user_id'])) {
        return $cached = null;
    }
    $cached = userRepository()->findById((int) $_SESSION['user_id']);
    return $cached ?: null;
}

function requireAuth(): void
{
    if (!currentUser()) {
        redirect('/login');
    }
}

function requireRole(array $roles = []): void
{
    $user = currentUser();
    if (!$user) {
        redirect('/login');
    }
    if ($roles && !in_array($user['role'], $roles, true)) {
        setFlash('error', 'You are not authorized to perform this action.');
        redirect('/');
    }
}

function isPublicRoute(string $method, string $path): bool
{
    if ($path === '/login' && in_array($method, ['GET', 'POST'], true)) {
        return true;
    }
    if (str_starts_with($path, '/store')) {
        return true;
    }
    return false;
}

function handleLogin(): void
{
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        setFlash('error', 'Please provide your email and password.');
        redirect('/login');
    }

    $user = userRepository()->findByEmail($email);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        setFlash('error', 'Invalid credentials.');
        redirect('/login');
    }

    $_SESSION['user_id'] = $user['id'];
    session_regenerate_id(true);
    setFlash('success', 'Welcome back!');
    redirect('/');
}

function handleLogout(): void
{
    unset($_SESSION['user_id']);
    session_regenerate_id(true);
    setFlash('success', 'Signed out successfully.');
    redirect('/login');
}

function handleInventoryCreate(InventoryRepository $inventory): void
{
    $payload = inventoryPayloadFromRequest();

    try {
        $inventory->create($payload);
        setFlash('success', 'Inventory item added.');
    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    redirect('/inventory');
}

function handleInventoryUpdate(InventoryRepository $inventory): void
{
    $originalSku = trim($_POST['original_sku'] ?? '');
    if ($originalSku === '') {
        setFlash('error', 'Missing original SKU.');
        redirect('/inventory');
    }

    $payload = inventoryPayloadFromRequest();

    try {
        $inventory->update($originalSku, $payload);
        setFlash('success', 'Inventory item updated.');
    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    redirect('/inventory');
}

function handleInventoryDelete(InventoryRepository $inventory): void
{
    $sku = trim($_POST['sku'] ?? '');
    if ($sku === '') {
        setFlash('error', 'Missing SKU.');
        redirect('/inventory');
    }

    try {
        $inventory->delete($sku);
        setFlash('success', "Deleted {$sku}.");
    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    redirect('/inventory');
}

function handleInventoryImport(InventoryRepository $inventory): void
{
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        setFlash('error', 'Please upload a valid CSV file.');
        redirect('/inventory/import');
    }

    $tmp = $_FILES['csv']['tmp_name'];
    $handle = fopen($tmp, 'r');
    if (!$handle) {
        setFlash('error', 'Unable to read uploaded file.');
        redirect('/inventory/import');
    }

    $headers = fgetcsv($handle) ?: [];
    $normalized = array_map(fn ($h) => strtolower(trim((string) $h)), $headers);
    $required = ['sku', 'model', 'storage_capacity', 'color', 'cost_price', 'list_price', 'quantity_on_hand'];

    foreach ($required as $field) {
        if (!in_array($field, $normalized, true)) {
            fclose($handle);
            setFlash('error', "Missing required column: {$field}");
            redirect('/inventory/import');
        }
    }

    $created = 0;
    $updated = 0;
    while (($row = fgetcsv($handle)) !== false) {
        $data = [];
        foreach ($normalized as $index => $column) {
            $data[$column] = $row[$index] ?? null;
        }

        $payload = [
            'sku' => trim($data['sku'] ?? ''),
            'slug' => trim($data['slug'] ?? ''),
            'model' => trim($data['model'] ?? ''),
            'storage_capacity' => (int) ($data['storage_capacity'] ?? 0),
            'color' => trim($data['color'] ?? ''),
            'cost_price' => (float) ($data['cost_price'] ?? 0),
            'list_price' => (float) ($data['list_price'] ?? 0),
            'online_price' => isset($data['online_price']) ? (float) $data['online_price'] : null,
            'quantity_on_hand' => (int) ($data['quantity_on_hand'] ?? 0),
            'hero_image' => trim($data['hero_image'] ?? '') ?: null,
            'gallery_images' => $data['gallery_images'] ?? null,
            'web_description' => $data['web_description'] ?? null,
            'visible_online' => isset($data['visible_online']) ? (int) $data['visible_online'] : 1,
        ];

        if ($payload['sku'] === '' || $payload['model'] === '') {
            continue;
        }

        try {
            if ($inventory->findBySku($payload['sku'])) {
                $inventory->update($payload['sku'], $payload);
                $updated++;
            } else {
                $inventory->create($payload);
                $created++;
            }
        } catch (Throwable $e) {
            continue;
        }
    }

    fclose($handle);
    setFlash('success', "Import complete: {$created} added, {$updated} updated.");
    redirect('/inventory');
}

function exportInventoryCsv(InventoryRepository $inventory): void
{
    $rows = $inventory->all();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="inventory.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, [
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
    ]);

    foreach ($rows as $row) {
        fputcsv($out, [
            $row['sku'],
            $row['slug'],
            $row['model'],
            $row['storage_capacity'],
            $row['color'],
            $row['cost_price'],
            $row['list_price'],
            $row['online_price'],
            $row['quantity_on_hand'],
            $row['hero_image'],
            $row['gallery_images'],
            $row['web_description'],
            $row['visible_online'] ?? 1,
        ]);
    }

    fclose($out);
    exit;
}

function handleInventoryAdjust(InventoryRepository $inventory): void
{
    $sku = trim($_POST['sku'] ?? '');
    $delta = (int) ($_POST['delta'] ?? 0);

    try {
        $inventory->adjustQuantity($sku, $delta);
        setFlash('success', 'Inventory updated.');
        $updated = $inventory->findBySku($sku);
        if ($updated) {
            notificationService()->maybeNotifyLowStock($updated);
        }
    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    redirect('/inventory');
}

function handleSaleCreate(SaleService $sales): void
{
    global $inventory;
    $payload = [
        'sku' => trim($_POST['sku'] ?? ''),
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'customer_email' => trim($_POST['customer_email'] ?? ''),
        'customer_phone' => trim($_POST['customer_phone'] ?? ''),
        'quantity' => (int) ($_POST['quantity'] ?? 1),
        'unit_price' => (float) ($_POST['unit_price'] ?? 0),
        'discount' => (float) ($_POST['discount'] ?? 0),
        'tax_rate' => (float) ($_POST['tax_rate'] ?? 0),
        'payment_method' => trim($_POST['payment_method'] ?? 'cash'),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    try {
        $sales->record($payload);
        setFlash('success', 'Sale recorded.');
        if ($inventory) {
            $item = $inventory->findBySku($payload['sku']);
            if ($item) {
                notificationService()->maybeNotifyLowStock($item);
            }
        }
    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    redirect('/sales');
}

function inventoryPayloadFromRequest(): array
{
    $rawSlug = trim($_POST['slug'] ?? '');
    if ($rawSlug === '') {
        $rawSlug = trim($_POST['model'] ?? ($_POST['sku'] ?? ''));
    }
    $slug = slugify($rawSlug ?: ($_POST['sku'] ?? ''));

    return [
        'sku' => trim($_POST['sku'] ?? ''),
        'slug' => $slug,
        'model' => trim($_POST['model'] ?? ''),
        'storage_capacity' => (int) ($_POST['storage_capacity'] ?? 0),
        'color' => trim($_POST['color'] ?? ''),
        'cost_price' => (float) ($_POST['cost_price'] ?? 0),
        'list_price' => (float) ($_POST['list_price'] ?? 0),
        'online_price' => isset($_POST['online_price']) ? (float) $_POST['online_price'] : null,
        'quantity_on_hand' => (int) ($_POST['quantity'] ?? 0),
        'hero_image' => trim($_POST['hero_image'] ?? '') ?: null,
        'gallery_images' => $_POST['gallery_images'] ?? null,
        'web_description' => $_POST['web_description'] ?? null,
        'visible_online' => isset($_POST['visible_online']) ? (int) $_POST['visible_online'] : 1,
    ];
}

function handleBookingStatus(): void
{
    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $allowed = ['pending', 'confirmed', 'picked_up', 'cancelled'];

    if ($bookingId <= 0 || !in_array($status, $allowed, true)) {
        setFlash('error', 'Invalid booking or status.');
        redirect('/bookings');
    }

    bookingRepository()->updateStatus($bookingId, $status);
    $updated = bookingRepository()->findById($bookingId);
    if ($updated) {
        $updated['status'] = $status;
        notificationService()->notifyBookingStatus($updated);
    }
    setFlash('success', 'Booking updated.');
    redirect('/bookings');
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value, '-');
}

function handleStoreProduct(InventoryRepository $inventory): void
{
    $slug = trim($_GET['slug'] ?? '');
    if ($slug === '') {
        redirect('/store');
    }
    $stmt = Database::connection()->prepare('SELECT * FROM inventory WHERE slug = :slug AND visible_online = 1');
    $stmt->execute([':slug' => $slug]);
    $product = $stmt->fetch();
    if (!$product) {
        http_response_code(404);
        render('404.php', ['layout' => 'store']);
        return;
    }
    render('store_product.php', ['product' => $product, 'layout' => 'store']);
}

function handleStoreBooking(InventoryRepository $inventory): void
{
    $payload = [
        'inventory_id' => (int) ($_POST['inventory_id'] ?? 0),
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'customer_email' => trim($_POST['customer_email'] ?? ''),
        'customer_phone' => trim($_POST['customer_phone'] ?? ''),
        'quantity' => max(1, (int) ($_POST['quantity'] ?? 1)),
        'deposit_amount' => (float) ($_POST['deposit_amount'] ?? 0),
        'preferred_date' => trim($_POST['preferred_date'] ?? ''),
        'preferred_time' => trim($_POST['preferred_time'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    if (
        $payload['inventory_id'] <= 0 ||
        $payload['customer_name'] === '' ||
        $payload['customer_email'] === '' ||
        $payload['customer_phone'] === ''
    ) {
        setFlash('error', 'Please complete all required fields.');
        redirect('/store');
    }

    $stmt = Database::connection()->prepare('SELECT id, sku, slug, quantity_on_hand FROM inventory WHERE id = :id AND visible_online = 1');
    $stmt->execute([':id' => $payload['inventory_id']]);
    $product = $stmt->fetch();
    if (!$product) {
        setFlash('error', 'Item not found or unavailable.');
        redirect('/store');
    }

    if ($payload['quantity'] > (int) $product['quantity_on_hand']) {
        setFlash('error', 'Requested quantity exceeds availability.');
        redirect('/store/product?slug=' . urlencode($product['slug'] ?: strtolower($product['sku'])));
    }

    $payload['status'] = 'pending';
    $payload['deposit_amount'] = max(0, $payload['deposit_amount']);
    $payload['preferred_date'] = $payload['preferred_date'] ?: null;
    $payload['preferred_time'] = $payload['preferred_time'] ?: null;
    $payload['notes'] = $payload['notes'] ?: null;

    $bookingId = bookingRepository()->create($payload);
    $booking = bookingRepository()->findById($bookingId);
    if ($booking) {
        notificationService()->notifyBookingCreated($booking, $product);
    }

    setFlash('success', 'Your booking request has been received. We will confirm shortly.');
    redirect('/store/product?slug=' . urlencode($product['slug'] ?: strtolower($product['sku'])));
}
