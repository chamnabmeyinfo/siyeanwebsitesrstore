<?php

declare(strict_types=1);

use App\Database;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

$options = getopt('', ['name:', 'email:', 'password:', 'role::']);

$name = $options['name'] ?? null;
$email = $options['email'] ?? null;
$password = $options['password'] ?? null;
$role = $options['role'] ?? 'admin';

if (!$name || !$email || !$password) {
    fwrite(STDERR, "Usage: php scripts/create_user.php --name=\"Admin\" --email=\"admin@example.com\" --password=\"secret\" [--role=admin]\n");
    exit(1);
}

$repo = new UserRepository(Database::connection());

if ($repo->findByEmail($email)) {
    fwrite(STDERR, "User with email {$email} already exists.\n");
    exit(1);
}

$repo->create($name, $email, $password, $role);
echo "User {$email} created with role {$role}." . PHP_EOL;

