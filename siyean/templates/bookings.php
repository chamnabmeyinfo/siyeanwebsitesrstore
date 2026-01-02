<section class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <div>
      <p class="chip">Bookings</p>
      <h2 style="margin:0;">Online reservations</h2>
    </div>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Preferred</th>
        <th>Status</th>
        <th>Deposit</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($bookings)): ?>
        <tr><td colspan="9">No bookings yet.</td></tr>
      <?php else: ?>
        <?php foreach ($bookings as $booking): ?>
          <tr>
            <td>#<?= (int) $booking['id'] ?></td>
            <td>
              <?= htmlspecialchars($booking['customer_name']) ?><br />
              <small><?= htmlspecialchars($booking['customer_email']) ?></small>
            </td>
            <td><?= htmlspecialchars($booking['customer_phone']) ?></td>
            <td><?= htmlspecialchars($booking['model']) ?> (<?= htmlspecialchars($booking['sku']) ?>)</td>
            <td><?= (int) $booking['quantity'] ?></td>
            <td>
              <?= htmlspecialchars($booking['preferred_date'] ?: '—') ?><br />
              <?= htmlspecialchars($booking['preferred_time'] ?: '') ?>
            </td>
            <td><?= strtoupper($booking['status']) ?></td>
            <td>$<?= number_format((float) $booking['deposit_amount'], 2) ?></td>
            <td>
              <form method="post" action="/bookings/status" style="display:flex;gap:0.4rem;align-items:center;">
                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>" />
                <select name="status">
                  <?php foreach (['pending','confirmed','picked_up','cancelled'] as $status): ?>
                    <option value="<?= $status ?>" <?= $status === $booking['status'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="ghost-btn">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</section>

