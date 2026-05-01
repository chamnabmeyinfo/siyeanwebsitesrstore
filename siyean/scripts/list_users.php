<?php

declare(strict_types=1);

use App\Database;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

$users = (new UserRepository(Database::connection()))->listUsers();

if ($users === []) {
    echo "No users found." . PHP_EOL;
    exit(0);
}

$out = fopen('php://stdout', 'w');
fputcsv($out, ['id', 'name', 'email', 'role', 'created_at']);
foreach ($users as $row) {
    fputcsv($out, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['role'],
        $row['created_at'],
    ]);
}
fclose($out);
