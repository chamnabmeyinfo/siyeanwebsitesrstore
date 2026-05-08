<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use SQLite3;
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

        $this->bridgeOwnerSession();

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

    // If a Laravel owner is authenticated, inject their user_id into the PHP
    // native session so the legacy backend routes recognise them without a
    // separate POS login.
    private function bridgeOwnerSession(): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'owner') {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            return;
        }

        $dbPath = base_path('siyean/storage/pos.db');
        if (!is_file($dbPath)) {
            return;
        }

        $db = new SQLite3($dbPath, SQLITE3_OPEN_READONLY);
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->bindValue(1, strtolower($user->email));
        $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        $db->close();

        if ($row) {
            $_SESSION['user_id'] = $row['id'];
        }
    }
}
