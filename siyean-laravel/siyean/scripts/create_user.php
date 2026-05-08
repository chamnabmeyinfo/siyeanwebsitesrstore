<?php

declare(strict_types=1);

use App\Database;
use App\PasswordPolicy;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

$options = getopt('', ['name:', 'email:', 'password:', 'role:']);

$name = $options['name'] ?? null;
$email = $options['email'] ?? null;
$password = $options['password'] ?? null;
$role = $options['role'] ?? null;

$allowedRoles = UserRepository::allowedRoles();

if (!$name || !$email || !$password || !$role) {
    fwrite(STDERR, sprintf(
        "Usage: php scripts/create_user.php --name=\"Owner\" --email=\"owner@example.com\" --password=\"<min %d chars, mixed case, with a digit>\" --role=<%s>\n",
        PasswordPolicy::MIN_LENGTH,
        implode('|', $allowedRoles)
    ));
    exit(1);
}

if (!in_array($role, $allowedRoles, true)) {
    fwrite(STDERR, sprintf(
        "Invalid role \"%s\". Allowed roles: %s.\n",
        $role,
        implode(', ', $allowedRoles)
    ));
    exit(1);
}

$policyError = PasswordPolicy::validate($password);
if ($policyError !== null) {
    fwrite(STDERR, $policyError . "\n");
    exit(1);
}

$repo = new UserRepository(Database::connection());

if ($repo->findByEmail($email)) {
    fwrite(STDERR, "User with email {$email} already exists.\n");
    exit(1);
}

$repo->create($name, $email, $password, $role);
echo "User {$email} created with role {$role}." . PHP_EOL;
