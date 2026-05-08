<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/**
 * Single source of truth for staff/POS password complexity rules.
 *
 * Mirrors the website (Laravel) policy as closely as we can without making
 * outbound HTTP calls (Laravel's Password::uncompromised() is the only piece
 * we cannot reproduce here, on purpose: this class must remain dependency-free).
 */
final class PasswordPolicy
{
    public const MIN_LENGTH = 12;

    /**
     * @return string|null Null when the password is acceptable, a human-readable
     *                    error message otherwise.
     */
    public static function validate(string $password): ?string
    {
        if (strlen($password) < self::MIN_LENGTH) {
            return sprintf('Password must be at least %d characters.', self::MIN_LENGTH);
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'Password must contain at least one lowercase letter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Password must contain at least one uppercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'Password must contain at least one digit.';
        }

        return null;
    }

    /**
     * Throws if the password is not acceptable. Convenience for code paths that
     * surface the message directly to the caller (CLI, repository).
     */
    public static function assert(string $password): void
    {
        $error = self::validate($password);
        if ($error !== null) {
            throw new InvalidArgumentException($error);
        }
    }
}
