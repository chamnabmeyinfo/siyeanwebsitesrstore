<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
    <div>
      <p class="chip">Catalog</p>
      <h2 style="margin:0;">Inventory</h2>
    </div>
    <div class="hero-actions">
      <a href="/inventory/new"><button>Add SKU</button></a>
      <a href="/inventory/import"><button class="ghost-btn">Import CSV</button></a>
      <a href="/inventory/export"><button class="ghost-btn">Export CSV</button></a>
    </div>
  </div>
  <div class="pill-filters">
    <span class="pill">MacBook</span>
    <span class="pill">PC</span>
    <span class="pill">Accessories</span>
    <span class="pill">Low Stock</span>
  </div>
  <table>
    <thead>
      <tr>
        <th>SKU</th>
        <th>Model</th>
        <th>Storage</th>
        <th>Color</th>
        <th>Cost</th>
        <th>List</th>
        <th>Qty</th>
        <th>Hero</th>
        <th>Gallery</th>
        <th>Online</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($items)): ?>
        <tr><td colspan="10">No inventory records yet.</td></tr>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <?php
            $galleryCount = 0;
            if (!empty($item['gallery_images'])) {
                $decoded = json_decode((string) $item['gallery_images'], true);
                if (is_array($decoded)) {
                    $galleryCount = count($decoded);
                }
            }
          ?>
          <tr>
            <td><?= htmlspecialchars($item['sku']) ?></td>
            <td><?= htmlspecialchars($item['model']) ?></td>
            <td><?= (int) $item['storage_capacity'] ?> GB</td>
            <td><?= htmlspecialchars($item['color']) ?></td>
            <td>$<?= number_format((float) $item['cost_price'], 2) ?></td>
            <td>$<?= number_format((float) $item['list_price'], 2) ?></td>
            <td><?= (int) $item['quantity_on_hand'] ?></td>
            <td>
              <?php if (!empty($item['hero_image'])): ?>
                <img src="<?= htmlspecialchars($item['hero_image']) ?>" alt="Hero image" class="inventory-thumb" loading="lazy" />
              <?php else: ?>
                <span class="muted">—</span>
              <?php endif; ?>
            </td>
            <td><?= $galleryCount ?></td>
          <td><?= ((int) ($item['visible_online'] ?? 1)) === 1 ? 'Visible' : 'Hidden' ?></td>
            <td>
              <a href="/inventory/edit?sku=<?= urlencode($item['sku']) ?>" class="link-btn">Edit</a>
              <form method="post" action="/inventory/delete" style="display:inline;" onsubmit="return confirm('Delete <?= htmlspecialchars($item['sku']) ?>?');">
                <input type="hidden" name="sku" value="<?= htmlspecialchars($item['sku']) ?>" />
                <button type="submit" class="ghost-btn danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<section style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1rem;">
  <form method="post" action="/inventory/adjust" class="panel">
    <h3>Quick quantity adjustment</h3>
    <label>SKU<input name="sku" required /></label>
    <label>Delta<input type="number" name="delta" required /></label>
    <button type="submit">Apply Adjustment</button>
  </form>
</section>

