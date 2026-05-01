<?php

declare(strict_types=1);

use App\Database;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

$options = getopt('', ['email:', 'password:']);

$email = $options['email'] ?? null;
$password = $options['password'] ?? null;

if (!$email || !$password) {
    fwrite(STDERR, "Usage: php scripts/reset_password.php --email=\"admin@example.com\" --password=\"new-secret\"\n");
    exit(1);
}

$repo = new UserRepository(Database::connection());

if (!$repo->updatePassword($email, $password)) {
    fwrite(STDERR, "No user found with email {$email}.\n");
    exit(1);
}

echo "Password updated for {$email}." . PHP_EOL;
