<section class="panel">
  <p class="chip">Import</p>
  <h2 style="margin:0.2rem 0 1rem;">Bulk import inventory</h2>
  <p style="color:var(--muted);max-width:520px;">
    Upload a CSV containing the columns: <code>sku, model, storage_capacity, color, cost_price, list_price, quantity_on_hand, hero_image, gallery_images</code>.
    Existing SKUs will be updated; new SKUs will be created.
  </p>
  <form method="post" action="/inventory/import" enctype="multipart/form-data">
    <label>CSV file
      <input type="file" name="csv" accept=".csv,text/csv" required />
    </label>
    <div style="margin-top:1rem;">
      <button type="submit">Upload &amp; import</button>
      <a href="/inventory" class="link-btn">Cancel</a>
      <a href="/inventory/export" class="link-btn">Download current CSV</a>
    </div>
  </form>
</section>

