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

    public function createPasswordResetToken(string $email, string $token, string $expiresAt): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }
        if ($token === '') {
            throw new InvalidArgumentException('Token cannot be empty.');
        }

        if (!$this->findByEmail($email)) {
            return false;
        }

        $delete = $this->db->prepare('DELETE FROM user_password_resets WHERE LOWER(email) = LOWER(:email)');
        $delete->execute([':email' => $email]);

        $insert = $this->db->prepare(
            'INSERT INTO user_password_resets (email, token_hash, expires_at) VALUES (:email, :token_hash, :expires_at)'
        );
        $insert->execute([
            ':email' => strtolower($email),
            ':token_hash' => hash('sha256', $token),
            ':expires_at' => $expiresAt,
        ]);

        return true;
    }

    public function isPasswordResetTokenValid(string $email, string $token): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $token === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            'SELECT token_hash
             FROM user_password_resets
             WHERE LOWER(email) = LOWER(:email)
               AND expires_at > CURRENT_TIMESTAMP
             ORDER BY created_at DESC
             LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        if (!$row || !isset($row['token_hash'])) {
            return false;
        }

        return hash_equals((string) $row['token_hash'], hash('sha256', $token));
    }

    public function clearPasswordResetTokens(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $stmt = $this->db->prepare('DELETE FROM user_password_resets WHERE LOWER(email) = LOWER(:email)');
        $stmt->execute([':email' => $email]);
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

