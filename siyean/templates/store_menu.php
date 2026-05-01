<?php
/** @var list<array<string, mixed>> $items */
?>
<section class="panel">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
    <div>
      <p class="chip">Storefront</p>
      <h2 style="margin:0;">Shop header menu</h2>
      <p style="color:var(--muted);max-width:42rem;margin:0.5rem 0 0;font-size:0.92rem;">
        These links power the public shop navigation. Use paths like <code>/</code>,
        <code>/#inventory-list</code>, <code>/login</code>, or external <code>https://…</code>,
        <code>mailto:</code>, <code>tel:</code>.
      </p>
    </div>
  </div>

  <?php if (empty($items)): ?>
    <p style="margin-top:1.25rem;color:var(--muted);">No links yet. Add one below.</p>
  <?php else: ?>
    <?php foreach ($items as $row): ?>
      <div class="panel" style="margin-top:1rem;padding:1rem;background:var(--card-bg);border:1px solid var(--panel-border);border-radius:12px;">
        <form method="post" action="/settings/store-menu/update" style="display:grid;gap:0.75rem;">
          <input type="hidden" name="menu_id" value="<?= (int) $row['id'] ?>" />
          <div style="display:grid;grid-template-columns:1fr 2fr;gap:0.75rem;align-items:end;">
            <label style="margin:0;">Label
              <input type="text" name="label" value="<?= htmlspecialchars((string) $row['label']) ?>" required />
            </label>
            <label style="margin:0;">URL
              <input type="text" name="href" value="<?= htmlspecialchars((string) $row['href']) ?>" required />
            </label>
          </div>
          <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:center;">
            <label style="margin:0;">Sort
              <input type="number" name="sort_order" value="<?= (int) $row['sort_order'] ?>" style="width:6rem;" />
            </label>
            <label style="display:flex;align-items:center;gap:0.4rem;margin:0;">
              <input type="checkbox" name="is_active" value="1" <?= ((int) $row['is_active'] === 1) ? 'checked' : '' ?> />
              <span>Visible on site</span>
            </label>
            <button type="submit">Save changes</button>
          </div>
        </form>
        <form method="post" action="/settings/store-menu/delete" style="margin-top:0.75rem;" onsubmit="return confirm('Remove this menu link?');">
          <input type="hidden" name="menu_id" value="<?= (int) $row['id'] ?>" />
          <button type="submit" class="ghost-btn danger">Delete link</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="panel" style="margin-top:1.5rem;padding:1rem;background:var(--card-bg);border:1px solid var(--panel-border);border-radius:12px;">
    <h3 style="margin-top:0;font-size:1rem;">Add link</h3>
    <form method="post" action="/settings/store-menu/create" style="display:grid;gap:0.75rem;max-width:36rem;">
      <label>Label
        <input type="text" name="label" placeholder="Contact" required />
      </label>
      <label>URL
        <input type="text" name="href" placeholder="/contact or https://example.com" required />
      </label>
      <label>Sort order
        <input type="number" name="sort_order" value="100" />
      </label>
      <label style="display:flex;align-items:center;gap:0.5rem;">
        <input type="checkbox" name="is_active" value="1" checked />
        <span>Visible on site</span>
      </label>
      <div><button type="submit">Add link</button></div>
    </form>
  </div>
</section>
