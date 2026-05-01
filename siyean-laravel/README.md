# SR Mac Shop — Application

This repository contains two pieces:

1. **`../siyean/`** — the SR Mac Shop POS / storefront / bookings application.
   Plain PHP 8.2 + SQLite, no framework. This is the actual product.
2. **`./` (this folder, `siyean-laravel/`)** — a Laravel 12 wrapper. Every
   inbound HTTP request is forwarded into `../siyean/public/index.php` by
   `app/Http/Controllers/LegacyBridgeController`. Laravel handles routing,
   middleware, errors, asset pipeline, and future framework-based features.

## Local Development

```bash
# 1. Install dependencies for both apps
cd siyean
composer install
cd ../siyean-laravel
composer install
cp .env.example .env
php artisan key:generate

# 2. Initialise the legacy app's SQLite database
mkdir -p ../siyean/storage
touch ../siyean/storage/pos.db

# 3. Create at least one admin user
php ../siyean/scripts/create_user.php \
    --name="Owner" --email="owner@example.com" \
    --password="ChangeMe123!" --role=admin

# 4. (Optional) seed sample inventory
php ../siyean/scripts/seed_inventory.php

# 5. Start the dev server
php artisan serve
# Visit http://127.0.0.1:8000/         (public storefront)
#       http://127.0.0.1:8000/login    (staff login)
```

See `../siyean/README.md` for the full feature list and CLI scripts.

## Deploying to cPanel

End-to-end runbook: [`../deploy/cpanel/README.md`](../deploy/cpanel/README.md).

Short version of what the server needs:

```bash
cd ~/repositories/siyeanwebsitesrstore

# Legacy app
cd siyean
composer install --no-dev --optimize-autoloader
mkdir -p storage
touch  storage/pos.db
chmod 775 storage
chmod 664 storage/pos.db

# Create admin user (one-off)
php scripts/create_user.php --name="Owner" \
    --email="owner@srmacshop.com" --password="<strong>" --role=admin

# List staff / reset password (SSH on server)
# php ../siyean/scripts/list_users.php
# php ../siyean/scripts/reset_password.php --email="owner@srmacshop.com" --password="<new>"

# Laravel wrapper
cd ../siyean-laravel
composer install --no-dev --optimize-autoloader
cp .env.example .env       # then edit .env (APP_ENV, DB, etc.)
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

Wire `~/public_html` to Laravel's `public/` once (see deploy guide for both
the symlink and forwarder approaches).

## How the bridge works

- `routes/web.php` matches **every** path and forwards to
  `LegacyBridgeController@handle`.
- The controller copies request data into `$_SERVER`, `$_GET`, `$_POST`,
  `$_FILES`, defines `LARAVEL_BRIDGE_MODE`, and `require`s
  `../siyean/public/index.php`.
- The legacy app does its own routing inside `App\Http\HttpKernel`, renders
  via `App\Http\ViewRenderer`, and writes output. The controller captures the
  output buffer and returns it as the Laravel response.
- Legacy redirects are signalled by throwing `RuntimeException` with a
  `__LEGACY_REDIRECT__:` prefix; the bridge converts those into a normal
  Laravel redirect response.
- Laravel's CSRF middleware is **disabled site-wide** in `bootstrap/app.php`
  because the legacy forms do not emit Laravel CSRF tokens. The legacy app
  manages its own form security.

## Production Checklist

- [ ] `siyean/` has `composer install --no-dev --optimize-autoloader` done
- [ ] `siyean/storage/pos.db` exists and is writable (664)
- [ ] At least one admin user created via `siyean/scripts/create_user.php`
      (`siyean/scripts/list_users.php` / `reset_password.php`)
- [ ] Optional: `siyean/config/app.php` set up (see `app.example.php`) for
      email / Telegram notifications
- [ ] Laravel `.env` has `APP_ENV=production`, `APP_DEBUG=false`,
      `APP_URL=https://srmacshop.com`, `APP_KEY` generated
- [ ] `siyean-laravel/storage/` and `bootstrap/cache/` writable (775)
- [ ] `config:cache`, `route:cache`, `view:cache` run
- [ ] `~/public_html` wired to `siyean-laravel/public/` (symlink or forwarder)
- [ ] HTTPS active in cPanel (AutoSSL) and Cloudflare SSL = Full (strict)
