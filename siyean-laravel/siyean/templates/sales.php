<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <div>
      <p class="chip">Performance</p>
      <h2 style="margin:0;">Sales</h2>
      <p style="color:#94a3b8;">Review every Mac/PC transaction and spot margin opportunities.</p>
    </div>
    <div style="text-align:right;">
      <div style="font-size:2rem;font-weight:600;">$<?= number_format($summary['revenue'], 2) ?></div>
      <div style="color:#94a3b8;"><?= $summary['units'] ?> units • <?= $summary['count'] ?> sales</div>
    </div>
  </div>
</div>

<div class="panel" style="margin-top:1rem;">
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Date</th>
      <th>SKU</th>
      <th>Customer</th>
      <th>Qty</th>
      <th>Unit Price</th>
      <th>Discount</th>
      <th>Tax %</th>
      <th>Payment</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($rows)): ?>
      <tr><td colspan="9">No sales recorded yet.</td></tr>
    <?php else: ?>
      <?php foreach ($rows as $sale): ?>
        <tr>
          <td><?= (int) $sale['id'] ?></td>
          <td><?= htmlspecialchars($sale['sold_at']) ?></td>
          <td><?= htmlspecialchars($sale['sku']) ?></td>
          <td><?= htmlspecialchars($sale['customer_name']) ?></td>
          <td><?= (int) $sale['quantity'] ?></td>
          <td>$<?= number_format((float) $sale['unit_price'], 2) ?></td>
          <td>$<?= number_format((float) $sale['discount'], 2) ?></td>
          <td><?= number_format((float) $sale['tax_rate'], 2) ?>%</td>
          <td><?= htmlspecialchars($sale['payment_method']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
</div>

