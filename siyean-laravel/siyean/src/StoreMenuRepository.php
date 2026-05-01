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

    public function maxSortOrder(): int
    {
        return (int) $this->db->query('SELECT COALESCE(MAX(sort_order), 0) FROM store_menu_items')->fetchColumn();
    }

    /**
     * Replace order and fields for all items in one transaction (WordPress-style “Save menu”).
     *
     * @param list<int> $orderedIds IDs top-to-bottom
     * @param array<int, array{label?:string,href?:string,is_active?:bool|string}> $itemsById
     */
    public function saveMenuStructure(array $orderedIds, array $itemsById): void
    {
        $existingIds = array_column($this->allForAdmin(), 'id');
        sort($existingIds);

        $cleanOrder = array_values(array_filter(array_map(static fn ($v) => (int) $v, $orderedIds), static fn (int $id) => $id > 0));
        $sortedIncoming = $cleanOrder;
        sort($sortedIncoming);

        if ($existingIds === [] && $sortedIncoming === []) {
            return;
        }

        if ($sortedIncoming !== $existingIds || count($cleanOrder) !== count($existingIds)) {
            throw new InvalidArgumentException('Menu items do not match the server. Refresh the page and try again.');
        }

        $this->db->beginTransaction();
        try {
            foreach ($cleanOrder as $position => $id) {
                $payload = $itemsById[$id] ?? null;
                if (!is_array($payload)) {
                    throw new InvalidArgumentException("Missing fields for menu item #{$id}.");
                }
                $label = trim((string) ($payload['label'] ?? ''));
                $href = trim((string) ($payload['href'] ?? ''));
                $active = !empty($payload['is_active']);
                $this->update($id, $label, $href, $position * 10, $active);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
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
