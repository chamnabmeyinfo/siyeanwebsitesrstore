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

    public function updatePassword(string $email, string $newPassword): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }
        if ($newPassword === '') {
            throw new InvalidArgumentException('Password cannot be empty.');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'UPDATE users SET password_hash = :hash WHERE LOWER(email) = LOWER(:email)'
        );
        $stmt->execute([
            ':hash' => $hash,
            ':email' => $email,
        ]);

        return $stmt->rowCount() > 0;
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

    /**
     * @return list<array{id:int|string,name:string,email:string,role:string,created_at:string}>
     */
    public function listUsers(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, email, role, created_at FROM users ORDER BY id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

