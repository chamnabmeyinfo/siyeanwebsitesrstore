<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StoreMenuItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

final class ImportLegacyData extends Command
{
    protected $signature = 'legacy:import {--db= : Path to SQLite pos.db (defaults to siyean/storage/pos.db)} {--fresh : Truncate target tables first}';

    protected $description = 'Import inventory, customers, sales, bookings, and store menu items from the legacy SQLite POS database into MySQL.';

    public function handle(): int
    {
        $path = $this->option('db') ?: base_path('siyean/storage/pos.db');
        if (!is_file($path)) {
            $this->error("SQLite DB not found: {$path}");
            return self::FAILURE;
        }

        $sqlite = new PDO('sqlite:' . $path);
        $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sqlite->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if ($this->option('fresh')) {
            $this->warn('Truncating target tables...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (['bookings', 'sales', 'customers', 'products', 'store_menu_items'] as $t) {
                DB::table($t)->truncate();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $invMap = $this->importInventory($sqlite);
        $custMap = $this->importCustomers($sqlite);
        $saleMap = $this->importSales($sqlite, $invMap, $custMap);
        $this->importBookings($sqlite, $invMap, $saleMap);
        $this->importStoreMenu($sqlite);

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function importInventory(PDO $sqlite): array
    {
        $rows = $sqlite->query('SELECT * FROM inventory')->fetchAll();
        $map = [];
        $bar = $this->output->createProgressBar(count($rows));
        $this->line('Products:');
        foreach ($rows as $row) {
            $gallery = null;
            if (!empty($row['gallery_images'])) {
                $decoded = json_decode((string) $row['gallery_images'], true);
                if (is_array($decoded)) {
                    $gallery = array_values(array_filter(array_map('trim', $decoded)));
                } else {
                    $parts = preg_split('/[\n,]+/', (string) $row['gallery_images']) ?: [];
                    $gallery = array_values(array_filter(array_map('trim', $parts)));
                }
                if (!$gallery) $gallery = null;
            }

            $product = Product::updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'slug' => $row['slug'] ?: strtolower((string) $row['sku']),
                    'model' => (string) $row['model'],
                    'storage_capacity' => (int) ($row['storage_capacity'] ?? 0),
                    'color' => (string) ($row['color'] ?? ''),
                    'cost_price' => (float) ($row['cost_price'] ?? 0),
                    'list_price' => (float) ($row['list_price'] ?? 0),
                    'online_price' => isset($row['online_price']) && $row['online_price'] !== '' ? (float) $row['online_price'] : null,
                    'quantity_on_hand' => (int) ($row['quantity_on_hand'] ?? 0),
                    'hero_image' => $row['hero_image'] ?: null,
                    'gallery_images' => $gallery,
                    'web_description' => $row['web_description'] ?: null,
                    'visible_online' => (int) ($row['visible_online'] ?? 1) === 1,
                ]
            );
            $map[(int) $row['id']] = $product->id;
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        return $map;
    }

    private function importCustomers(PDO $sqlite): array
    {
        $rows = $sqlite->query('SELECT * FROM customers')->fetchAll();
        $map = [];
        $this->line('Customers: ' . count($rows));
        foreach ($rows as $row) {
            $customer = Customer::create([
                'name' => (string) $row['name'],
                'email' => $row['email'] ?: null,
                'phone' => $row['phone'] ?: null,
            ]);
            $map[(int) $row['id']] = $customer->id;
        }
        return $map;
    }

    private function importSales(PDO $sqlite, array $invMap, array $custMap): array
    {
        $rows = $sqlite->query('SELECT * FROM sales')->fetchAll();
        $map = [];
        $this->line('Sales: ' . count($rows));
        foreach ($rows as $row) {
            $productId = $invMap[(int) $row['inventory_id']] ?? null;
            $customerId = $custMap[(int) $row['customer_id']] ?? null;
            if (!$productId || !$customerId) {
                $this->warn("Skipping sale #{$row['id']}: missing product/customer.");
                continue;
            }
            $sale = Sale::create([
                'product_id' => $productId,
                'customer_id' => $customerId,
                'quantity' => (int) $row['quantity'],
                'unit_price' => (float) $row['unit_price'],
                'discount' => (float) ($row['discount'] ?? 0),
                'tax_rate' => (float) ($row['tax_rate'] ?? 0),
                'payment_method' => (string) ($row['payment_method'] ?? 'cash'),
                'notes' => $row['notes'] ?: null,
                'sold_at' => $row['sold_at'] ?? now(),
            ]);
            $map[(int) $row['id']] = $sale->id;
        }
        return $map;
    }

    private function importBookings(PDO $sqlite, array $invMap, array $saleMap): void
    {
        $rows = $sqlite->query('SELECT * FROM bookings')->fetchAll();
        $this->line('Bookings: ' . count($rows));
        foreach ($rows as $row) {
            $productId = $invMap[(int) $row['inventory_id']] ?? null;
            if (!$productId) {
                $this->warn("Skipping booking #{$row['id']}: missing product.");
                continue;
            }
            Booking::create([
                'product_id' => $productId,
                'customer_name' => (string) $row['customer_name'],
                'customer_email' => (string) $row['customer_email'],
                'customer_phone' => (string) $row['customer_phone'],
                'quantity' => (int) ($row['quantity'] ?? 1),
                'deposit_amount' => (float) ($row['deposit_amount'] ?? 0),
                'preferred_date' => $row['preferred_date'] ?: null,
                'preferred_time' => $row['preferred_time'] ?: null,
                'status' => (string) ($row['status'] ?? 'pending'),
                'notes' => $row['notes'] ?: null,
                'converted_sale_id' => isset($row['converted_sale_id']) ? ($saleMap[(int) $row['converted_sale_id']] ?? null) : null,
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['created_at'] ?? now(),
            ]);
        }
    }

    private function importStoreMenu(PDO $sqlite): void
    {
        $rows = $sqlite->query('SELECT * FROM store_menu_items')->fetchAll();
        $this->line('Store menu items: ' . count($rows));
        foreach ($rows as $row) {
            StoreMenuItem::updateOrCreate(
                ['label' => (string) $row['label'], 'href' => (string) $row['href']],
                [
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'is_active' => (int) ($row['is_active'] ?? 1) === 1,
                ]
            );
        }
    }
}
