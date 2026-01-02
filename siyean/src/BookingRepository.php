<?php

declare(strict_types=1);

namespace App;

use PDO;

final class BookingRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function create(array $payload): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bookings (
                inventory_id, customer_name, customer_email, customer_phone,
                quantity, deposit_amount, preferred_date, preferred_time, status, notes
            ) VALUES (
                :inventory_id, :customer_name, :customer_email, :customer_phone,
                :quantity, :deposit_amount, :preferred_date, :preferred_time, :status, :notes
            )'
        );
        $stmt->execute([
            ':inventory_id' => $payload['inventory_id'],
            ':customer_name' => $payload['customer_name'],
            ':customer_email' => $payload['customer_email'],
            ':customer_phone' => $payload['customer_phone'],
            ':quantity' => $payload['quantity'],
            ':deposit_amount' => $payload['deposit_amount'] ?? 0,
            ':preferred_date' => $payload['preferred_date'] ?? null,
            ':preferred_time' => $payload['preferred_time'] ?? null,
            ':status' => $payload['status'] ?? 'pending',
            ':notes' => $payload['notes'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findAll(string $status = null): array
    {
        $sql = 'SELECT b.*, i.model, i.sku FROM bookings b JOIN inventory i ON b.inventory_id = i.id';
        $params = [];
        if ($status) {
            $sql .= ' WHERE b.status = :status';
            $params[':status'] = $status;
        }
        $sql .= ' ORDER BY b.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, i.model, i.sku
             FROM bookings b
             JOIN inventory i ON b.inventory_id = i.id
             WHERE b.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

