<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;

final class SaleService
{
    public function __construct(private readonly PDO $db, private readonly InventoryRepository $inventory)
    {
    }

    public function record(array $payload): void
    {
        $sku = $payload['sku'];
        $quantity = max(1, (int) $payload['quantity']);
        $item = $this->inventory->findBySku($sku);

        if ($item === null) {
            throw new InvalidArgumentException("SKU {$sku} not found.");
        }

        if ($item['quantity_on_hand'] < $quantity) {
            throw new InvalidArgumentException(
                "Not enough stock for {$sku}. Available {$item['quantity_on_hand']}."
            );
        }

        $this->db->beginTransaction();
        try {
            $customerId = $this->upsertCustomer(
                $payload['customer_name'],
                $payload['customer_email'] ?? null,
                $payload['customer_phone'] ?? null
            );

            $stmt = $this->db->prepare(
                'INSERT INTO sales (
                    inventory_id, customer_id, quantity, unit_price, discount,
                    tax_rate, payment_method, notes
                ) VALUES (:inventory_id, :customer_id, :quantity, :unit_price, :discount, :tax_rate, :payment, :notes)'
            );

            $stmt->execute([
                ':inventory_id' => $item['id'],
                ':customer_id' => $customerId,
                ':quantity' => $quantity,
                ':unit_price' => (float) $payload['unit_price'],
                ':discount' => (float) ($payload['discount'] ?? 0),
                ':tax_rate' => (float) ($payload['tax_rate'] ?? 0),
                ':payment' => $payload['payment_method'] ?? 'cash',
                ':notes' => $payload['notes'] ?? null,
            ]);

            $update = $this->db->prepare(
                'UPDATE inventory SET quantity_on_hand = quantity_on_hand - :qty WHERE id = :id'
            );
            $update->execute([':qty' => $quantity, ':id' => $item['id']]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function sales(?string $from = null, ?string $to = null): array
    {
        $clauses = [];
        $params = [];

        if ($from) {
            $clauses[] = 'sold_at >= :fromDate';
            $params[':fromDate'] = "{$from} 00:00:00";
        }

        if ($to) {
            $clauses[] = 'sold_at <= :toDate';
            $params[':toDate'] = "{$to} 23:59:59";
        }

        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        $sql = <<<SQL
        SELECT s.id, s.sold_at, s.quantity, s.unit_price, s.discount, s.tax_rate, s.payment_method,
               s.notes, i.sku, i.model, c.name AS customer_name
        FROM sales s
        JOIN inventory i ON s.inventory_id = i.id
        JOIN customers c ON s.customer_id = c.id
        {$where}
        ORDER BY s.sold_at DESC
        SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function total(array $sale): float
    {
        $subtotal = (float) $sale['unit_price'] * (int) $sale['quantity'];
        $afterDiscount = $subtotal - (float) $sale['discount'];
        $afterTax = $afterDiscount + ($afterDiscount * ((float) $sale['tax_rate'] / 100));
        return round($afterTax, 2);
    }

    private function upsertCustomer(string $name, ?string $email, ?string $phone): int
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM customers WHERE name = :name AND IFNULL(email, "") = IFNULL(:email, "") AND IFNULL(phone, "") = IFNULL(:phone, "")'
        );
        $stmt->execute([':name' => $name, ':email' => $email, ':phone' => $phone]);
        $row = $stmt->fetch();

        if ($row) {
            return (int) $row['id'];
        }

        $insert = $this->db->prepare('INSERT INTO customers (name, email, phone) VALUES (:name, :email, :phone)');
        $insert->execute([':name' => $name, ':email' => $email, ':phone' => $phone]);

        return (int) $this->db->lastInsertId();
    }
}

