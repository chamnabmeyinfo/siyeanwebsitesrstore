<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    private const DB_FILE = __DIR__ . '/../storage/pos.db';

    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $dsn = 'sqlite:' . self::DB_FILE;
            self::$connection = new PDO($dsn);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$connection;
    }

    public static function migrate(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS inventory (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sku TEXT UNIQUE NOT NULL,
            slug TEXT UNIQUE,
            model TEXT NOT NULL,
            storage_capacity INTEGER NOT NULL,
            color TEXT NOT NULL,
            cost_price REAL NOT NULL,
            list_price REAL NOT NULL,
            online_price REAL,
            quantity_on_hand INTEGER NOT NULL DEFAULT 0,
            hero_image TEXT,
            gallery_images TEXT,
            web_description TEXT,
            visible_online INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT,
            phone TEXT
        );

        CREATE TABLE IF NOT EXISTS sales (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            inventory_id INTEGER NOT NULL,
            customer_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            unit_price REAL NOT NULL,
            discount REAL NOT NULL DEFAULT 0,
            tax_rate REAL NOT NULL DEFAULT 0,
            payment_method TEXT NOT NULL,
            notes TEXT,
            sold_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (inventory_id) REFERENCES inventory(id),
            FOREIGN KEY (customer_id) REFERENCES customers(id)
        );

        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            inventory_id INTEGER NOT NULL,
            customer_name TEXT NOT NULL,
            customer_email TEXT NOT NULL,
            customer_phone TEXT NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            deposit_amount REAL NOT NULL DEFAULT 0,
            preferred_date TEXT,
            preferred_time TEXT,
            status TEXT NOT NULL DEFAULT 'pending',
            notes TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            converted_sale_id INTEGER,
            FOREIGN KEY (inventory_id) REFERENCES inventory(id),
            FOREIGN KEY (converted_sale_id) REFERENCES sales(id)
        );

        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'admin',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS user_password_resets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            token_hash TEXT NOT NULL,
            expires_at TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS store_menu_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT NOT NULL,
            href TEXT NOT NULL,
            sort_order INTEGER NOT NULL DEFAULT 0,
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        SQL;

        self::connection()->exec($sql);
        self::seedStoreMenuDefaults();
        self::ensureColumn('inventory', 'hero_image', 'TEXT');
        self::ensureColumn('inventory', 'gallery_images', 'TEXT');
        self::ensureColumn('inventory', 'slug', 'TEXT');
        self::ensureColumn('inventory', 'online_price', 'REAL');
        self::ensureColumn('inventory', 'web_description', 'TEXT');
        self::ensureColumn('inventory', 'visible_online', 'INTEGER NOT NULL DEFAULT 1');
        self::backfillInventorySlugs();
        self::ensureColumn('users', 'role', "TEXT NOT NULL DEFAULT 'admin'");
    }

    private static function ensureColumn(string $table, string $column, string $definition): void
    {
        $pdo = self::connection();
        $columns = $pdo->query("PRAGMA table_info({$table})")->fetchAll();
        foreach ($columns as $col) {
            if (($col['name'] ?? '') === $column) {
                return;
            }
        }
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }

    private static function seedStoreMenuDefaults(): void
    {
        $pdo = self::connection();
        $count = (int) $pdo->query('SELECT COUNT(*) FROM store_menu_items')->fetchColumn();
        if ($count > 0) {
            return;
        }
        $defaults = [
            ['Home', '/', 0],
            ['Devices', '/#inventory-list', 10],
            ['Sign in', '/login', 20],
        ];
        $stmt = $pdo->prepare(
            'INSERT INTO store_menu_items (label, href, sort_order, is_active) VALUES (:label, :href, :sort_order, 1)'
        );
        foreach ($defaults as [$label, $href, $order]) {
            $stmt->execute([
                ':label' => $label,
                ':href' => $href,
                ':sort_order' => $order,
            ]);
        }
    }

    private static function backfillInventorySlugs(): void
    {
        $pdo = self::connection();
        $rows = $pdo->query("SELECT id, sku FROM inventory WHERE slug IS NULL OR slug = ''")->fetchAll();
        if (!$rows) {
            return;
        }
        $stmt = $pdo->prepare('UPDATE inventory SET slug = :slug WHERE id = :id');
        foreach ($rows as $row) {
            $slug = strtolower($row['sku']);
            $stmt->execute([':slug' => $slug, ':id' => $row['id']]);
        }
    }
}

