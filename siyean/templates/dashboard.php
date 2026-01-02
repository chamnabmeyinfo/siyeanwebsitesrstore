<section class="hero-banner panel">
  <div>
    <p class="chip">Premium Macs & PCs</p>
    <h2>Sell more devices with clarity.</h2>
    <p style="color:#cbd5f5;max-width:420px;">
      Track MacBook, PC, and accessory stock, convert walk-ins fast, and keep an eye on your daily revenue.
    </p>
    <div class="hero-actions">
      <a href="/inventory" style="text-decoration:none;"><button>Add Mac SKU</button></a>
      <a href="/sales/new" style="text-decoration:none;"><button class="ghost-btn">Record Sale</button></a>
    </div>
  </div>
</section>

<div class="grid" style="margin-top:1.5rem;">
  <div class="card">
    <h3>Sales Today</h3>
    <p><?= $summary['count'] ?></p>
  </div>
  <div class="card">
    <h3>Units Sold</h3>
    <p><?= $summary['units'] ?></p>
  </div>
  <div class="card">
    <h3>Revenue</h3>
    <p>$<?= number_format($summary['revenue'], 2) ?></p>
  </div>
  <div class="card">
    <h3>Avg Ticket</h3>
    <p>$<?= number_format($summary['avg_ticket'], 2) ?></p>
  </div>
</div>

<div class="pill-filters">
  <span class="pill">MacBook</span>
  <span class="pill">PC</span>
  <span class="pill">Accessories</span>
  <span class="pill">Low Stock</span>
</div>

<div class="panel" style="margin-top:1rem;">
<h3 style="margin:0 0 1rem;">Top Inventory</h3>
<table>
  <thead>
    <tr>
      <th>SKU</th>
      <th>Model</th>
      <th>Storage</th>
      <th>Qty</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($inventory)): ?>
      <tr><td colspan="4">No inventory yet.</td></tr>
    <?php else: ?>
      <?php foreach ($inventory as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['sku']) ?></td>
          <td><?= htmlspecialchars($item['model']) ?></td>
          <td><?= (int) $item['storage_capacity'] ?> GB</td>
          <td><?= (int) $item['quantity_on_hand'] ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
</div>

