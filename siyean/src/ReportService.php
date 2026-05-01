<?php

declare(strict_types=1);

namespace App;

use PDO;

final class ReportService
{
    public function __construct(private readonly PDO $db, private readonly SaleService $sales)
    {
    }

    public function inventorySnapshot(): array
    {
        $stmt = $this->db->query(
            'SELECT sku, model, storage_capacity, color, cost_price, list_price, quantity_on_hand
             FROM inventory ORDER BY quantity_on_hand DESC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function summary(?string $from = null, ?string $to = null): array
    {
        $rows = $this->sales->sales($from, $to);

        $units = 0;
        $revenue = 0.0;

        foreach ($rows as $row) {
            $units += (int) $row['quantity'];
            $revenue += $this->sales->total($row);
        }

        return [
            'count' => count($rows),
            'units' => $units,
            'revenue' => round($revenue, 2),
            'avg_ticket' => $units > 0 ? round($revenue / $units, 2) : 0,
            'rows' => $rows,
        ];
    }
}

