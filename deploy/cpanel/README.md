# cPanel Deployment — Quick Setup

This guide assumes the repo is cloned at:

```
/home/<cpanel-user>/repositories/siyeanwebsitesrstore/
```

(which is the default location used by cPanel's *Git Version Control*
feature). The Laravel app is therefore at:

```
/home/<cpanel-user>/repositories/siyeanwebsitesrstore/siyean-laravel/
```

The goal: serve `srmacshop.com` from this Laravel app via `~/public_html`.

---

## Prerequisites (one-time, in cPanel UI)

1. **PHP 8.2+** — *MultiPHP Manager* → set `srmacshop.com` to PHP 8.2 or 8.3.
2. **Database** — pick one:
   - **SQLite** (zero config — recommended for first launch). Nothing to do
     in the cPanel UI.
   - **MySQL** — *MySQL Databases* → create database `<user>_srmacshop`,
     create a DB user, attach with **ALL PRIVILEGES**. Note the prefixed
     names cPanel assigns.
3. **HTTPS** — *SSL/TLS Status* → run **AutoSSL** for `srmacshop.com` (do
   this after the site is up, but before going live to the public).

---

## Step 1 — Configure the Laravel app

Open cPanel **Terminal** (or SSH in) and run:

```bash
cd ~/repositories/siyeanwebsitesrstore/siyean-laravel

# Install PHP dependencies for production
composer install --no-dev --optimize-autoloader

# Create the .env file
cp .env.example .env

# Generate the application encryption key
php artisan key:generate
```

Edit `.env` (use the cPanel File Manager or `nano .env`):

```dotenv
APP_NAME="SR Mac Shop"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://srmacshop.com

LOG_CHANNEL=stack
LOG_LEVEL=warning

# --- pick ONE database block ---

# SQLite (simplest — file lives at database/database.sqlite)
DB_CONNECTION=sqlite

# OR MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=<cpanel-user>_srmacshop
# DB_USERNAME=<cpanel-user>_srmacshop
# DB_PASSWORD=<the-password-you-set>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Then initialise the database and caches:

```bash
# If using SQLite, create the file
touch database/database.sqlite

php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions Apache needs (cPanel runs PHP as your user, so 775 is fine)
chmod -R 775 storage bootstrap/cache
```

---

## Step 2 — Wire `public_html` to Laravel's `public/`

Pick **Approach A** if your host allows replacing `public_html` (most do).
Otherwise use **Approach B**.

### Approach A — Symlink (recommended, cleanest)

```bash
# Back up whatever is currently in public_html, then replace it with a
# symlink to Laravel's public/ folder.
mv ~/public_html ~/public_html.backup.$(date +%s)
ln -s ~/repositories/siyeanwebsitesrstore/siyean-laravel/public ~/public_html
```

That's it. Apache will now serve Laravel directly. The `public/.htaccess`
already in the repo handles all routing.

If `ls -l ~/public_html` shows `public_html -> ... /siyean-laravel/public`
you're done — go to **Step 3**.

> If your host blocks this (some hosts auto-recreate `public_html` or refuse
> to follow symlinks for the document root), use Approach B instead.

### Approach B — Forwarder (works on every host)

```bash
# Empty public_html (back up first if it has anything you want to keep)
mv ~/public_html ~/public_html.backup.$(date +%s)
mkdir ~/public_html

# Drop in the forwarder files from this repo
cp ~/repositories/siyeanwebsitesrstore/deploy/cpanel/public_html/index.php   ~/public_html/
cp ~/repositories/siyeanwebsitesrstore/deploy/cpanel/public_html/.htaccess   ~/public_html/

# Make Apache-served static asset folders reachable from public_html
ln -s ~/repositories/siyeanwebsitesrstore/siyean-laravel/public/build    ~/public_html/build
ln -s ~/repositories/siyeanwebsitesrstore/siyean-laravel/public/storage  ~/public_html/storage
cp    ~/repositories/siyeanwebsitesrstore/siyean-laravel/public/favicon.ico ~/public_html/
cp    ~/repositories/siyeanwebsitesrstore/siyean-laravel/public/robots.txt  ~/public_html/
```

If your host blocks symlinks entirely, replace the two `ln -s` lines with
`cp -R ...` — but then you must repeat the `cp -R` after every front-end
build.

---

## Step 3 — Verify

Visit `https://srmacshop.com` (or `http://` if HTTPS isn't on yet).

You should see the **Laravel welcome page**.

If you get a blank page or HTTP 500:

1. Check the log: `tail -100 ~/repositories/siyeanwebsitesrstore/siyean-laravel/storage/logs/laravel.log`
2. Temporarily set `APP_DEBUG=true` in `.env`, hit the page, read the
   error, then set it back to `false`.
3. Re-check permissions: `chmod -R 775 ~/repositories/siyeanwebsitesrstore/siyean-laravel/storage ~/repositories/siyeanwebsitesrstore/siyean-laravel/bootstrap/cache`

Common errors and fixes are in the main
[`siyean-laravel/README.md` Troubleshooting section](../../siyean-laravel/README.md#troubleshooting).

---

## Step 4 — Updating after future `git push`

```bash
cd ~/repositories/siyeanwebsitesrstore
git pull
cd siyean-laravel
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you used Approach A (symlink), no further action is needed. If you used
Approach B (forwarder), no further action is needed either — only re-copy
the forwarder files if `deploy/cpanel/public_html/` itself changes (it
rarely does).

---

## Front-end assets (Vite)

`public/build/` is in `.gitignore`. If you add or change anything in
`resources/css` or `resources/js`, build assets and upload `public/build/`:

```bash
# Locally on your dev machine:
cd siyean-laravel
npm ci
npm run build
# Then upload siyean-laravel/public/build/ to the server (rsync, SFTP, etc.)
```

If your cPanel host has Node.js available, you can build on the server
instead.

---

## Production checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false` in `.env`
- [ ] `APP_URL=https://srmacshop.com` (with the real scheme)
- [ ] `APP_KEY` generated
- [ ] `php artisan migrate --force` succeeded
- [ ] `php artisan storage:link` succeeded
- [ ] `config:cache`, `route:cache`, `view:cache` run
- [ ] `storage/`, `bootstrap/cache/` are 775 and owned by you
- [ ] `https://srmacshop.com` returns the welcome page (HTTP 200)
- [ ] AutoSSL active in cPanel
- [ ] `~/repositories/siyeanwebsitesrstore/siyean-laravel/.env` is **not**
      web-accessible (it isn't — it's outside `public_html`)
