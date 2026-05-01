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

## Website accounts (Laravel / MySQL)

Standard session auth lives **outside** the legacy app and uses the Laravel
`users` table (MySQL):

| URL | Purpose |
|-----|---------|
| `/auth/register` | Sign up |
| `/auth/login` | Sign in |
| `/auth/forgot-password` | Request password-reset email |
| `/auth/reset-password/{token}` | Set new password (link from email) |
| `/auth/account` | Signed-in profile + logout (when authenticated) |

**Staff / POS** still uses **`/login`** and SQLite (`../siyean/storage/pos.db`)
via the legacy bridge — same browser can hold both sessions independently.

Password-reset emails require valid **`MAIL_*`** settings in `.env`. Automatic
email verification on register is disabled until you add a verify flow (see
`App\Models\User::sendEmailVerificationNotification`).

## Running automated tests

PHPUnit lives in **`require-dev`**. Servers installed with
`composer install --no-dev --optimize-autoloader` **do not** ship PHPUnit, so
`php artisan test` may say `Command "test" is not defined` — that is normal on
production; you do not need to run tests there.

Run tests on your PC or CI after a **full** `composer install`, from **`siyean-laravel/`**:

```bash
php artisan test
# or
vendor/bin/phpunit tests/Feature/WebsiteAuthTest.php
```

PHPUnit uses `tests/bootstrap.php` to set **`APP_BASE_PATH`** (this repo has two
Composer roots; without it, Laravel can resolve the wrong base path), to
delete **`bootstrap/cache/routes-*.php`** so tests never use a stale
**`route:cache`** from production (which would skip `/auth/*` and return **302**
from the legacy app), and to force **`DB_CONNECTION=sqlite`** /
**`DB_DATABASE=:memory:`** so Feature tests do not run against the server MySQL
from `.env`.

After deploying route changes, rebuild or clear caches:
`php artisan route:clear` or `php artisan route:cache`.

`php artisan migrate --force` reporting **Nothing to migrate** means the
database already matches your migrations — not an error.

## How the bridge works

- Explicit Laravel routes (for example `/auth/*`) are handled first; **all other**
  paths match **`/{any?}`** and forward to `LegacyBridgeController@handle`.
- The controller copies request data into `$_SERVER`, `$_GET`, `$_POST`,
  `$_FILES`, defines `LARAVEL_BRIDGE_MODE`, and `require`s
  `../siyean/public/index.php`.
- The legacy app does its own routing inside `App\Http\HttpKernel`, renders
  via `App\Http\ViewRenderer`, and writes output. The controller captures the
  output buffer and returns it as the Laravel response.
- Legacy redirects are signalled by throwing `RuntimeException` with a
  `__LEGACY_REDIRECT__:` prefix; the bridge converts those into a normal
  Laravel redirect response.
- Laravel CSRF protection applies to `/auth/*` (Blade forms). The legacy
  catch-all route skips CSRF middleware because legacy forms do not emit Laravel
  tokens; the legacy app manages its own form security there.

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
- [ ] If using password reset: `MAIL_*` configured (same as transactional mail)
- [ ] `siyean-laravel/storage/` and `bootstrap/cache/` writable (775)
- [ ] `config:cache`, `route:cache`, `view:cache` run
- [ ] `~/public_html` wired to `siyean-laravel/public/` (symlink or forwarder)
- [ ] HTTPS active in cPanel (AutoSSL) and Cloudflare SSL = Full (strict)
