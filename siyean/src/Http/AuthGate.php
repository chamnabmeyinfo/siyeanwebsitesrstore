<?php

declare(strict_types=1);

namespace App\Http;

use App\UserRepository;

final class AuthGate
{
    private bool $resolved = false;

    /** @var array<string, mixed>|null */
    private ?array $cachedUser = null;

    public function __construct(private readonly UserRepository $users)
    {
    }

    /** @return array<string, mixed>|null */
    public function user(): ?array
    {
        if ($this->resolved) {
            return $this->cachedUser;
        }
        $this->resolved = true;
        if (!isset($_SESSION['user_id'])) {
            return $this->cachedUser = null;
        }
        $row = $this->users->findById((int) $_SESSION['user_id']);

        return $this->cachedUser = $row ?: null;
    }

    public function requireAuth(ViewRenderer $respond): void
    {
        if (!$this->user()) {
            $respond->redirect('/login');
        }
    }

    /**
     * @param list<string> $roles Empty = any authenticated role.
     */
    public function requireRole(ViewRenderer $respond, array $roles = []): void
    {
        $user = $this->user();
        if (!$user) {
            $respond->redirect('/login');
        }
        if ($roles && !in_array($user['role'], $roles, true)) {
            $respond->flash('error', 'You are not authorized to perform this action.');
            $respond->redirect('/dashboard');
        }
    }

    public function isPublicRoute(string $method, string $path): bool
    {
        if ($path === '/login' && in_array($method, ['GET', 'POST'], true)) {
            return true;
        }
        if ($path === '/') {
            return true;
        }
        if (str_starts_with($path, '/store')) {
            return true;
        }

        return false;
    }
}
