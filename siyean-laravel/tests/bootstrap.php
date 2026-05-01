<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PHPUnit bootstrap (must load before Composer autoload)
|--------------------------------------------------------------------------
|
| This repo contains two Composer projects (`./siyean` and this folder).
| Laravel's Application::inferBasePath() picks the first registered Composer
| loader, which can resolve to the wrong directory and break tests with:
|   Failed opening required '.../siyean-laravel/siyean/bootstrap/app.php'
|
| Cached routes from production (`route:cache`) also omit /auth/* and cause
| HTTP tests to hit the legacy bridge (302 to /login).
|
*/

$basePath = dirname(__DIR__);

$_ENV['APP_BASE_PATH'] = $basePath;
$_SERVER['APP_BASE_PATH'] = $basePath;

// Never run Feature tests against production MySQL from `.env` — force SQLite.
$_ENV['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = ':memory:';
$_SERVER['DB_DATABASE'] = ':memory:';

foreach (glob($basePath.'/bootstrap/cache/routes-*.php') ?: [] as $cached) {
    @unlink($cached);
}

require $basePath.'/vendor/autoload.php';
