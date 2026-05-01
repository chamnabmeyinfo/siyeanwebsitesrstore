<?php

/*
|--------------------------------------------------------------------------
| cPanel public_html forwarder
|--------------------------------------------------------------------------
|
| Drop this file into ~/public_html/index.php on a cPanel host where the
| Laravel application lives outside the document root.
|
| Default layout assumed:
|   ~/repositories/siyeanwebsitesrstore/siyean-laravel/   (the Laravel app)
|   ~/public_html/                                        (Apache document root)
|
| If your clone lives elsewhere, edit the LARAVEL_PUBLIC constant below.
|
| Note: __DIR__ inside Laravel's own public/index.php still resolves to
| siyean-laravel/public, so its relative require()s for autoload.php and
| bootstrap/app.php continue to work correctly.
|
*/

const LARAVEL_PUBLIC = __DIR__ . '/../repositories/siyeanwebsitesrstore/siyean-laravel/public';

$entrypoint = LARAVEL_PUBLIC . '/index.php';

if (!is_file($entrypoint)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Laravel entrypoint not found at: {$entrypoint}\n";
    echo "Edit ~/public_html/index.php and update LARAVEL_PUBLIC to the correct path.\n";
    exit;
}

require $entrypoint;
