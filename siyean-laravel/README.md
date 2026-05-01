# SR Mac Shop — Application

**This folder (`siyean-laravel/`) is the project root:** composer/Artisan,
deployment (`public/`), and `scripts/` wrappers for staff/SQLite tasks all live
here.

**`../siyean/`** holds the legacy POS / storefront / bookings code (PHP + SQLite,
no framework). Every HTTP request is bridged into `../siyean/public/index.php` via
`app/Http/Controllers/LegacyBridgeController`. Laravel supplies the front
controller, middleware, configuration, and future framework features.

## Local Development

Work from **`siyean-laravel/`**:

```bash
cd siyean-laravel
composer install
cp .env.example .env
php artisan key:generate

# Legacy app: dependencies + SQLite (still stored under ../siyean/storage/)
cd ../siyean && composer install && mkdir -p storage && touch storage/pos.db && cd ../siyean-laravel

# Staff accounts (wrappers call ../siyean/scripts/*.php)
php scripts/create_user.php \
    --name="Owner" --email="owner@example.com" \
    --password="ChangeMe123!" --role=admin

# Optional: sample inventory
php scripts/seed_inventory.php

php artisan serve
# http://127.0.0.1:8000/       storefront
# http://127.0.0.1:8000/login  staff
```

Other CLI tools: `php scripts/list_users.php`, `php scripts/reset_password.php`,
`php scripts/seed_demo_data.php`.

See `../siyean/README.md` for POS feature detail (optional reading).

## Deploying to cPanel

End-to-end runbook: [`../deploy/cpanel/README.md`](../deploy/cpanel/README.md).

Short version (project root = **`siyean-laravel/`**):

```bash
cd ~/repositories/siyeanwebsitesrstore/siyean-laravel

composer install --no-dev --optimize-autoloader
cp .env.example .env       # then edit .env (APP_ENV, DB, etc.)
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache

# Legacy app (SQLite + vendors), required for the live site
cd ../siyean
composer install --no-dev --optimize-autoloader
mkdir -p storage
touch  storage/pos.db
chmod 775 storage
chmod 664 storage/pos.db
cd ../siyean-laravel

# Staff users — run from siyean-laravel/scripts/
php scripts/create_user.php --name="Owner" \
    --email="owner@srmacshop.com" --password="<strong>" --role=admin
# php scripts/list_users.php
# php scripts/reset_password.php --email="owner@srmacshop.com" --password="<new>"
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

- [ ] `siyean-laravel/` — `composer install`, `.env`, migrations, caches (above)
- [ ] `siyean/` — `composer install --no-dev --optimize-autoloader`
- [ ] `siyean/storage/pos.db` exists and is writable (664)
- [ ] At least one staff user via `php scripts/create_user.php` (from
      `siyean-laravel/`); list/reset: `list_users.php`, `reset_password.php`
- [ ] Optional: `siyean/config/app.php` set up (see `app.example.php`) for
      email / Telegram notifications
- [ ] Laravel `.env` has `APP_ENV=production`, `APP_DEBUG=false`,
      `APP_URL=https://srmacshop.com`, `APP_KEY` generated
- [ ] `siyean-laravel/storage/` and `bootstrap/cache/` writable (775)
- [ ] `config:cache`, `route:cache`, `view:cache` run
- [ ] `~/public_html` wired to `siyean-laravel/public/` (symlink or forwarder)
- [ ] HTTPS active in cPanel (AutoSSL) and Cloudflare SSL = Full (strict)
