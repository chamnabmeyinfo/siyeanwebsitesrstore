## POS System Plan

### Goals

- Track MacBook inventory levels and record each sale in a durable store.
- Keep core workflows (inventory intake, selling, reporting) fast for a single-operator shop.
- Avoid external dependencies so the tool is easy to run locally or from a USB stick.

### Architecture

- **Language:** PHP 8.2+
- **Runtime:** Built-in PHP dev server (`composer serve`) or any PHP-FPM host.
- **Storage:** SQLite file (`storage/pos.db`) accessed through PDO.
- **Structure:**
  - `src/Database.php`: PDO connection + migrations.
  - `src/InventoryRepository.php`: CRUD, stock adjustments, storefront lookups.
  - `src/SaleService.php`: customer upsert, stock enforcement, sale persistence.
  - `src/ReportService.php`: revenue/unit aggregations and inventory snapshot.
  - `src/UserRepository.php`: authentication + role lookups.
  - `public/index.php`: bootstrap only — wires services and delegates to `HttpKernel`.
  - `src/Http/`: HTTP concerns — routing (`HttpKernel`), sessions/auth (`AuthGate`), responses (`ViewRenderer`), dashboard view-model (`DashboardViewModel`), form mapping (`Http/Form/*`).
  - `templates/*.php`: PHP templates (presentation); business rules stay out of views.
  - `templates/layout_store.php`: public showroom shell.

### Key data model

- **Inventory**
  - `id`, `sku`, `slug`, `model`, `storage_capacity`, `color`, `cost_price`, `list_price`, `online_price`, `quantity_on_hand`, `hero_image`, `gallery_images`, `web_description`, `visible_online`, `created_at`.
- **Customers**
  - `id`, `name`, `email`, `phone`.
- **Sales**
  - `id`, `inventory_id`, `customer_id`, `quantity`, `unit_price`, `discount`, `tax_rate`, `payment_method`, `notes`, `sold_at`.
- **Bookings**
  - `id`, `inventory_id`, `customer_name`, `customer_email`, `customer_phone`, `quantity`, `deposit_amount`, `preferred_date`, `preferred_time`, `status (pending|confirmed|picked_up|cancelled)`, `notes`, `created_at`, `converted_sale_id`.
- **Users**
  - `id`, `name`, `email`, `password_hash`, `role (admin|clerk|ecommerce)`, `created_at`.
- **Payments** (virtual)
  - Derived inside each sale/booking record; total = `(unit_price * quantity) - discount + tax - deposit`.

### Primary workflows

1. `composer install` to pull autoload files.
2. `composer serve` to boot the dev server (first request runs migrations automatically).
3. `/login` authenticates admins/clerks/ecommerce managers; `/logout` ends the session (returns visitors to the public shop home).
4. `/` and `/store` serve the public storefront catalog (same content); product URLs stay under `/store/product`.
5. `/dashboard` (signed-in) shows POS metrics; `/inventory` handles SKU creation, adjustment, import/export (delete/import restricted to admins).
6. `/sales/new` records in-store sales and auto-decrements inventory.
7. `/sales` provides sales history alongside dashboard summaries on `/dashboard`.
8. Bookings: customers use `/store/product` + `/store/book`; staff use `/bookings` for confirmations and notifications.

### Non-goals (v1)

- Multi-user concurrency; assume single seller.
- Integrations with payment processors or barcode scanners.

### Testing strategy

- Future PHPUnit feature tests using an in-memory SQLite database.
- Manual sanity pass per change: add SKU → record sale → confirm stock drop + sale history entry.
