# SR Mac Shop — Laravel Application

A Laravel 12 application for SR Mac Shop. PHP 8.2+ required.

## Local Development

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # macOS / Linux
# (Windows PowerShell)  New-Item database/database.sqlite -ItemType File
php artisan migrate
npm install
npm run dev                           # in a second terminal, optional (Vite)
php artisan serve                     # http://127.0.0.1:8000
```

## Deploying to cPanel

This guide assumes a typical cPanel shared host with PHP 8.2+, Composer access
(via SSH or Terminal), and either MySQL/MariaDB or SQLite available.

### 1. Verify the host

In cPanel **MultiPHP Manager**, set the domain's PHP version to **8.2 or
higher**. Required PHP extensions (almost always enabled by default on cPanel):

- `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `pdo`,
  `tokenizer`, `xml`, plus `pdo_mysql` (if using MySQL) or `pdo_sqlite`.

### 2. Upload the code

Pick **one** of the two recommended layouts.

#### Option A (recommended) — Project outside `public_html`

```
/home/<cpanel-user>/
├── srmacshop/                  ← clone repo here (siyean-laravel/ is inside)
└── public_html/                ← Apache document root for your domain
```

Steps via SSH:

```bash
cd ~
git clone https://github.com/chamnabmeyinfo/siyeanwebsitesrstore.git srmacshop
cd srmacshop/siyean-laravel
composer install --no-dev --optimize-autoloader
```

Then point the domain at Laravel's `public/` folder. Two equally valid ways:

1. **Change the document root** (cPanel → *Domains* → edit your domain →
   document root = `srmacshop/siyean-laravel/public`). This is the cleanest
   approach when the host allows it.

2. **If you cannot change the document root**, replace the contents of
   `~/public_html` with a small bootstrap that delegates to Laravel:

   `~/public_html/index.php`:

   ```php
   <?php
   require __DIR__ . '/../srmacshop/siyean-laravel/public/index.php';
   ```

   `~/public_html/.htaccess` (copy from `siyean-laravel/public/.htaccess`):

   ```apache
   <IfModule mod_rewrite.c>
       <IfModule mod_negotiation.c>
           Options -MultiViews -Indexes
       </IfModule>
       RewriteEngine On
       RewriteCond %{HTTP:Authorization} .
       RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </IfModule>
   ```

   Then symlink Laravel's `public/build` and `public/storage` (and any other
   static asset folders) into `public_html` so Apache can serve them directly.

#### Option B — Everything inside `public_html` (only if you have no other choice)

Upload `siyean-laravel/` *contents* into `~/public_html`. This exposes
`vendor/`, `.env`, `storage/`, etc. to the web. You **must** keep the existing
`public/.htaccess` and add a top-level `.htaccess` that blocks access to
everything except `public/`. Option A is strongly preferred.

### 3. Configure the environment

Create `siyean-laravel/.env` (do **not** commit it). Start from `.env.example`:

```bash
cp .env.example .env
php artisan key:generate
```

Then edit `.env` for production:

```dotenv
APP_NAME="SR Mac Shop"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://srmacshop.com

LOG_CHANNEL=stack
LOG_LEVEL=warning

# --- Database: pick ONE block ---

# MySQL / MariaDB (typical on cPanel)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpaneluser_srmacshop
DB_USERNAME=cpaneluser_srmacshop
DB_PASSWORD=<strong-password>

# OR SQLite (file-based, no MySQL needed)
# DB_CONNECTION=sqlite
# DB_DATABASE=/home/<cpanel-user>/srmacshop/siyean-laravel/database/database.sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

For MySQL: create the DB and user in cPanel → *MySQL Databases*, attach the
user with **ALL PRIVILEGES**, and use the prefixed name cPanel assigns.

### 4. Initialise the application

```bash
cd ~/srmacshop/siyean-laravel
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. File permissions

Apache (run as the cPanel user on shared hosting) must be able to write to
`storage/` and `bootstrap/cache/`:

```bash
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

If using SQLite, ensure both the `database/` directory and the `.sqlite` file
are writable (`chmod 775 database` and `chmod 664 database/database.sqlite`).

### 6. Build front-end assets

Vite assets (`public/build/`) are not committed. Build them locally and upload
`public/build/` to the server, **or** run on the server if Node.js is
available:

```bash
npm ci
npm run build
```

### 7. Schedule + queues (only if you start using them)

cPanel → *Cron Jobs*, run every minute:

```
* * * * * cd /home/<cpanel-user>/srmacshop/siyean-laravel && php artisan schedule:run >> /dev/null 2>&1
```

For background queues, set up a Supervisor-like process or use cPanel's
"Application Manager" / a cron-backed `queue:work --stop-when-empty` loop.

### Updating after a `git push`

```bash
cd ~/srmacshop
git pull
cd siyean-laravel
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Production Checklist

- [ ] `APP_ENV=production` and `APP_DEBUG=false` in `.env`
- [ ] `APP_KEY` generated (`php artisan key:generate`)
- [ ] `APP_URL` matches the live domain (with `https://`)
- [ ] HTTPS enabled in cPanel (AutoSSL / Let's Encrypt)
- [ ] Database credentials set and `php artisan migrate --force` run
- [ ] `storage/` and `bootstrap/cache/` writable
- [ ] `config:cache`, `route:cache`, `view:cache` run
- [ ] Document root points to `siyean-laravel/public/` (or bootstrap forwarder
      in place — see Option A)
- [ ] `.env`, `vendor/`, `storage/`, `bootstrap/`, `database/` are not
      web-accessible

## Troubleshooting

- **HTTP 500 on every page**: temporarily set `APP_DEBUG=true` in `.env`,
  refresh, read the message, then set it back to `false`. Check
  `storage/logs/laravel.log`.
- **"No application encryption key has been specified."**: run
  `php artisan key:generate`.
- **"could not find driver"**: enable `pdo_mysql` (MultiPHP INI Editor) or
  switch `DB_CONNECTION=sqlite` and ensure `pdo_sqlite` is enabled.
- **Permission denied on `storage/logs/laravel.log`**: re-run the chmod block
  in step 5.
