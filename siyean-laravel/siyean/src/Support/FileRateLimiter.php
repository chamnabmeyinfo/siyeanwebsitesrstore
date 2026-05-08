<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Tiny file-based rate limiter for the legacy PHP app.
 *
 * Intentionally has no schema and no library dependencies so the legacy
 * controller can ship hardening without coupling to Laravel's container or
 * touching SQLite. Replace with a proper Laravel RateLimiter call once the
 * legacy controllers move into Laravel.
 *
 * One small JSON file per key under storage/throttle/<sha1(key)>.json:
 *   { "first_at": <unix-ts>, "count": <int> }
 *
 * The counter resets when the rolling window expires. Stale files are pruned
 * opportunistically on each call to keep the directory small without a cron.
 */
final class FileRateLimiter
{
    private const STALE_AFTER_SECONDS = 86400;

    private string $directory;

    public function __construct(?string $directory = null)
    {
        $this->directory = $directory ?? __DIR__ . '/../../storage/throttle';
        if (!is_dir($this->directory)) {
            @mkdir($this->directory, 0775, true);
        }
    }

    /**
     * Number of attempts the given key has used in the current window.
     * If the window has expired, the counter is treated as zero.
     */
    public function attempts(string $key, int $windowSeconds): int
    {
        $path = $this->pathFor($key);
        $entry = $this->readEntry($path);
        if ($entry === null) {
            return 0;
        }
        if ((time() - $entry['first_at']) > $windowSeconds) {
            return 0;
        }

        return $entry['count'];
    }

    public function tooManyAttempts(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        return $this->attempts($key, $windowSeconds) >= $maxAttempts;
    }

    /**
     * Number of seconds until the key's window resets.
     * Zero when there is no active window.
     */
    public function availableIn(string $key, int $windowSeconds): int
    {
        $path = $this->pathFor($key);
        $entry = $this->readEntry($path);
        if ($entry === null) {
            return 0;
        }
        $elapsed = time() - $entry['first_at'];
        if ($elapsed >= $windowSeconds) {
            return 0;
        }

        return $windowSeconds - $elapsed;
    }

    /**
     * Increment the counter for $key. Starts a new window if none is active.
     * Returns the post-increment count.
     */
    public function hit(string $key, int $windowSeconds): int
    {
        $path = $this->pathFor($key);
        $entry = $this->readEntry($path);
        $now = time();

        if ($entry === null || ($now - $entry['first_at']) > $windowSeconds) {
            $entry = ['first_at' => $now, 'count' => 0];
        }
        $entry['count']++;

        @file_put_contents($path, json_encode($entry, JSON_THROW_ON_ERROR), LOCK_EX);
        $this->pruneStale();

        return $entry['count'];
    }

    public function clear(string $key): void
    {
        $path = $this->pathFor($key);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function pathFor(string $key): string
    {
        return $this->directory . '/' . sha1($key) . '.json';
    }

    /**
     * @return array{first_at:int,count:int}|null
     */
    private function readEntry(string $path): ?array
    {
        if (!is_file($path)) {
            return null;
        }
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return null;
        }
        try {
            $decoded = json_decode($raw, true, 8, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }
        if (!is_array($decoded)
            || !isset($decoded['first_at'], $decoded['count'])
            || !is_int($decoded['first_at'])
            || !is_int($decoded['count'])
        ) {
            return null;
        }

        return $decoded;
    }

    /**
     * Best-effort pruning of throttle files older than 24h. Failures are silent
     * because rate limiting must continue even if cleanup hits a permission
     * error (a stale file just behaves like an expired window).
     */
    private function pruneStale(): void
    {
        $cutoff = time() - self::STALE_AFTER_SECONDS;
        $files = @scandir($this->directory);
        if ($files === false) {
            return;
        }
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $full = $this->directory . '/' . $file;
            $mtime = @filemtime($full);
            if ($mtime !== false && $mtime < $cutoff) {
                @unlink($full);
            }
        }
    }
}
