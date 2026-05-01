<?php
$editing = ($mode ?? 'create') === 'edit';
$record = $item ?? null;
$galleryText = '';
if ($editing && !empty($record['gallery_images'])) {
    $decoded = json_decode((string) $record['gallery_images'], true);
    if (is_array($decoded)) {
        $galleryText = implode("\n", $decoded);
    }
}
$action = $editing ? '/inventory/update' : '/inventory/store';
?>

<section class="panel">
  <p class="chip"><?= $editing ? 'Edit SKU' : 'Add SKU' ?></p>
  <h2 style="margin:0.2rem 0 1rem;"><?= $editing ? 'Update inventory item' : 'Create new inventory item' ?></h2>
  <form method="post" action="<?= $action ?>">
    <?php if ($editing): ?>
      <input type="hidden" name="original_sku" value="<?= htmlspecialchars($record['sku']) ?>" />
    <?php endif; ?>
    <label>SKU
      <input name="sku" value="<?= htmlspecialchars($record['sku'] ?? '') ?>" required />
    </label>
    <label>Model
      <input name="model" value="<?= htmlspecialchars($record['model'] ?? '') ?>" required />
    </label>
    <label>Slug (for storefront URL)
      <input name="slug" value="<?= htmlspecialchars($record['slug'] ?? '') ?>" placeholder="macbook-pro-14-m4" />
    </label>
    <label>Storage (GB)
      <input type="number" name="storage_capacity" min="1" value="<?= htmlspecialchars($record['storage_capacity'] ?? 0) ?>" required />
    </label>
    <label>Color
      <input name="color" value="<?= htmlspecialchars($record['color'] ?? '') ?>" required />
    </label>
    <label>Cost Price
      <input type="number" step="0.01" name="cost_price" value="<?= htmlspecialchars($record['cost_price'] ?? 0) ?>" required />
    </label>
    <label>List Price
      <input type="number" step="0.01" name="list_price" value="<?= htmlspecialchars($record['list_price'] ?? 0) ?>" required />
    </label>
    <label>Online Price
      <input type="number" step="0.01" name="online_price" value="<?= htmlspecialchars($record['online_price'] ?? '') ?>" placeholder="Leave blank to reuse list price" />
    </label>
    <label>On-hand Quantity
      <input type="number" name="quantity" value="<?= htmlspecialchars($record['quantity_on_hand'] ?? 0) ?>" />
    </label>
    <label>Hero Image URL
      <input type="url" name="hero_image" value="<?= htmlspecialchars($record['hero_image'] ?? '') ?>" placeholder="https://cdn.example.com/macbook.jpg" />
    </label>
    <label>Gallery URLs (one per line)
      <textarea name="gallery_images" rows="4" placeholder="https://cdn.example.com/mac-1.jpg&#10;https://cdn.example.com/mac-2.jpg"><?= htmlspecialchars($galleryText) ?></textarea>
    </label>
    <label>Storefront Description
      <textarea name="web_description" rows="5" placeholder="Key selling points, condition, warranty info"><?= htmlspecialchars($record['web_description'] ?? '') ?></textarea>
    </label>
    <label>
      <input type="hidden" name="visible_online" value="0" />
      <input type="checkbox" name="visible_online" value="1" <?= isset($record['visible_online']) ? ((int) $record['visible_online'] === 1 ? 'checked' : '') : 'checked' ?> />
      Show on website
    </label>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
      <button type="submit"><?= $editing ? 'Save changes' : 'Create SKU' ?></button>
      <a href="/inventory" class="link-btn">Back to inventory</a>
    </div>
  </form>
</section>

