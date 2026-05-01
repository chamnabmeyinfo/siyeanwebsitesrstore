# cPanel Deployment — Quick Setup

Repo layout assumed (matches cPanel *Git Version Control* default):

```
/home/<user>/repositories/siyeanwebsitesrstore/
    siyean-laravel/         ← primary project: Laravel 12, Artisan, public/, scripts/
    siyean/                 ← legacy POS / storefront (PHP + SQLite); loaded by the bridge
    deploy/cpanel/          ← these deployment files
```

**Treat `siyean-laravel/` as your main `cd` target** (Artisan, `php scripts/…`,
deployment). `siyean/` holds the bundled legacy source and `storage/pos.db`.

The goal: serve `srmacshop.com` from this repo via `~/public_html`.

The runtime chain on the server:

```
Browser  →  Cloudflare  →  LiteSpeed  →  ~/public_html/  →  siyean-laravel/public/index.php
                                                              ↓
                                                          LegacyBridgeController
                                                              ↓
                                                      siyean/public/index.php  (the actual app)
```

---

## Prerequisites (in cPanel UI)

1. **PHP 8.2+** — *MultiPHP Manager* → set `srmacshop.com` to PHP 8.2 or 8.3.
   Required PHP extensions (almost always on by default): `pdo`, `pdo_sqlite`
   (legacy app uses SQLite), `pdo_mysql` (Laravel uses MySQL), `mbstring`,
   `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `bcmath`.
2. **MySQL database for Laravel** — *MySQL Databases* → create
   `<user>_srmacshop` DB + matching user with **ALL PRIVILEGES**. (The
   legacy app does NOT need MySQL — it uses SQLite at
   `siyean/storage/pos.db`.)
3. **HTTPS** — *SSL/TLS Status* → run **AutoSSL** for `srmacshop.com` (after
   Step 3 below). If using Cloudflare, set SSL/TLS mode = **Full (strict)**.

---

## Step 1 — Set up the Laravel project (`siyean-laravel/`)

In cPanel **Terminal**:

```bash
cd ~/repositories/siyeanwebsitesrstore/siyean-laravel

composer install --no-dev --optimize-autoloader

cp .env.example .env
php artisan key:generate
```

Edit `.env` for production. Minimal block:

```dotenv
APP_NAME="SR Mac Shop"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://srmacshop.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<user>_srmacshop
DB_USERNAME=<user>_srmacshop
DB_PASSWORD=<the-password-you-set>

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Then:

```bash
php artisan migrate --force        # creates Laravel's own users/cache/jobs tables
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R 775 storage bootstrap/cache
```

Important: **after editing `.env` ever again**, run `php artisan
config:clear && php artisan config:cache`.

---

## Step 2 — Set up the legacy SR Mac Shop app (`siyean/`)

The live storefront and staff UI run from this folder via the Laravel bridge.

```bash
cd ~/repositories/siyeanwebsitesrstore/siyean

composer install --no-dev --optimize-autoloader

mkdir -p storage
touch  storage/pos.db
chmod 775 storage
chmod 664 storage/pos.db

# (Optional) custom config — only needed for email / Telegram notifications
cp config/app.example.php config/app.php
nano config/app.php

# (Optional) seed sample inventory + demo data so the storefront isn't empty
php scripts/seed_inventory.php
# OR for a fuller demo dataset:
php scripts/seed_demo_data.php
```

After this, `siyean/storage/pos.db` is the source of truth for **products,
sales, customers, bookings, users, store-menu**.

> Roles available: `admin` (full access incl. delete/import), `clerk`
> (sales + inventory edit), `ecommerce` (bookings console).

### Staff accounts — create, list, reset (run from `siyean-laravel/`)

Use the project-root wrappers so your working directory stays **`siyean-laravel/`**:

```bash
cd ~/repositories/siyeanwebsitesrstore/siyean-laravel

# Create your first admin (one-off)
php scripts/create_user.php \
    --name="Owner" \
    --email="owner@srmacshop.com" \
    --password="<a-strong-password>" \
    --role=admin

# See id, name, email, role, created_at (no password data)
php scripts/list_users.php

# Set a new password for an existing email from the list above
php scripts/reset_password.php \
    --email="owner@srmacshop.com" \
    --password="<new-strong-password>"
```

`reset_password.php` updates `password_hash` only; it does not create users.

---

## Step 3 — Wire `public_html` to Laravel's `public/`

Try **Approach A** first. If LiteSpeed refuses to follow the symlinked
docroot (you'll get a 404 from LiteSpeed at `/`), use **Approach B**.

### Approach A — Symlink (cleanest)

```bash
mv ~/public_html ~/public_html.backup.$(date +%s) 2>/dev/null
ln -s ~/repositories/siyeanwebsitesrstore/siyean-laravel/public ~/public_html
ls -l ~/public_html
```

If `ls -l` shows the symlink and `curl -sI https://srmacshop.com/` returns
`HTTP/2 200`, you're done.

### Approach B — Forwarder (works everywhere, including LiteSpeed-strict)

```bash
rm -f ~/public_html
mkdir ~/public_html

# Forwarder index.php + .htaccess from this repo
cp ~/repositories/siyeanwebsitesrstore/deploy/cpanel/public_html/index.php ~/public_html/
cp ~/repositories/siyeanwebsitesrstore/deploy/cpanel/public_html/.htaccess ~/public_html/

# Static asset folders (single-hop symlinks; LiteSpeed handles these)
ln -s ~/repositories/siyeanwebsitesrstore/siyean-laravel/public/build ~/public_html/build

# storage/ → straight to the actual files (skip the Laravel-internal hop)
ln -s ~/repositories/siyeanwebsitesrstore/siyean-laravel/storage/app/public ~/public_html/storage

# Static files served directly
cp ~/repositories/siyeanwebsitesrstore/siyean-laravel/public/favicon.ico ~/public_html/
cp ~/repositories/siyeanwebsitesrstore/siyean-laravel/public/robots.txt  ~/public_html/

# The legacy app keeps its branding under siyean/public/assets — make those
# reachable directly so <img src="/assets/sr-mac-logo.svg"> just works.
ln -s ~/repositories/siyeanwebsitesrstore/siyean/public/assets ~/public_html/assets

ls -la ~/public_html
```

---

## Step 4 — Verify

```bash
curl -sI https://srmacshop.com/ | head -5            # expect HTTP/2 200
curl -s  https://srmacshop.com/ | head -20           # expect storefront HTML
curl -sI https://srmacshop.com/store | head -3       # expect HTTP/2 200
curl -sI https://srmacshop.com/login | head -3       # expect HTTP/2 200
```

In a browser:
- `https://srmacshop.com/` — public storefront (SR Mac Shop)
- `https://srmacshop.com/login` — staff sign-in (admin user from Step 2)
- After login: `/dashboard`, `/inventory`, `/sales`, `/sales/new`,
  `/bookings`, `/inventory/import`, `/inventory/export`

---

## Step 5 — Updates after a future `git push`

```bash
cd ~/repositories/siyeanwebsitesrstore
git pull

# Reinstall vendors only if composer.json/.lock changed (project root first)
[ -d siyean-laravel/vendor ] || (cd siyean-laravel && composer install --no-dev --optimize-autoloader)
[ -d siyean/vendor ]         || (cd siyean         && composer install --no-dev --optimize-autoloader)

cd siyean-laravel
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

You don't need to touch `~/public_html` again — it forwards to whatever's in
`siyean-laravel/public/`, which forwards to `siyean/`.

---

## Troubleshooting

| Symptom | Likely cause | Fix |
|---|---|---|
| `HTTP 500 / Legacy application entrypoint not found.` | `siyean/` folder missing or wrong path | `ls -la ~/repositories/siyeanwebsitesrstore/siyean/public/index.php` should exist |
| `HTTP 500 / Class "App\Database" not found` | `siyean/vendor/` not installed | `cd siyean && composer install --no-dev --optimize-autoloader` |
| `HTTP 500 / unable to open database file` | `siyean/storage/pos.db` missing or not writable | `mkdir -p siyean/storage && touch siyean/storage/pos.db && chmod 775 siyean/storage && chmod 664 siyean/storage/pos.db` |
| Storefront loads but no images / `/assets/sr-mac-logo.svg` 404 | Approach B without the `assets` symlink | `ln -s ~/repositories/siyeanwebsitesrstore/siyean/public/assets ~/public_html/assets` |
| Login form just reloads, no error | Stale Laravel route cache | `cd siyean-laravel && php artisan route:clear && php artisan config:clear && php artisan route:cache && php artisan config:cache` |
| Plain LiteSpeed 404 page (not Laravel-styled) | `~/public_html` not wired (Step 3 not done or was reverted) | Re-run Step 3 |
| Stale page after deploy | Cloudflare cache | Cloudflare → Caching → Purge Everything |
| `Could not open input file: scripts/list_users.php` (or other `scripts/*.php`) | Server clone is older than the commits that add `siyean-laravel/scripts/` | `cd ~/repositories/siyeanwebsitesrstore && git pull` then `ls siyean-laravel/scripts`. Until updated, run the real scripts from `siyean/`: `cd siyean && php scripts/list_users.php` |

Logs to check:

```bash
tail -50 ~/repositories/siyeanwebsitesrstore/siyean-laravel/storage/logs/laravel.log
ls ~/logs/                              # cPanel domain access/error logs
```
