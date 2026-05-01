<?php

declare(strict_types=1);

namespace App\Http;

final class ViewRenderer
{
    private string $requestPath = '/';

    public function __construct(private readonly AuthGate $auth)
    {
    }

    public function setRequestPath(string $path): void
    {
        $this->requestPath = $path === '' ? '/' : $path;
    }

    public function flash(string $status, string $message): void
    {
        $_SESSION['flash'] = compact('status', 'message');
    }

    public function redirect(string $path): void
    {
        header("Location: {$path}");
        if (defined('LARAVEL_BRIDGE_MODE') && LARAVEL_BRIDGE_MODE === true) {
            throw new \RuntimeException('__LEGACY_REDIRECT__:' . $path);
        }
        exit;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $layout = $data['layout'] ?? 'admin';
        unset($data['layout']);

        $currentUser = $this->auth->user();
        $request_path = $this->requestPath;

        $view = $this->basePath($template);
        extract($data, EXTR_SKIP);
        $layoutFile = match ($layout) {
            'store' => 'layout_store.php',
            'auth' => 'layout_auth.php',
            default => 'layout.php',
        };
        include $this->basePath($layoutFile);
    }

    private function basePath(string $template): string
    {
        return dirname(__DIR__, 2) . '/templates/' . $template;
    }
}
