<?php
/** @var list<array<string, mixed>> $items */
$idsInOrder = array_map(static fn ($r) => (int) $r['id'], $items);
$menuOrderInitial = implode(',', $idsInOrder);
?>
<section class="panel">
<style>
  .store-menu-screen {
    max-width: 1120px;
    margin: 0 auto;
  }
  .store-menu-screen .screen-title {
    margin: 0 0 0.35rem;
    font-size: 1.65rem;
    font-weight: 600;
    letter-spacing: -0.02em;
  }
  .store-menu-screen .screen-intro {
    margin: 0 0 1.5rem;
    color: var(--muted);
    font-size: 0.95rem;
    max-width: 52rem;
    line-height: 1.55;
  }
  .store-menu-columns {
    display: grid;
    grid-template-columns: minmax(260px, 320px) 1fr;
    gap: 1.5rem;
    align-items: start;
  }
  @media (max-width: 880px) {
    .store-menu-columns {
      grid-template-columns: 1fr;
    }
  }

  /* Sidebar — “Add menu items” (WordPress meta-box style) */
  .menu-add-panel {
    background: var(--panel-bg);
    border: 1px solid var(--panel-border);
    border-radius: 12px;
    box-shadow: var(--shadow-xs, 0 1px 2px rgba(0, 0, 0, 0.04));
    overflow: hidden;
  }
  .menu-add-panel__head {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid var(--panel-border);
    background: rgba(59, 130, 246, 0.06);
    font-weight: 600;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--chip-color, #93c5fd);
  }
  .menu-add-panel__body {
    padding: 1rem;
  }
  .menu-add-panel__section-title {
    margin: 0 0 0.5rem;
    font-size: 0.95rem;
    font-weight: 600;
  }
  .menu-add-panel__hint {
    margin: 0 0 1rem;
    font-size: 0.82rem;
    color: var(--muted);
    line-height: 1.45;
  }
  .menu-add-panel label {
    display: block;
    font-size: 0.82rem;
    font-weight: 500;
    margin-bottom: 0.35rem;
    color: var(--muted);
  }
  .menu-add-panel input[type="text"] {
    width: 100%;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0.65rem;
    border-radius: 8px;
    border: 1px solid var(--input-border);
    background: var(--input-bg);
    color: var(--text-color);
    font-size: 0.92rem;
  }
  .menu-add-panel .add-to-menu-btn {
    width: 100%;
    margin-top: 0.25rem;
    padding: 0.55rem 1rem;
    font-weight: 600;
    cursor: pointer;
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
  }
  .menu-add-panel .add-to-menu-btn:hover {
    filter: brightness(1.06);
  }
  .menu-add-panel .add-visible {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    margin: 0.75rem 0;
    font-size: 0.88rem;
    cursor: pointer;
  }

  /* Main — Menu structure */
  .menu-structure-head {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
  }
  .menu-structure-head h2 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
  }
  .menu-structure-help {
    margin: 0 0 1rem;
    font-size: 0.88rem;
    color: var(--muted);
    line-height: 1.5;
  }
  .menu-structure-list {
    list-style: none;
    margin: 0 0 1.25rem;
    padding: 0;
    min-height: 2rem;
  }
  .menu-structure-list.is-empty {
    padding: 2rem 1rem;
    text-align: center;
    border: 2px dashed var(--panel-border);
    border-radius: 12px;
    color: var(--muted);
    font-size: 0.92rem;
  }

  .menu-item-row {
    margin-bottom: 0.5rem;
    border: 1px solid var(--panel-border);
    border-radius: 10px;
    background: var(--card-bg);
    overflow: hidden;
  }
  .menu-item-row.is-dragging {
    opacity: 0.65;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }
  .menu-item-details > summary {
    list-style: none;
    cursor: pointer;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 0.65rem;
    align-items: center;
    padding: 0.55rem 0.75rem;
    font-size: 0.88rem;
    user-select: none;
  }
  .menu-item-details > summary::-webkit-details-marker {
    display: none;
  }
  .menu-item-details > summary::after {
    content: "▾";
    font-size: 0.75rem;
    opacity: 0.55;
    margin-left: 0.25rem;
  }
  .menu-item-details[open] > summary::after {
    transform: rotate(-180deg);
    display: inline-block;
  }

  .menu-drag-handle {
    cursor: grab;
    color: var(--muted);
    font-size: 1.1rem;
    line-height: 1;
    padding: 0.15rem 0.25rem;
    border-radius: 6px;
  }
  .menu-drag-handle:active {
    cursor: grabbing;
  }
  .menu-drag-handle:focus-visible {
    outline: 2px solid var(--chip-color);
    outline-offset: 2px;
  }

  .menu-item-titles {
    min-width: 0;
  }
  .menu-item-titles strong {
    display: block;
    font-weight: 600;
    font-size: 0.92rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .menu-item-titles span.url-preview {
    display: block;
    font-size: 0.78rem;
    color: var(--muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 0.15rem;
  }

  .menu-item-badge {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.2rem 0.45rem;
    border-radius: 999px;
    white-space: nowrap;
  }
  .menu-item-badge--live {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
  }
  .menu-item-badge--hidden {
    background: rgba(148, 163, 184, 0.2);
    color: var(--muted);
  }

  .menu-item-panel {
    padding: 0.85rem 1rem 1rem;
    border-top: 1px solid var(--panel-border);
    background: rgba(15, 23, 42, 0.25);
    display: grid;
    gap: 0.65rem;
  }
  :root[data-theme="light"] .menu-item-panel,
  :root[data-theme="system"][data-system-pref="light"] .menu-item-panel {
    background: rgba(248, 250, 252, 0.95);
  }
  .menu-item-panel label span {
    display: block;
    font-size: 0.78rem;
    font-weight: 500;
    color: var(--muted);
    margin-bottom: 0.25rem;
  }
  .menu-item-panel input[type="text"] {
    width: 100%;
    padding: 0.45rem 0.55rem;
    border-radius: 8px;
    border: 1px solid var(--input-border);
    background: var(--input-bg);
    color: var(--text-color);
    font-size: 0.88rem;
  }
  .menu-item-panel .menu-item-visible {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.88rem;
    cursor: pointer;
  }
  .menu-item-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
  }
  .menu-item-actions .move-btn {
    padding: 0.35rem 0.55rem;
    font-size: 0.78rem;
    border-radius: 6px;
    border: 1px solid var(--panel-border);
    background: var(--input-bg);
    color: var(--text-color);
    cursor: pointer;
  }
  .menu-item-actions .move-btn:hover {
    border-color: var(--chip-color);
  }

  .save-menu-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem;
    margin-top: 0.25rem;
    padding-top: 1rem;
    border-top: 1px solid var(--panel-border);
  }
  .save-menu-bar .btn-save-menu {
    padding: 0.55rem 1.35rem;
    font-weight: 700;
    font-size: 0.95rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    background: linear-gradient(135deg, #0d9488, #0f766e);
    color: #ecfdf5;
    box-shadow: 0 2px 8px rgba(13, 148, 136, 0.35);
  }
  .save-menu-bar .btn-save-menu:hover {
    filter: brightness(1.07);
  }
  .save-menu-bar .save-hint {
    font-size: 0.82rem;
    color: var(--muted);
  }
</style>

<div class="store-menu-screen">
  <h1 class="screen-title">Menus</h1>
  <p class="screen-intro">
    Edit the links that appear in your shop header. Add custom URLs below, drag items to set order (like WordPress
    <strong>Appearance → Menus</strong>), then click <strong>Save menu</strong>. Paths such as
    <code>/</code>, <code>/#inventory-list</code>, and <code>/login</code> work alongside <code>https://</code>,
    <code>mailto:</code>, and <code>tel:</code>.
  </p>

  <div class="store-menu-columns">
    <aside class="store-menu-sidebar" aria-label="Add menu items">
      <div class="menu-add-panel">
        <div class="menu-add-panel__head">Add menu items</div>
        <div class="menu-add-panel__body">
          <p class="menu-add-panel__section-title">Custom links</p>
          <p class="menu-add-panel__hint">
            Enter a URL and link text, then add it to your menu. You can reorder it in the structure panel.
          </p>
          <form method="post" action="/settings/store-menu/create" autocomplete="off">
            <label for="add-url">URL</label>
            <input id="add-url" type="text" name="href" placeholder="https://example.com or /contact" required />

            <label for="add-label">Link text</label>
            <input id="add-label" type="text" name="label" placeholder="Contact" required />

            <label class="add-visible">
              <input type="checkbox" name="is_active" value="1" checked />
              <span>Show on site</span>
            </label>

            <button type="submit" class="add-to-menu-btn">Add to menu</button>
          </form>
        </div>
      </div>
    </aside>

    <div class="store-menu-main">
      <div class="menu-structure-head">
        <h2>Menu structure</h2>
      </div>
      <p class="menu-structure-help">
        Drag <span aria-hidden="true">⠿</span> to reorder. Expand a row to edit the label, URL, or visibility.
        Remove sends you back here after confirmation — remember to <strong>Save menu</strong> after other edits.
      </p>

      <?php if (empty($items)): ?>
        <ul class="menu-structure-list is-empty" id="menu-structure-list">
          <li>No menu items yet. Use <strong>Custom links</strong> on the left to add your first entry.</li>
        </ul>
      <?php else: ?>
        <form id="store-menu-save-form" method="post" action="/settings/store-menu/save" class="menu-save-form">
          <input type="hidden" name="menu_order" id="menu-order-field" value="<?= htmlspecialchars($menuOrderInitial) ?>" />

          <ul class="menu-structure-list" id="menu-structure-list">
            <?php foreach ($items as $row): ?>
              <?php
              $mid = (int) $row['id'];
              $active = (int) ($row['is_active'] ?? 0) === 1;
              $labelEsc = htmlspecialchars((string) $row['label']);
              $hrefEsc = htmlspecialchars((string) $row['href']);
              ?>
              <li class="menu-item-row" data-id="<?= $mid ?>">
                <details class="menu-item-details">
                  <summary>
                    <span class="menu-drag-handle" draggable="true" title="Drag to reorder">⠿</span>
                    <span class="menu-item-titles">
                      <strong class="js-summary-label"><?= $labelEsc ?></strong>
                      <span class="url-preview js-summary-url"><?= $hrefEsc ?></span>
                    </span>
                    <?php if ($active): ?>
                      <span class="menu-item-badge menu-item-badge--live">Live</span>
                    <?php else: ?>
                      <span class="menu-item-badge menu-item-badge--hidden">Hidden</span>
                    <?php endif; ?>
                  </summary>

                  <div class="menu-item-panel">
                    <label>
                      <span>Navigation label</span>
                      <input
                        type="text"
                        name="items[<?= $mid ?>][label]"
                        value="<?= $labelEsc ?>"
                        required
                        class="js-field-label"
                        autocomplete="off"
                      />
                    </label>
                    <label>
                      <span>URL</span>
                      <input
                        type="text"
                        name="items[<?= $mid ?>][href]"
                        value="<?= $hrefEsc ?>"
                        required
                        class="js-field-href"
                        autocomplete="off"
                      />
                    </label>
                    <label class="menu-item-visible">
                      <input type="checkbox" name="items[<?= $mid ?>][is_active]" value="1" <?= $active ? 'checked' : '' ?> class="js-field-active" />
                      <span>Show on site</span>
                    </label>

                    <div class="menu-item-actions">
                      <button type="button" class="move-btn menu-move-up" aria-label="Move up">Move up</button>
                      <button type="button" class="move-btn menu-move-down" aria-label="Move down">Move down</button>
                      <button
                        type="submit"
                        class="ghost-btn danger"
                        formaction="/settings/store-menu/delete"
                        formmethod="post"
                        name="menu_id"
                        value="<?= $mid ?>"
                        onclick="return confirm('Remove this link from the menu?');"
                      >
                        Remove
                      </button>
                    </div>
                  </div>
                </details>
              </li>
            <?php endforeach; ?>
          </ul>

          <div class="save-menu-bar">
            <button type="submit" class="btn-save-menu">Save menu</button>
            <span class="save-hint">Publish order, labels, and visibility to the live shop header.</span>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
</section>

<?php if (!empty($items)): ?>
<script>
(function () {
  const list = document.getElementById("menu-structure-list");
  const orderField = document.getElementById("menu-order-field");
  const form = document.getElementById("store-menu-save-form");
  if (!list || !orderField || !form) return;

  let draggedRow = null;

  function syncOrder() {
    orderField.value = [...list.querySelectorAll("li.menu-item-row[data-id]")]
      .map((li) => li.getAttribute("data-id"))
      .join(",");
  }

  function syncSummaryFromInputs(li) {
    const labelIn = li.querySelector(".js-field-label");
    const hrefIn = li.querySelector(".js-field-href");
    const activeIn = li.querySelector(".js-field-active");
    const sLabel = li.querySelector(".js-summary-label");
    const sUrl = li.querySelector(".js-summary-url");
    const badge = li.querySelector(".menu-item-badge");
    if (labelIn && sLabel) sLabel.textContent = labelIn.value || "—";
    if (hrefIn && sUrl) sUrl.textContent = hrefIn.value || "—";
    if (badge && activeIn) {
      badge.textContent = activeIn.checked ? "Live" : "Hidden";
      badge.classList.toggle("menu-item-badge--live", activeIn.checked);
      badge.classList.toggle("menu-item-badge--hidden", !activeIn.checked);
    }
  }

  list.querySelectorAll(".menu-item-row").forEach((li) => {
    li.querySelectorAll(".js-field-label, .js-field-href, .js-field-active").forEach((el) => {
      el.addEventListener("input", () => syncSummaryFromInputs(li));
      el.addEventListener("change", () => syncSummaryFromInputs(li));
    });
  });

  function getDragAfterElement(container, y) {
    const rows = [...container.querySelectorAll("li.menu-item-row:not(.is-dragging)")];
    return rows.reduce(
      (closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child };
        }
        return closest;
      },
      { offset: Number.NEGATIVE_INFINITY, element: /** @type {HTMLElement|null} */ (null) }
    ).element;
  }

  list.addEventListener("dragstart", (e) => {
    const handle = e.target.closest(".menu-drag-handle");
    if (!handle || !list.contains(handle)) {
      e.preventDefault();
      return;
    }
    const row = handle.closest("li.menu-item-row");
    if (!row) return;
    draggedRow = row;
    row.classList.add("is-dragging");
    e.dataTransfer.effectAllowed = "move";
    try {
      e.dataTransfer.setData("text/plain", row.getAttribute("data-id") || "");
    } catch (_) {}
  });

  list.addEventListener("dragend", () => {
    if (draggedRow) draggedRow.classList.remove("is-dragging");
    draggedRow = null;
    syncOrder();
  });

  list.addEventListener("dragover", (e) => {
    e.preventDefault();
    if (!draggedRow) return;
    const after = getDragAfterElement(list, e.clientY);
    if (after == null) list.appendChild(draggedRow);
    else list.insertBefore(draggedRow, after);
  });

  form.addEventListener("submit", syncOrder);

  list.querySelectorAll(".menu-move-up").forEach((btn) => {
    btn.addEventListener("click", () => {
      const li = btn.closest("li.menu-item-row");
      if (!li || !li.previousElementSibling) return;
      li.parentNode.insertBefore(li, li.previousElementSibling);
      syncOrder();
    });
  });

  list.querySelectorAll(".menu-move-down").forEach((btn) => {
    btn.addEventListener("click", () => {
      const li = btn.closest("li.menu-item-row");
      if (!li || !li.nextElementSibling) return;
      li.parentNode.insertBefore(li.nextElementSibling, li);
      syncOrder();
    });
  });
})();
</script>
<?php endif; ?>
