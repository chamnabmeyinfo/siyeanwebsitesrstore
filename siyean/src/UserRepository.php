<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function create(string $name, string $email, string $password, string $role = 'admin'): int
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, :role)'
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => strtolower($email),
            ':hash' => $hash,
            ':role' => $role,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}

