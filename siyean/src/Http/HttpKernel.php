<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Form\InventoryFormMapper;
use App\Http\Form\SaleFormMapper;
use Throwable;

final class HttpKernel
{
    public function __construct(
        private readonly WebContainer $app,
        private readonly AuthGate $auth,
        private readonly ViewRenderer $view,
    ) {
    }

    public function dispatch(string $method, string $requestPath): void
    {
        $this->view->setRequestPath($requestPath);

        if (!$this->auth->isPublicRoute($method, $requestPath) && !$this->auth->user()) {
            $this->view->redirect('/login');
        }

        switch (true) {
            case $method === 'GET' && $requestPath === '/login':
                if ($this->auth->user()) {
                    $this->view->redirect('/');
                }
                $this->view->render('auth_login.php', ['layout' => 'auth']);
                break;

            case $method === 'POST' && $requestPath === '/login':
                $this->handleLoginPost();
                break;

            case $method === 'POST' && $requestPath === '/logout':
                $this->auth->requireAuth($this->view);
                $this->handleLogoutPost();
                break;

            case $method === 'GET' && $requestPath === '/':
                $this->auth->requireAuth($this->view);
                $dashboard = DashboardViewModel::build(
                    $this->app->report->summary(),
                    array_slice($this->app->report->inventorySnapshot(), 0, 5)
                );
                $this->view->render('dashboard.php', $dashboard);
                break;

            case $method === 'GET' && $requestPath === '/inventory':
                $this->auth->requireAuth($this->view);
                $this->view->render('inventory.php', ['items' => $this->app->inventory->all()]);
                break;

            case $method === 'GET' && $requestPath === '/inventory/new':
                $this->auth->requireRole($this->view);
                $this->view->render('inventory_form.php', ['mode' => 'create', 'item' => null]);
                break;

            case $method === 'GET' && $requestPath === '/inventory/edit':
                $this->auth->requireRole($this->view);
                $sku = trim($_GET['sku'] ?? '');
                $record = $this->app->inventory->findBySku($sku);
                if (!$record) {
                    $this->view->flash('error', 'Inventory item not found.');
                    $this->view->redirect('/inventory');
                }
                $this->view->render('inventory_form.php', ['mode' => 'edit', 'item' => $record]);
                break;

            case $method === 'POST' && $requestPath === '/inventory/store':
                $this->auth->requireRole($this->view);
                $this->handleInventoryCreate();
                break;

            case $method === 'POST' && $requestPath === '/inventory/update':
                $this->auth->requireRole($this->view);
                $this->handleInventoryUpdate();
                break;

            case $method === 'POST' && $requestPath === '/inventory/delete':
                $this->auth->requireRole($this->view, ['admin']);
                $this->handleInventoryDelete();
                break;

            case $method === 'POST' && $requestPath === '/inventory/adjust':
                $this->auth->requireRole($this->view);
                $this->handleInventoryAdjust();
                break;

            case $method === 'GET' && $requestPath === '/inventory/import':
                $this->auth->requireRole($this->view, ['admin']);
                $this->view->render('inventory_import.php');
                break;

            case $method === 'POST' && $requestPath === '/inventory/import':
                $this->auth->requireRole($this->view, ['admin']);
                $this->handleInventoryImport();
                break;

            case $method === 'GET' && $requestPath === '/inventory/export':
                $this->auth->requireRole($this->view, ['admin']);
                $this->exportInventoryCsv();
                break;

            case $method === 'GET' && $requestPath === '/sales':
                $this->auth->requireAuth($this->view);
                $this->view->render('sales.php', [
                    'summary' => $this->app->report->summary(),
                    'rows' => $this->app->sales->sales(),
                ]);
                break;

            case $method === 'GET' && $requestPath === '/sales/new':
                $this->auth->requireRole($this->view);
                $this->view->render('sale_form.php', ['items' => $this->app->inventory->all()]);
                break;

            case $method === 'POST' && $requestPath === '/sales/create':
                $this->auth->requireRole($this->view);
                $this->handleSaleCreate();
                break;

            case $method === 'GET' && $requestPath === '/bookings':
                $this->auth->requireRole($this->view);
                $this->view->render('bookings.php', [
                    'bookings' => $this->app->bookings->findAll(),
                    'layout' => 'admin',
                ]);
                break;

            case $method === 'POST' && $requestPath === '/bookings/status':
                $this->auth->requireRole($this->view);
                $this->handleBookingStatus();
                break;

            case $method === 'GET' && $requestPath === '/store':
                $this->view->render('store.php', ['items' => $this->app->inventory->all(true), 'layout' => 'store']);
                break;

            case $method === 'GET' && str_starts_with($requestPath, '/store/product'):
                $this->handleStoreProduct();
                break;

            case $method === 'POST' && $requestPath === '/store/book':
                $this->handleStoreBooking();
                break;

            default:
                http_response_code(404);
                $this->view->render('404.php', ['layout' => 'store']);
                break;
        }
    }

    private function handleLoginPost(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->view->flash('error', 'Please provide your email and password.');
            $this->view->redirect('/login');
        }

        $user = $this->app->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->view->flash('error', 'Invalid credentials.');
            $this->view->redirect('/login');
        }

        $_SESSION['user_id'] = $user['id'];
        session_regenerate_id(true);
        $this->view->flash('success', 'Welcome back!');
        $this->view->redirect('/');
    }

    private function handleLogoutPost(): void
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);
        $this->view->flash('success', 'Signed out successfully.');
        $this->view->redirect('/login');
    }

    private function handleInventoryCreate(): void
    {
        $payload = InventoryFormMapper::fromPost($_POST);

        try {
            $this->app->inventory->create($payload);
            $this->view->flash('success', 'Inventory item added.');
        } catch (Throwable $e) {
            $this->view->flash('error', $e->getMessage());
        }

        $this->view->redirect('/inventory');
    }

    private function handleInventoryUpdate(): void
    {
        $originalSku = trim($_POST['original_sku'] ?? '');
        if ($originalSku === '') {
            $this->view->flash('error', 'Missing original SKU.');
            $this->view->redirect('/inventory');
        }

        $payload = InventoryFormMapper::fromPost($_POST);

        try {
            $this->app->inventory->update($originalSku, $payload);
            $this->view->flash('success', 'Inventory item updated.');
        } catch (Throwable $e) {
            $this->view->flash('error', $e->getMessage());
        }

        $this->view->redirect('/inventory');
    }

    private function handleInventoryDelete(): void
    {
        $sku = trim($_POST['sku'] ?? '');
        if ($sku === '') {
            $this->view->flash('error', 'Missing SKU.');
            $this->view->redirect('/inventory');
        }

        try {
            $this->app->inventory->delete($sku);
            $this->view->flash('success', "Deleted {$sku}.");
        } catch (Throwable $e) {
            $this->view->flash('error', $e->getMessage());
        }

        $this->view->redirect('/inventory');
    }

    private function handleInventoryImport(): void
    {
        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            $this->view->flash('error', 'Please upload a valid CSV file.');
            $this->view->redirect('/inventory/import');
        }

        $tmp = $_FILES['csv']['tmp_name'];
        $handle = fopen($tmp, 'r');
        if (!$handle) {
            $this->view->flash('error', 'Unable to read uploaded file.');
            $this->view->redirect('/inventory/import');
        }

        $headers = fgetcsv($handle) ?: [];
        $normalized = array_map(fn ($h) => strtolower(trim((string) $h)), $headers);
        $required = ['sku', 'model', 'storage_capacity', 'color', 'cost_price', 'list_price', 'quantity_on_hand'];

        foreach ($required as $field) {
            if (!in_array($field, $normalized, true)) {
                fclose($handle);
                $this->view->flash('error', "Missing required column: {$field}");
                $this->view->redirect('/inventory/import');
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
                if ($this->app->inventory->findBySku($payload['sku'])) {
                    $this->app->inventory->update($payload['sku'], $payload);
                    $updated++;
                } else {
                    $this->app->inventory->create($payload);
                    $created++;
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        fclose($handle);
        $this->view->flash('success', "Import complete: {$created} added, {$updated} updated.");
        $this->view->redirect('/inventory');
    }

    private function exportInventoryCsv(): void
    {
        $rows = $this->app->inventory->all();
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

    private function handleInventoryAdjust(): void
    {
        $sku = trim($_POST['sku'] ?? '');
        $delta = (int) ($_POST['delta'] ?? 0);

        try {
            $this->app->inventory->adjustQuantity($sku, $delta);
            $this->view->flash('success', 'Inventory updated.');
            $updated = $this->app->inventory->findBySku($sku);
            if ($updated) {
                $this->app->notifications->maybeNotifyLowStock($updated);
            }
        } catch (Throwable $e) {
            $this->view->flash('error', $e->getMessage());
        }

        $this->view->redirect('/inventory');
    }

    private function handleSaleCreate(): void
    {
        $payload = SaleFormMapper::fromPost($_POST);

        try {
            $this->app->sales->record($payload);
            $this->view->flash('success', 'Sale recorded.');
            $item = $this->app->inventory->findBySku($payload['sku']);
            if ($item) {
                $this->app->notifications->maybeNotifyLowStock($item);
            }
        } catch (Throwable $e) {
            $this->view->flash('error', $e->getMessage());
        }

        $this->view->redirect('/sales');
    }

    private function handleBookingStatus(): void
    {
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $allowed = ['pending', 'confirmed', 'picked_up', 'cancelled'];

        if ($bookingId <= 0 || !in_array($status, $allowed, true)) {
            $this->view->flash('error', 'Invalid booking or status.');
            $this->view->redirect('/bookings');
        }

        $this->app->bookings->updateStatus($bookingId, $status);
        $updated = $this->app->bookings->findById($bookingId);
        if ($updated) {
            $updated['status'] = $status;
            $this->app->notifications->notifyBookingStatus($updated);
        }
        $this->view->flash('success', 'Booking updated.');
        $this->view->redirect('/bookings');
    }

    private function handleStoreProduct(): void
    {
        $slug = trim($_GET['slug'] ?? '');
        if ($slug === '') {
            $this->view->redirect('/store');
        }
        $product = $this->app->inventory->findVisibleOnlineBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->view->render('404.php', ['layout' => 'store']);

            return;
        }
        $this->view->render('store_product.php', ['product' => $product, 'layout' => 'store']);
    }

    private function handleStoreBooking(): void
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
            $payload['inventory_id'] <= 0
            || $payload['customer_name'] === ''
            || $payload['customer_email'] === ''
            || $payload['customer_phone'] === ''
        ) {
            $this->view->flash('error', 'Please complete all required fields.');
            $this->view->redirect('/store');
        }

        $product = $this->app->inventory->findVisibleOnlineSummaryById($payload['inventory_id']);
        if (!$product) {
            $this->view->flash('error', 'Item not found or unavailable.');
            $this->view->redirect('/store');
        }

        if ($payload['quantity'] > (int) $product['quantity_on_hand']) {
            $this->view->flash('error', 'Requested quantity exceeds availability.');
            $slugForRedirect = $product['slug'] ?: strtolower((string) $product['sku']);
            $this->view->redirect('/store/product?slug=' . urlencode($slugForRedirect));
        }

        $payload['status'] = 'pending';
        $payload['deposit_amount'] = max(0, $payload['deposit_amount']);
        $payload['preferred_date'] = $payload['preferred_date'] ?: null;
        $payload['preferred_time'] = $payload['preferred_time'] ?: null;
        $payload['notes'] = $payload['notes'] ?: null;

        $bookingId = $this->app->bookings->create($payload);
        $booking = $this->app->bookings->findById($bookingId);
        if ($booking) {
            $this->app->notifications->notifyBookingCreated($booking, $product);
        }

        $this->view->flash('success', 'Your booking request has been received. We will confirm shortly.');
        $slugForRedirect = $product['slug'] ?: strtolower((string) $product['sku']);
        $this->view->redirect('/store/product?slug=' . urlencode($slugForRedirect));
    }
}
