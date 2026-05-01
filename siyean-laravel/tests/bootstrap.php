<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| PHPUnit bootstrap (must load before Composer autoload)
|--------------------------------------------------------------------------
|
| This repo contains two Composer projects (`../siyean` and this folder).
| Laravel's Application::inferBasePath() picks the first registered Composer
| loader, which can resolve to the wrong directory and break tests with:
|   Failed opening required '.../siyean/bootstrap/app.php'
|
| Cached routes from production (`route:cache`) also omit /auth/* and cause
| HTTP tests to hit the legacy bridge (302 to /login).
|
*/

$basePath = dirname(__DIR__);

$_ENV['APP_BASE_PATH'] = $basePath;
$_SERVER['APP_BASE_PATH'] = $basePath;

foreach (glob($basePath.'/bootstrap/cache/routes-*.php') ?: [] as $cached) {
    @unlink($cached);
}

require $basePath.'/vendor/autoload.php';
