<?php

declare(strict_types=1);

use App\Database;
use App\UserRepository;

require __DIR__ . '/../vendor/autoload.php';

$users = (new UserRepository(Database::connection()))->listUsers();

if ($users === []) {
    fwrite(STDERR, "No users in the SQLite database (siyean/storage/pos.db).\n");
    fwrite(STDERR, "Create the first account — do not use reset_password until a user exists:\n");
    fwrite(STDERR, '  php scripts/create_user.php --name="Owner" --email="you@example.com" --password="..." --role=admin' . PHP_EOL);
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
