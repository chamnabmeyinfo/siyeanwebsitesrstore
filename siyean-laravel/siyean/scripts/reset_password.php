<?php

declare(strict_types=1);

use App\Database;
use App\PasswordPolicy;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

$options = getopt('', ['email:', 'password:']);

$email = $options['email'] ?? null;
$password = $options['password'] ?? null;

if (!$email || !$password) {
    fwrite(STDERR, sprintf(
        "Usage: php scripts/reset_password.php --email=\"admin@example.com\" --password=\"<min %d chars, mixed case, with a digit>\"\n",
        PasswordPolicy::MIN_LENGTH
    ));
    exit(1);
}

$policyError = PasswordPolicy::validate($password);
if ($policyError !== null) {
    fwrite(STDERR, $policyError . "\n");
    exit(1);
}

$repo = new UserRepository(Database::connection());

if (!$repo->updatePassword($email, $password)) {
    fwrite(STDERR, "No user found with email {$email}.\n");
    if ($repo->count() === 0) {
        fwrite(STDERR, "There are no users yet — run scripts/create_user.php instead.\n");
    }
    exit(1);
}

echo "Password updated for {$email}." . PHP_EOL;
