<?php
/** @var array|null $flash */
$navPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

function nav_link_active(string $href, string $current): bool
{
    if ($href === '/') {
        return $current === '/';
    }
    if ($href === '/sales/new') {
        return str_starts_with($current, '/sales/new');
    }
    if ($href === '/sales') {
        return $current === '/sales';
    }

    return str_starts_with($current, $href);
}

function nav_attrs(string $href, string $current): string
{
    return nav_link_active($href, $current) ? ' class="active" aria-current="page"' : '';
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="system">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mac POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet" />
    <style>
      :root {
        font-family: "Space Grotesk", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--text-color);
        background: var(--body-bg);
      }
      :root,
      :root[data-theme="dark"],
      :root[data-theme="system"][data-system-pref="dark"],
      :root:not([data-theme]) {
        --body-bg: radial-gradient(circle at top, #18233a, #05070f 60%);
        --panel-bg: rgba(15, 23, 42, 0.65);
        --panel-border: rgba(59, 130, 246, 0.08);
        --text-color: #e2e8f0;
        --muted: #94a3b8;
        --card-bg: rgba(15, 23, 42, 0.75);
        --table-row-hover: rgba(15, 23, 42, 0.5);
        --input-bg: rgba(15, 23, 42, 0.65);
        --input-border: rgba(148, 163, 184, 0.3);
        --chip-bg: rgba(59, 130, 246, 0.15);
        --chip-color: #93c5fd;
      }
      :root[data-theme="light"],
      :root[data-theme="system"][data-system-pref="light"] {
        --body-bg: #f5f7fb;
        --panel-bg: #ffffff;
        --panel-border: rgba(15, 23, 42, 0.08);
        --text-color: #0f172a;
        --muted: #64748b;
        --card-bg: #ffffff;
        --table-row-hover: rgba(248, 250, 252, 0.9);
        --input-bg: #f8fafc;
        --input-border: rgba(15, 23, 42, 0.12);
        --chip-bg: rgba(59, 130, 246, 0.1);
        --chip-color: #2563eb;
      }
      body {
        margin: 0;
        min-height: 100vh;
        background: var(--body-bg);
        color: var(--text-color);
      }
      header {
        background: transparent;
      }
      .main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem clamp(1.5rem, 4vw, 3rem);
        margin: 1.2rem clamp(1.5rem, 4vw, 3rem) 0;
        border-radius: 1.2rem;
        background: linear-gradient(120deg, rgba(11, 18, 32, 0.95), rgba(37, 99, 235, 0.3));
        border: 1px solid rgba(59, 130, 246, 0.25);
        box-shadow: 0 25px 50px rgba(2, 6, 23, 0.5);
        gap: 1rem 1.25rem;
        flex-wrap: nowrap;
      }
      @media (max-width: 680px) {
        .main-header {
          flex-wrap: wrap;
        }
        .brand {
          flex: 1 1 auto;
          min-width: 0;
        }
        nav {
          flex: 1 1 100%;
          order: 3;
        }
        .user-chip {
          flex: 1 1 auto;
          justify-content: flex-end;
        }
      }
      :root[data-theme="light"] header {
        background: transparent;
      }
      :root[data-theme="light"] .main-header {
        background: linear-gradient(120deg, rgba(248, 250, 252, 0.95), rgba(226, 232, 240, 0.85));
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.12);
      }
      .brand {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-shrink: 0;
      }
      .brand-logo {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        overflow: hidden;
        background: radial-gradient(circle at 30% 30%, #ffffff, #cbd5f5);
        border: 1px solid rgba(255, 255, 255, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 8px rgba(255, 255, 255, 0.4);
      }
      .brand-logo img {
        width: 46px;
        height: 46px;
        object-fit: contain;
      }
      .brand h1 {
        margin: 0;
        font-size: 1.8rem;
      }
      .brand-tagline {
        margin: 0;
        color: var(--muted);
        font-size: 0.95rem;
      }
      .brand-badge {
        background: rgba(56, 189, 248, 0.15);
        color: #38bdf8;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.9rem;
        letter-spacing: 0.05em;
        border: 1px solid rgba(56, 189, 248, 0.4);
      }
      nav {
        flex: 1;
        min-width: 0;
      }
      .nav-links {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
        padding: 0.15rem 0;
        mask-image: linear-gradient(to right, transparent 0, #000 12px, #000 calc(100% - 12px), transparent 100%);
      }
      .nav-links::-webkit-scrollbar {
        height: 5px;
      }
      .nav-links::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.45);
        border-radius: 999px;
      }
      .nav-links a {
        flex: 0 0 auto;
        color: #e2e8f0;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.42rem 0.85rem;
        border-radius: 999px;
        border: 1px solid rgba(226, 232, 240, 0.15);
        background: rgba(15, 23, 42, 0.35);
        transition: background 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
      }
      .nav-links a:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
      }
      .nav-links a.active {
        background: rgba(59, 130, 246, 0.28);
        border-color: rgba(96, 165, 250, 0.55);
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.35);
      }
      :root[data-theme="light"] .nav-links a {
        color: #0f172a;
        background: rgba(15, 23, 42, 0.05);
        border-color: rgba(15, 23, 42, 0.08);
      }
      :root[data-theme="light"] .nav-links a.active {
        background: rgba(59, 130, 246, 0.14);
        border-color: rgba(37, 99, 235, 0.35);
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.12);
      }
      main {
        padding: 2.5rem clamp(1.5rem, 4vw, 4rem);
      }
      .flash {
        padding: 0.85rem 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.4);
      }
      .flash.success {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.25);
      }
      .flash.error {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.25);
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        color: var(--text-color);
      }
      th,
      td {
        padding: 0.85rem 0.5rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        text-align: left;
      }
      th {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #94a3b8;
      }
      table tr:hover td {
        background: var(--table-row-hover);
      }
      .panel {
        background: var(--panel-bg);
        backdrop-filter: blur(16px);
        padding: 1.25rem;
        border-radius: 1rem;
        box-shadow: 0 20px 45px rgba(2, 6, 23, 0.15);
        border: 1px solid var(--panel-border);
      }
      form.panel {
        margin-top: 1.25rem;
      }
      label {
        display: block;
        margin-top: 0.65rem;
        font-size: 0.9rem;
        color: var(--muted);
      }
      input,
      select,
      textarea {
        width: 100%;
        padding: 0.6rem 0.75rem;
        margin-top: 0.35rem;
        border: 1px solid var(--input-border);
        border-radius: 0.6rem;
        background: var(--input-bg);
        color: var(--text-color);
      }
      input:focus,
      select:focus,
      textarea:focus {
        outline: none;
        border-color: rgba(59, 130, 246, 0.6);
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.4);
      }
      button {
        margin-top: 1.25rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(120deg, #2563eb, #38bdf8);
        color: #fff;
        border: none;
        border-radius: 0.65rem;
        cursor: pointer;
        font-weight: 600;
        letter-spacing: 0.03em;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        box-shadow: 0 15px 35px rgba(37, 99, 235, 0.35);
      }
      button:hover {
        transform: translateY(-1px);
        box-shadow: 0 20px 40px rgba(56, 189, 248, 0.35);
      }
      .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.2rem;
      }
      .card {
        background: var(--card-bg);
        padding: 1.25rem;
        border-radius: 1rem;
        border: 1px solid rgba(59, 130, 246, 0.08);
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.07);
      }
      .card h3 {
        margin: 0;
        font-size: 0.9rem;
        color: var(--muted);
        letter-spacing: 0.04em;
        text-transform: uppercase;
      }
      .card p {
        font-size: 1.8rem;
        margin: 0.4rem 0 0;
        font-weight: 600;
      }
      .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        background: var(--chip-bg);
        color: var(--chip-color);
      }
      .hero-banner {
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
        padding: 1.5rem;
        background: radial-gradient(circle at top left, rgba(37, 99, 235, 0.3), rgba(2, 6, 23, 0.9));
        border-radius: 1.25rem;
        border: 1px solid rgba(15, 118, 110, 0.3);
      }
      :root[data-theme="light"] .hero-banner {
        background: radial-gradient(circle at top left, rgba(37, 99, 235, 0.15), rgba(248, 250, 252, 0.95));
        border-color: rgba(15, 23, 42, 0.08);
      }
      .hero-banner h2 {
        margin: 0;
        font-size: 1.6rem;
      }
      .hero-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
      }
      .ghost-btn {
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.4);
        box-shadow: none;
      }
      .ghost-btn:hover {
        border-color: rgba(59, 130, 246, 0.7);
      }
      .pill-filters {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin: 1rem 0;
      }
      .pill {
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: var(--text-color);
        font-size: 0.85rem;
      }
      .showroom-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        padding: clamp(2rem, 6vw, 3.5rem);
        background: radial-gradient(circle at 25% 20%, rgba(59, 130, 246, 0.35), transparent 55%),
          linear-gradient(135deg, #05070f, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.1);
        min-height: 360px;
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: center;
      }
      :root[data-theme="light"] .showroom-hero {
        background: linear-gradient(135deg, #f5f7fb, #e2e8f0);
        border-color: rgba(15, 23, 42, 0.08);
      }
      .showroom-hero .hero-content {
        flex: 1 1 320px;
        z-index: 1;
      }
      .showroom-hero h1 {
        font-size: clamp(2.5rem, 4vw, 3.5rem);
        margin: 0.3rem 0;
      }
      .showroom-hero p {
        color: var(--muted);
        max-width: 480px;
      }
      .showroom-hero .hero-media {
        flex: 1 1 320px;
        position: relative;
      }
      .showroom-hero .hero-media img {
        width: 100%;
        border-radius: 1.2rem;
        object-fit: cover;
        box-shadow: 0 25px 60px rgba(2, 6, 23, 0.55);
        border: 1px solid rgba(148, 163, 184, 0.15);
      }
      .hero-floating-badges {
        position: absolute;
        inset: 0;
        pointer-events: none;
      }
      .hero-floating-badges span {
        position: absolute;
        padding: 0.5rem 1rem;
        background: rgba(15, 23, 42, 0.8);
        border-radius: 999px;
        color: #e0f2ff;
        font-size: 0.85rem;
        border: 1px solid rgba(59, 130, 246, 0.4);
        box-shadow: 0 10px 30px rgba(2, 6, 23, 0.5);
      }
      .showroom-highlight {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
      }
      .featured-card {
        position: relative;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.4));
        border-radius: 1.2rem;
        padding: 1.5rem;
        border: 1px solid var(--panel-border);
        overflow: hidden;
        min-height: 320px;
      }
      .featured-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top, rgba(59, 130, 246, 0.25), transparent 60%);
        opacity: 0.6;
      }
      .featured-card > * {
        position: relative;
        z-index: 1;
      }
      .featured-card ul {
          list-style: none;
          padding: 0;
          margin: 0 0 1rem;
          color: var(--muted);
      }
      .featured-card li {
        margin-bottom: 0.2rem;
      }
      .featured-media img {
        width: 100%;
        border-radius: 0.85rem;
        border: 1px solid var(--panel-border);
        margin-bottom: 0.75rem;
        object-fit: cover;
        max-height: 220px;
      }
      .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
      }
      .product-card {
        background: var(--card-bg);
        border-radius: 1.1rem;
        padding: 1.25rem;
        border: 1px solid var(--panel-border);
        box-shadow: 0 15px 35px rgba(2, 6, 23, 0.15);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
      }
      .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 45px rgba(2, 6, 23, 0.25);
      }
      .badge-row {
        display: flex;
        gap: 0.4rem;
      }
      .badge {
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.12);
        color: #93c5fd;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
      }
      :root[data-theme="light"] .badge {
        background: rgba(37, 99, 235, 0.08);
        color: #2563eb;
      }
      .badge.warning {
        background: rgba(251, 191, 36, 0.18);
        color: #fbbf24;
      }
      .sku {
        color: var(--muted);
        font-size: 0.9rem;
        margin-top: -0.4rem;
      }
      .spec-list {
        list-style: none;
        padding: 0;
        margin: 0;
        color: var(--muted);
      }
      .spec-list li {
        margin-bottom: 0.2rem;
      }
      .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
      }
      .price {
        font-size: 1.6rem;
        margin: 0;
        font-weight: 600;
      }
      .muted {
        color: var(--muted);
        margin: 0;
        font-size: 0.85rem;
      }
      .product-media {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid var(--panel-border);
      }
      .product-media img {
        width: 100%;
        display: block;
        object-fit: cover;
        aspect-ratio: 4 / 3;
      }
      .zoom-pane {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 110px;
        height: 110px;
        border-radius: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        background-repeat: no-repeat;
        background-size: 200%;
        display: none;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
      }
      .gallery-thumbs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin: 0.65rem 0 0;
      }
      .gallery-thumbs img {
        width: 58px;
        height: 58px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
      }
      .thumb {
        background: transparent;
        border: none;
        padding: 0;
        cursor: pointer;
      }
      .product-cta {
        text-decoration: none;
        padding: 0.6rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(59, 130, 246, 0.4);
        color: #e0f2ff;
        font-weight: 600;
      }
      :root[data-theme="light"] .product-cta {
        color: #1d4ed8;
      }
      .link-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        color: var(--text-color);
        text-decoration: none;
        font-size: 0.9rem;
      }
      .ghost-btn.danger,
      .link-btn.danger {
        border-color: rgba(239, 68, 68, 0.4);
        color: #f87171;
      }
      .inventory-thumb {
        width: 58px;
        height: 58px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 1px solid var(--panel-border);
      }
      .theme-toggle {
        display: flex;
        gap: 0.4rem;
        align-items: center;
      }
      .theme-btn {
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        color: var(--muted);
        cursor: pointer;
        font-size: 0.85rem;
      }
      .theme-btn.active {
        border-color: rgba(59, 130, 246, 0.7);
        color: #e0f2ff;
        background: rgba(59, 130, 246, 0.15);
      }
      :root[data-theme="light"] .theme-btn.active {
        color: #1d4ed8;
        background: rgba(59, 130, 246, 0.08);
      }
      .user-chip {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-shrink: 0;
        flex-wrap: wrap;
        justify-content: flex-end;
      }
      .user-chip span {
        font-size: 0.9rem;
        color: var(--muted);
      }
      .user-chip form {
        margin: 0;
      }
      .user-chip button {
        margin-top: 0;
        padding: 0.45rem 0.9rem;
        font-size: 0.85rem;
      }
    </style>
  </head>
  <body>
    <header>
      <div class="main-header">
        <div class="brand">
          <div class="brand-logo">
            <img src="/assets/sr-mac-logo.svg" alt="SR Mac Shop logo" />
          </div>
          <div>
            <h1>SR MAC SHOP</h1>
            <p class="brand-tagline">Premier Mac Studio & Ops</p>
          </div>
        </div>
        <nav aria-label="Main navigation">
          <div class="nav-links">
            <a href="/"<?= nav_attrs('/', $navPath) ?>>Dashboard</a>
            <a href="/inventory"<?= nav_attrs('/inventory', $navPath) ?>>Inventory</a>
            <a href="/sales/new"<?= nav_attrs('/sales/new', $navPath) ?>>New Sale</a>
            <a href="/sales"<?= nav_attrs('/sales', $navPath) ?>>Sales</a>
            <a href="/bookings"<?= nav_attrs('/bookings', $navPath) ?>>Bookings</a>
            <a href="/store"<?= nav_attrs('/store', $navPath) ?>>Showroom</a>
          </div>
        </nav>
        <div class="user-chip">
          <div class="theme-toggle">
            <button class="theme-btn" data-theme="light">Light</button>
            <button class="theme-btn" data-theme="dark">Dark</button>
            <button class="theme-btn active" data-theme="system">System</button>
          </div>
          <?php if (!empty($currentUser)): ?>
            <span><?= htmlspecialchars($currentUser['name']) ?> · <?= strtoupper(htmlspecialchars($currentUser['role'])) ?></span>
            <form method="post" action="/logout">
              <button type="submit" class="ghost-btn danger">Logout</button>
            </form>
          <?php else: ?>
            <a href="/login" class="link-btn">Sign in</a>
          <?php endif; ?>
        </div>
      </div>
    </header>
    <main>
      <?php if ($flash): ?>
        <div class="flash <?= htmlspecialchars($flash['status'] ?? 'success') ?>">
          <?= htmlspecialchars($flash['message'] ?? '') ?>
        </div>
      <?php endif; ?>
      <?php if (isset($view) && file_exists($view)) { include $view; } ?>
    </main>
    <script>
      (function () {
        const root = document.documentElement;
        const stored = localStorage.getItem('mac-pos-theme') || 'system';
        const buttons = document.querySelectorAll('.theme-btn');

        function applyTheme(mode) {
          root.setAttribute('data-theme', mode);
          if (mode === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            root.dataset.systemPref = prefersDark ? 'dark' : 'light';
          } else {
            delete root.dataset.systemPref;
          }
          buttons.forEach((btn) => btn.classList.toggle('active', btn.dataset.theme === mode));
        }

        applyTheme(stored);

        buttons.forEach((btn) => {
          btn.addEventListener('click', () => {
            const mode = btn.dataset.theme;
            localStorage.setItem('mac-pos-theme', mode);
            applyTheme(mode);
          });
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
          if ((localStorage.getItem('mac-pos-theme') || 'system') === 'system') {
            applyTheme('system');
          }
        });
      })();
    </script>
  </body>
</html>

