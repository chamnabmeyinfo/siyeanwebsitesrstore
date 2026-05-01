<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;

final class StoreMenuRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Ordered rows for the public shop header (active only).
     *
     * @return list<array{id:int,label:string,href:string,sort_order:int,is_active:int}>
     */
    public function visibleOrdered(): array
    {
        $stmt = $this->db->query(
            'SELECT id, label, href, sort_order, is_active FROM store_menu_items
             WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        );

        return $stmt->fetchAll() ?: [];
    }

    /**
     * @return list<array{id:int,label:string,href:string,sort_order:int,is_active:int}>
     */
    public function allForAdmin(): array
    {
        $stmt = $this->db->query(
            'SELECT id, label, href, sort_order, is_active FROM store_menu_items
             ORDER BY sort_order ASC, id ASC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, label, href, sort_order, is_active FROM store_menu_items WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(string $label, string $href, int $sortOrder, bool $active): int
    {
        $this->assertValidLabel($label);
        $this->assertValidHref($href);

        $stmt = $this->db->prepare(
            'INSERT INTO store_menu_items (label, href, sort_order, is_active)
             VALUES (:label, :href, :sort_order, :active)'
        );
        $stmt->execute([
            ':label' => $label,
            ':href' => $href,
            ':sort_order' => $sortOrder,
            ':active' => $active ? 1 : 0,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $label, string $href, int $sortOrder, bool $active): void
    {
        $this->assertValidLabel($label);
        $this->assertValidHref($href);

        $stmt = $this->db->prepare(
            'UPDATE store_menu_items SET
                label = :label,
                href = :href,
                sort_order = :sort_order,
                is_active = :active
             WHERE id = :id'
        );
        $stmt->execute([
            ':label' => $label,
            ':href' => $href,
            ':sort_order' => $sortOrder,
            ':active' => $active ? 1 : 0,
            ':id' => $id,
        ]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Menu item not found.');
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM store_menu_items WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException('Menu item not found.');
        }
    }

    private function assertValidLabel(string $label): void
    {
        $t = trim($label);
        if ($t === '' || strlen($t) > 120) {
            throw new InvalidArgumentException('Label must be 1–120 characters.');
        }
    }

    private function assertValidHref(string $href): void
    {
        $h = trim($href);
        if ($h === '' || strlen($h) > 2048) {
            throw new InvalidArgumentException('Link URL is invalid or too long.');
        }
        if (preg_match('/^\s*javascript:/i', $h) === 1) {
            throw new InvalidArgumentException('Invalid link URL.');
        }
        if (preg_match('#^(https?://|mailto:|tel:|/)#i', $h) === 1) {
            return;
        }
        throw new InvalidArgumentException('Link must start with /, http://, https://, mailto:, or tel:.');
    }
}
