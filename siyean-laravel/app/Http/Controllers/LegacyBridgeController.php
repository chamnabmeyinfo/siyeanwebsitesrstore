<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class LegacyBridgeController extends Controller
{
    public function handle(Request $request): Response
    {
        $legacyRoot = base_path('siyean');
        $legacyPublic = $legacyRoot . '/public/index.php';

        if (!is_file($legacyPublic)) {
            abort(500, 'Legacy application entrypoint not found.');
        }

        $_SERVER['REQUEST_METHOD'] = $request->method();
        $_SERVER['REQUEST_URI'] = $request->getRequestUri();
        $_SERVER['QUERY_STRING'] = $request->server('QUERY_STRING', '');
        $_SERVER['HTTP_HOST'] = $request->getHost();
        $_GET = $request->query->all();
        $_POST = $request->request->all();
        $_FILES = $request->files->all();

        if (!defined('LARAVEL_BRIDGE_MODE')) {
            define('LARAVEL_BRIDGE_MODE', true);
        }

        ob_start();
        try {
            require $legacyPublic;
        } catch (RuntimeException $e) {
            if (!str_starts_with($e->getMessage(), '__LEGACY_REDIRECT__:')) {
                throw $e;
            }
            $location = substr($e->getMessage(), strlen('__LEGACY_REDIRECT__:')) ?: '/';
            // Persist the legacy PHP session BEFORE we discard headers, so the new PHPSESSID
            // queued by session_regenerate_id() is committed to disk.
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            // Clear ONLY the Location header set by the legacy app — keep Set-Cookie (PHPSESSID)
            // and any other native headers so the browser receives the rotated session id.
            header_remove('Location');
            ob_end_clean();

            return redirect($location);
        }
        $content = ob_get_clean();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        return response($content ?? '');
    }
}
