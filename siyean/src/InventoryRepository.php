<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;

final class InventoryRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function all(bool $onlyVisible = false): array
    {
        $sql = 'SELECT sku, slug, model, storage_capacity, color, cost_price, list_price, online_price, quantity_on_hand, hero_image, gallery_images, web_description, visible_online
             FROM inventory';
        if ($onlyVisible) {
            $sql .= ' WHERE visible_online = 1';
        }
        $sql .= ' ORDER BY model';
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO inventory (sku, slug, model, storage_capacity, color, cost_price, list_price, online_price, quantity_on_hand, hero_image, gallery_images, web_description, visible_online)
             VALUES (:sku, :slug, :model, :storage, :color, :cost, :list, :online, :qty, :hero, :gallery, :description, :visible)'
        );

        $stmt->execute($this->buildPayload($data));
    }

    public function update(string $originalSku, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE inventory SET
                sku = :sku,
                slug = :slug,
                model = :model,
                storage_capacity = :storage,
                color = :color,
                cost_price = :cost,
                list_price = :list,
                online_price = :online,
                quantity_on_hand = :qty,
                hero_image = :hero,
                gallery_images = :gallery,
                web_description = :description,
                visible_online = :visible
            WHERE sku = :original'
        );

        $payload = $this->buildPayload($data);
        $payload[':original'] = $originalSku;
        $stmt->execute($payload);

        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException("SKU {$originalSku} not found.");
        }
    }

    public function delete(string $sku): void
    {
        $stmt = $this->db->prepare('DELETE FROM inventory WHERE sku = :sku');
        $stmt->execute([':sku' => $sku]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException("SKU {$sku} not found.");
        }
    }

    public function adjustQuantity(string $sku, int $delta): void
    {
        $stmt = $this->db->prepare(
            'UPDATE inventory SET quantity_on_hand = quantity_on_hand + :delta WHERE sku = :sku'
        );
        $stmt->execute([':delta' => $delta, ':sku' => $sku]);

        if ($stmt->rowCount() === 0) {
            throw new InvalidArgumentException("SKU {$sku} not found.");
        }
    }

    public function findBySku(string $sku): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM inventory WHERE sku = :sku');
        $stmt->execute([':sku' => $sku]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function encodeGallery(array|string|null $input): ?string
    {
        if ($input === null) {
            return null;
        }
        if (is_string($input)) {
            $parts = array_filter(array_map('trim', preg_split('/[\n,]+/', $input)));
        } else {
            $parts = array_filter(array_map('trim', $input));
        }
        if (!$parts) {
            return null;
        }
        return json_encode(array_values($parts), JSON_UNESCAPED_SLASHES);
    }

    private function buildPayload(array $data): array
    {
        $slug = $data['slug'] ?? null;
        if (!$slug) {
            $slug = strtolower(str_replace(' ', '-', $data['sku']));
        }

        return [
            ':sku' => $data['sku'],
            ':slug' => $slug,
            ':model' => $data['model'],
            ':storage' => (int) $data['storage_capacity'],
            ':color' => $data['color'],
            ':cost' => (float) $data['cost_price'],
            ':list' => (float) $data['list_price'],
            ':online' => isset($data['online_price']) ? (float) $data['online_price'] : null,
            ':qty' => (int) $data['quantity_on_hand'],
            ':hero' => $data['hero_image'] ?? null,
            ':gallery' => $this->encodeGallery($data['gallery_images'] ?? []),
            ':description' => $data['web_description'] ?? null,
            ':visible' => isset($data['visible_online']) ? (int) $data['visible_online'] : 1,
        ];
    }
}

