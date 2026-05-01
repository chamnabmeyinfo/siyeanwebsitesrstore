## POS for Mac Sales (PHP)

This folder is the **legacy** SR Mac Shop app (PHP 8.2 + SQLite). It is bundled
inside the main project at [`../`](../) — that Laravel app is the deployment
entry point and forwards HTTP requests here.

Day-to-day setup commands are documented in `../README.md`; you can also run
the same CLI scripts from `../scripts/` (wrappers).

---

This application tracks MacBook inventory and sales. Everything runs locally—no external SaaS—and the UI is optimized for a single in-store operator.

### Highlights

- Inventory catalog with SKU, model, storage, color, cost, list price, and on-hand quantity.
- Sale capture flow with customer details, discounts, tax, payment method, and notes.
- Automatic stock deductions whenever a sale is recorded plus customer deduplication.
- Dashboard with live revenue/unit metrics and full sales history table.
- Visitor-facing showroom page so customers can browse available Macs/PCs.
- Per-product hero image + gallery thumbnails with a magnifier overlay for an ecommerce-style experience.
- Full inventory CRUD from the browser, including CSV import/export and quick quantity adjustments.
- Online booking workflow so customers can reserve devices while staff manage confirmations in the bookings console.

### Prerequisites

- PHP 8.2+ with `pdo_sqlite` enabled.
- Composer.

### Getting started

If you use the repo’s Laravel app (`../`), follow that README:
your working directory is **`siyean-laravel/`**, and the same CLI commands exist
as `php scripts/*.php` there (wrappers). The steps below apply when developing
**this folder alone** (`composer serve`).

1. Install dependencies:
   ```
   composer install
   ```
2. Boot the development server (auto creates/updates `storage/pos.db`):
   ```
   composer serve
   ```
3. Visit http://127.0.0.1:8000/ for the **public shop** (same catalog as `/store`). Staff sign in at `/login`, then use:
   - Dashboard (`/dashboard`)
   - Inventory manager (`/inventory`)
   - New sale form (`/sales/new`)
   - Sales history (`/sales`)
4. (Optional) Seed sample inventory:
   ```
   php scripts/seed_inventory.php
   ```
5. Create at least one admin user:
   ```
   php scripts/create_user.php --name="Store Owner" --email="owner@example.com" --password="SuperSecure123" --role=admin
   ```
6. Sign in at http://127.0.0.1:8000/login (admins can invite clerks with `--role=clerk`).

   List existing staff accounts (CSV to the terminal; passwords are never shown):

   ```
   php scripts/list_users.php
   ```
   (From `../`, same filenames under `scripts/`.)

   To reset an existing user’s password (same SQLite DB as login):

   ```
   php scripts/reset_password.php --email="owner@example.com" --password="NewSecurePassword"
   ```
7. Manage online reservations at http://127.0.0.1:8000/bookings (admins/ecommerce managers).

### Notifications

1. Copy `config/app.example.php` to `config/app.php`.
2. Populate the `notifications` section:
   - `MAIL_*` settings if you want email alerts (requires a working mail transport).
   - `TELEGRAM_*` values (bot token + chat ID) to receive Telegram pings.
   - `LOW_STOCK_THRESHOLD` to control when low-stock alerts are sent.
3. Restart `composer serve` after changing configuration so the new settings are loaded.

### Branding

- Update `public/assets/sr-mac-logo.svg` if you want to swap in your own badge (both the admin and storefront headers load this asset). 
- Edit `templates/layout.php` or `templates/layout_store.php` if you want alternate taglines or navigation labels.

### Example workflow

1. Add your first SKU via the Inventory page (fills cost/list/quantity).
2. Drop in optional hero + gallery URLs in the Add Inventory form so `/store` showcases the device.
3. Record a sale from “New Sale” choosing the SKU and entering the buyer info.
4. Review inventory counts and revenue updates on the Dashboard, while customers browse `/store`.
5. Customers reserve devices from the product detail page; staff confirm via `/bookings` and convert to sales.
6. Use role-aware controls: admins can import/export/delete SKUs, clerks can create/edit stock and log sales.

Architectural notes live in `docs/pos_plan.md`.
