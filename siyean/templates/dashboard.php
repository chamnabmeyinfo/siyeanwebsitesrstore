<?php
/**
 * @var array $summary
 * @var array $inventory
 */
$hasLiveData =
    !empty($inventory)
    || (int) ($summary['count'] ?? 0) > 0
    || (float) ($summary['revenue'] ?? 0) > 0.0;

$demoSummary = [
    'count' => 12,
    'units' => 18,
    'revenue' => 28490.0,
    'avg_ticket' => 2374.17,
];
$demoInventory = [
    [
        'sku' => 'MBP-14-M4-512',
        'model' => 'MacBook Pro 14" M4',
        'storage_capacity' => 512,
        'quantity_on_hand' => 4,
    ],
    [
        'sku' => 'MBA-13-M3-256',
        'model' => 'MacBook Air 13" M3',
        'storage_capacity' => 256,
        'quantity_on_hand' => 6,
    ],
    [
        'sku' => 'iM-24-M4-512',
        'model' => 'iMac 24" M4',
        'storage_capacity' => 512,
        'quantity_on_hand' => 2,
    ],
    [
        'sku' => 'ACC-USBC-2M',
        'model' => 'USB-C cable 2m',
        'storage_capacity' => 0,
        'quantity_on_hand' => 24,
    ],
    [
        'sku' => 'PC-ULTRA-1TB',
        'model' => 'PC Workstation Ultra',
        'storage_capacity' => 1024,
        'quantity_on_hand' => 1,
    ],
];

$showDemoRibbon = !$hasLiveData;
$displaySummary = $showDemoRibbon ? $demoSummary : $summary;
$displayInventory = $showDemoRibbon ? $demoInventory : $inventory;
?>
<div class="dashboard">
  <?php if ($showDemoRibbon): ?>
    <div class="demo-ribbon" role="status">
      <span aria-hidden="true">✦</span>
      <span><strong>Demo data</strong> — sample figures and stock so you can preview the dashboard. Add inventory and record sales to see live numbers here.</span>
    </div>
  <?php endif; ?>

  <section class="dashboard-hero" aria-labelledby="dashboard-hero-title">
    <div class="dashboard-hero-copy">
      <p class="chip">SR Mac Shop · Point of sale</p>
      <h2 id="dashboard-hero-title">Run the floor with a clear daily picture.</h2>
      <p class="lead">
        Track Mac and PC stock, log walk-in sales, and keep bookings and revenue in one place. Use the quick actions to add SKUs or record a sale.
      </p>
      <div class="hero-actions" style="margin-top:1rem;">
        <a href="/inventory/new" style="text-decoration:none;"><button type="button">Add inventory</button></a>
        <a href="/sales/new" style="text-decoration:none;"><button type="button" class="ghost-btn">New sale</button></a>
      </div>
    </div>
    <div class="dashboard-hero-visual" aria-hidden="true"></div>
  </section>

  <section class="stat-grid" aria-label="Today at a glance">
    <div class="stat-card<?= $showDemoRibbon ? ' stat-muted' : '' ?>">
      <h3>Transactions (period)</h3>
      <p class="stat-value"><?= (int) $displaySummary['count'] ?></p>
    </div>
    <div class="stat-card<?= $showDemoRibbon ? ' stat-muted' : '' ?>">
      <h3>Units sold</h3>
      <p class="stat-value"><?= (int) $displaySummary['units'] ?></p>
    </div>
    <div class="stat-card<?= $showDemoRibbon ? ' stat-muted' : '' ?>">
      <h3>Revenue</h3>
      <p class="stat-value">$<?= number_format((float) $displaySummary['revenue'], 2) ?></p>
    </div>
    <div class="stat-card<?= $showDemoRibbon ? ' stat-muted' : '' ?>">
      <h3>Avg ticket</h3>
      <p class="stat-value">$<?= number_format((float) $displaySummary['avg_ticket'], 2) ?></p>
    </div>
  </section>

  <section class="pill-filters" aria-label="Quick filters (visual)">
    <span class="pill">MacBook</span>
    <span class="pill">iMac</span>
    <span class="pill">PC</span>
    <span class="pill">Accessories</span>
    <span class="pill">Low stock</span>
  </section>

  <section class="panel table-panel">
    <h3>Top inventory<?= $showDemoRibbon ? ' <span style="font-weight:500;color:var(--muted);font-size:0.85rem;">(demo sample)</span>' : '' ?></h3>
    <div class="table-wrap">
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
          <?php if (empty($displayInventory)): ?>
            <tr><td colspan="4">No inventory rows yet.</td></tr>
          <?php else: ?>
            <?php foreach ($displayInventory as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['sku']) ?></td>
                <td><?= htmlspecialchars($item['model']) ?></td>
                <td><?= (int) $item['storage_capacity'] > 0 ? (int) $item['storage_capacity'] . ' GB' : '—' ?></td>
                <td><?= (int) $item['quantity_on_hand'] ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
