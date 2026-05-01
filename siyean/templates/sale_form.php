<section class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <div>
      <p class="chip">Checkout Flow</p>
      <h2 style="margin:0.4rem 0;">Record New Sale</h2>
      <p style="color:#94a3b8;">Complete the sale in three quick steps: Product → Customer → Payment.</p>
    </div>
    <div class="pill-filters">
      <span class="pill">Mac</span>
      <span class="pill">PC</span>
      <span class="pill">Accessory</span>
    </div>
  </div>
  <form method="post" action="/sales/create" style="margin-top:1.5rem;">
    <h3>1 · Product</h3>
    <label>SKU
      <select name="sku" required>
        <?php foreach ($items as $item): ?>
          <option value="<?= htmlspecialchars($item['sku']) ?>">
            <?= htmlspecialchars($item['sku'] . ' • ' . $item['model']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Quantity<input type="number" name="quantity" min="1" value="1" /></label>
    <label>Unit Price<input type="number" step="0.01" name="unit_price" required /></label>
    <label>Discount<input type="number" step="0.01" name="discount" value="0" /></label>
    <label>Tax Rate (%)<input type="number" step="0.01" name="tax_rate" value="0" /></label>

    <h3 style="margin-top:1.5rem;">2 · Customer</h3>
    <label>Customer Name<input name="customer_name" required /></label>
    <label>Email<input type="email" name="customer_email" /></label>
    <label>Phone<input name="customer_phone" /></label>

    <h3 style="margin-top:1.5rem;">3 · Payment</h3>
    <label>Payment Method
      <select name="payment_method">
        <option value="cash">Cash</option>
        <option value="card">Card</option>
        <option value="transfer">Transfer</option>
      </select>
    </label>
    <label>Notes<textarea name="notes" rows="3"></textarea></label>
    <button type="submit">Save Sale</button>
  </form>
</section>

