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

function user_initials(string $name): string
{
    $name = trim($name);
    if ($name === '') {
        return '?';
    }
    $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
    }

    return strtoupper(substr($name, 0, min(2, strlen($name))));
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="system">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script>
      (function () {
        var h = document.documentElement;
        if (h.getAttribute('data-theme') === 'system') {
          h.setAttribute(
            'data-system-pref',
            window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
          );
        }
      })();
    </script>
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
        --header-divider: rgba(148, 163, 184, 0.22);
        display: grid;
        grid-template-columns: minmax(0, auto) minmax(0, 1fr) auto;
        align-items: center;
        column-gap: clamp(0.75rem, 2vw, 1.75rem);
        row-gap: 1rem;
        padding: 0.85rem 1rem 0.85rem clamp(1rem, 3vw, 2rem);
        margin: 1rem clamp(1.25rem, 4vw, 3rem) 0;
        border-radius: 1.35rem;
        position: relative;
        isolation: isolate;
        background:
          linear-gradient(145deg, rgba(17, 26, 46, 0.92) 0%, rgba(15, 23, 42, 0.88) 45%, rgba(30, 58, 138, 0.35) 100%);
        border: 1px solid rgba(96, 165, 250, 0.18);
        box-shadow:
          0 0 0 1px rgba(255, 255, 255, 0.04) inset,
          0 1px 0 rgba(255, 255, 255, 0.06) inset,
          0 28px 56px -12px rgba(2, 6, 23, 0.65),
          0 12px 24px -16px rgba(37, 99, 235, 0.35);
        backdrop-filter: blur(12px);
      }
      .main-header::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(
          125deg,
          rgba(147, 197, 253, 0.45) 0%,
          rgba(59, 130, 246, 0.08) 35%,
          rgba(59, 130, 246, 0.05) 70%,
          rgba(96, 165, 250, 0.25) 100%
        );
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
        opacity: 0.85;
      }
      @media (max-width: 680px) {
        .main-header {
          grid-template-columns: minmax(0, 1fr) auto;
          grid-template-rows: auto auto;
        }
        .main-header .brand {
          grid-column: 1;
          grid-row: 1;
          min-width: 0;
        }
        .main-header .header-actions {
          grid-column: 2;
          grid-row: 1;
          align-self: center;
          justify-self: end;
          margin-left: 0;
          padding-left: 0;
          border-left: none;
        }
        .main-header nav {
          grid-column: 1 / -1;
          grid-row: 2;
          justify-self: stretch;
        }
      }
      :root[data-theme="light"] header {
        background: transparent;
      }
      :root[data-theme="light"] .main-header {
        --header-divider: rgba(15, 23, 42, 0.1);
        background: linear-gradient(
          165deg,
          rgba(255, 255, 255, 0.98) 0%,
          rgba(248, 250, 252, 0.95) 40%,
          rgba(241, 245, 249, 0.92) 100%
        );
        border-color: rgba(15, 23, 42, 0.09);
        box-shadow:
          0 0 0 1px rgba(255, 255, 255, 0.8) inset,
          0 1px 0 rgba(255, 255, 255, 1) inset,
          0 22px 44px -18px rgba(15, 23, 42, 0.14),
          0 8px 16px -10px rgba(59, 130, 246, 0.12);
        backdrop-filter: blur(10px);
      }
      :root[data-theme="light"] .main-header::before {
        background: linear-gradient(
          125deg,
          rgba(59, 130, 246, 0.2) 0%,
          rgba(148, 163, 184, 0.08) 45%,
          rgba(59, 130, 246, 0.12) 100%
        );
        opacity: 1;
      }
      .brand {
        display: flex;
        align-items: center;
        gap: 0.85rem 1rem;
        flex-shrink: 0;
        min-width: 0;
      }
      .brand-mark {
        flex-shrink: 0;
        text-decoration: none;
        color: inherit;
        border-radius: 1.05rem;
        padding: 0.1rem;
        background: linear-gradient(
          135deg,
          rgba(255, 255, 255, 0.12) 0%,
          rgba(59, 130, 246, 0.15) 50%,
          rgba(15, 23, 42, 0.4) 100%
        );
        box-shadow:
          0 1px 0 rgba(255, 255, 255, 0.1) inset,
          0 6px 20px rgba(2, 6, 23, 0.45);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }
      .brand-mark:hover {
        transform: translateY(-1px);
        box-shadow:
          0 1px 0 rgba(255, 255, 255, 0.12) inset,
          0 10px 28px rgba(37, 99, 235, 0.3);
      }
      .brand-mark:focus-visible {
        outline: 2px solid rgba(59, 130, 246, 0.8);
        outline-offset: 3px;
      }
      :root[data-theme="light"] .brand-mark {
        background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
        box-shadow:
          0 1px 0 rgba(255, 255, 255, 1) inset,
          0 4px 16px rgba(15, 23, 42, 0.1),
          0 0 0 1px rgba(15, 23, 42, 0.06);
      }
      :root[data-theme="light"] .brand-mark:hover {
        box-shadow:
          0 1px 0 rgba(255, 255, 255, 1) inset,
          0 8px 24px rgba(37, 99, 235, 0.12),
          0 0 0 1px rgba(37, 99, 235, 0.12);
      }
      .brand-logo {
        width: 50px;
        height: 50px;
        border-radius: 0.9rem;
        overflow: hidden;
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.95) 0%, rgba(226, 232, 240, 0.9) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
      }
      :root[data-theme="light"] .brand-logo {
        background: linear-gradient(160deg, #ffffff 0%, #f8fafc 100%);
      }
      .brand-logo img {
        width: 40px;
        height: 40px;
        object-fit: contain;
      }
      .brand-text {
        display: flex;
        flex-direction: column;
        gap: 0.12rem;
        min-width: 0;
      }
      .brand-name {
        margin: 0;
        font-size: clamp(1.2rem, 2.4vw, 1.5rem);
        font-weight: 700;
        letter-spacing: -0.04em;
        line-height: 1.1;
        color: var(--text-color);
      }
      @supports (background-clip: text) or (-webkit-background-clip: text) {
        :root[data-theme="dark"] .brand-name,
        :root[data-theme="system"][data-system-pref="dark"] .brand-name,
        :root:not([data-theme]) .brand-name {
          background: linear-gradient(100deg, #f8fafc 0%, #e2e8f0 40%, #7dd3fc 100%);
          -webkit-background-clip: text;
          background-clip: text;
          color: transparent;
        }
        :root[data-theme="light"] .brand-name,
        :root[data-theme="system"][data-system-pref="light"] .brand-name {
          background: linear-gradient(100deg, #0f172a 0%, #1e3a5f 45%, #2563eb 100%);
          -webkit-background-clip: text;
          background-clip: text;
          color: transparent;
        }
      }
      .brand-tagline {
        margin: 0.1rem 0 0;
        color: var(--muted);
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.01em;
        line-height: 1.35;
        max-width: 16rem;
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
        min-width: 0;
        justify-self: center;
        width: 100%;
        max-width: min(52rem, 100%);
      }
      .nav-shell {
        display: block;
        width: 100%;
        padding: 0.3rem 0.45rem;
        border-radius: 999px;
        background: rgba(2, 6, 23, 0.35);
        border: 1px solid rgba(148, 163, 184, 0.12);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.05) inset;
      }
      :root[data-theme="light"] .nav-shell {
        background: rgba(15, 23, 42, 0.04);
        border-color: rgba(15, 23, 42, 0.07);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.9) inset;
      }
      .nav-links {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.2rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
        padding: 0.05rem 0;
        mask-image: linear-gradient(to right, transparent 0, #000 10px, #000 calc(100% - 10px), transparent 100%);
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
        font-size: 0.8125rem;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        border: 1px solid transparent;
        background: transparent;
        transition: background 0.18s ease, transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
      }
      .nav-links a:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-1px);
      }
      .nav-links a.active {
        background: rgba(59, 130, 246, 0.35);
        border-color: rgba(147, 197, 253, 0.35);
        box-shadow: 0 2px 12px rgba(37, 99, 235, 0.35);
      }
      :root[data-theme="light"] .nav-links a {
        color: #0f172a;
        background: transparent;
        border-color: transparent;
      }
      :root[data-theme="light"] .nav-links a:hover {
        background: rgba(15, 23, 42, 0.06);
      }
      :root[data-theme="light"] .nav-links a.active {
        background: rgba(59, 130, 246, 0.18);
        border-color: rgba(37, 99, 235, 0.25);
        box-shadow: 0 2px 10px rgba(37, 99, 235, 0.15);
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
        display: inline-flex;
        gap: 0;
        align-items: center;
        padding: 0.1rem;
        border-radius: 999px;
        background: rgba(2, 6, 23, 0.28);
        border: 1px solid rgba(148, 163, 184, 0.12);
        flex-shrink: 0;
      }
      :root[data-theme="light"] .theme-toggle {
        background: rgba(15, 23, 42, 0.05);
        border-color: rgba(15, 23, 42, 0.08);
      }
      .theme-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 1px solid transparent;
        padding: 0.2rem;
        min-width: 1.65rem;
        min-height: 1.65rem;
        border-radius: 999px;
        color: var(--muted);
        cursor: pointer;
        line-height: 0;
        transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
      }
      .theme-btn svg {
        width: 13px;
        height: 13px;
        flex-shrink: 0;
      }
      /* Selected segment follows html[data-theme] — matches resolved colors via CSS vars */
      :root[data-theme="light"] .theme-toggle .theme-btn[data-theme="light"],
      :root[data-theme="dark"] .theme-toggle .theme-btn[data-theme="dark"],
      :root[data-theme="system"] .theme-toggle .theme-btn[data-theme="system"] {
        border-color: rgba(59, 130, 246, 0.55);
        background: var(--chip-bg);
        color: var(--chip-color);
        box-shadow:
          0 0 0 1px rgba(59, 130, 246, 0.2),
          0 1px 3px rgba(15, 23, 42, 0.12);
      }
      :root[data-theme="dark"] .theme-toggle .theme-btn[data-theme="dark"],
      :root[data-theme="system"][data-system-pref="dark"] .theme-toggle .theme-btn[data-theme="system"] {
        box-shadow:
          0 0 0 1px rgba(96, 165, 250, 0.35),
          0 2px 8px rgba(37, 99, 235, 0.25);
      }
      .theme-toggle .theme-btn:focus-visible {
        outline: 2px solid rgba(59, 130, 246, 0.75);
        outline-offset: 2px;
      }
      .user-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        flex-shrink: 0;
        min-width: 0;
        flex-wrap: nowrap;
        justify-content: flex-end;
      }
      .header-actions {
        padding-left: 0.35rem;
        margin-left: 0.3rem;
        border-left: 1px solid var(--header-divider);
      }
      .user-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.65rem;
        height: 1.65rem;
        border-radius: 999px;
        font-size: 0.58rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        line-height: 1;
        background: rgba(59, 130, 246, 0.22);
        border: 1px solid rgba(96, 165, 250, 0.38);
        color: var(--text-color);
        flex-shrink: 0;
        cursor: default;
      }
      :root[data-theme="light"] .user-avatar {
        background: rgba(59, 130, 246, 0.12);
        border-color: rgba(37, 99, 235, 0.22);
      }
      .user-chip form {
        margin: 0;
        flex-shrink: 0;
      }
      .user-chip button {
        margin-top: 0;
      }
      .user-chip .header-logout {
        padding: 0.2rem 0.32rem;
        min-width: 1.85rem;
        min-height: 1.65rem;
        border-radius: 0.55rem;
        margin-top: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }
      .user-chip .header-logout svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
      }
      .user-chip .link-btn {
        padding: 0.22rem 0.5rem;
        font-size: 0.72rem;
        border-radius: 999px;
        white-space: nowrap;
      }
    </style>
  </head>
  <body>
    <header>
      <div class="main-header">
        <div class="brand">
          <a class="brand-mark" href="/" title="SR Mac Shop — Dashboard" aria-label="SR Mac Shop, go to dashboard">
            <div class="brand-logo">
              <img src="/assets/sr-mac-logo.svg" alt="" width="40" height="40" />
            </div>
          </a>
          <div class="brand-text">
            <h1 class="brand-name">SR Mac Shop</h1>
            <p class="brand-tagline">Premier Mac studio, showroom &amp; operations</p>
          </div>
        </div>
        <nav aria-label="Main navigation">
          <div class="nav-shell">
            <div class="nav-links">
              <a href="/"<?= nav_attrs('/', $navPath) ?>>Dashboard</a>
              <a href="/inventory"<?= nav_attrs('/inventory', $navPath) ?>>Inventory</a>
              <a href="/sales/new"<?= nav_attrs('/sales/new', $navPath) ?>>New Sale</a>
              <a href="/sales"<?= nav_attrs('/sales', $navPath) ?>>Sales</a>
              <a href="/bookings"<?= nav_attrs('/bookings', $navPath) ?>>Bookings</a>
              <a href="/store"<?= nav_attrs('/store', $navPath) ?>>Showroom</a>
            </div>
          </div>
        </nav>
        <div class="user-chip header-actions">
          <div class="theme-toggle" role="group" aria-label="Color theme">
            <button type="button" class="theme-btn" data-theme="light" title="Light" aria-label="Light theme" aria-pressed="false">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 14v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m14 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            </button>
            <button type="button" class="theme-btn" data-theme="dark" title="Dark" aria-label="Dark theme" aria-pressed="false">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
            <button type="button" class="theme-btn" data-theme="system" title="System (match device)" aria-label="System theme" aria-pressed="false">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><path stroke-linecap="round" d="M8 21h8m-4-4v4"/></svg>
            </button>
          </div>
          <?php if (!empty($currentUser)): ?>
            <?php
            $userLabel = htmlspecialchars($currentUser['name']) . ' · ' . strtoupper(htmlspecialchars($currentUser['role']));
            ?>
            <span class="user-avatar" title="<?= $userLabel ?>"><?= htmlspecialchars(user_initials($currentUser['name'])) ?></span>
            <form method="post" action="/logout">
              <button type="submit" class="ghost-btn danger header-logout" aria-label="Log out" title="Log out">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 9l3 3m0 0l-3 3m3-3H9"/></svg>
              </button>
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
        const buttons = document.querySelectorAll('.theme-btn');
        const raw = localStorage.getItem('mac-pos-theme');
        const stored = raw === 'light' || raw === 'dark' || raw === 'system' ? raw : 'system';

        function applyTheme(mode) {
          if (mode !== 'light' && mode !== 'dark' && mode !== 'system') {
            mode = 'system';
          }
          root.setAttribute('data-theme', mode);
          if (mode === 'system') {
            root.dataset.systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches
              ? 'dark'
              : 'light';
          } else {
            delete root.dataset.systemPref;
          }
          buttons.forEach((btn) => {
            const on = btn.dataset.theme === mode;
            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
          });
        }

        applyTheme(stored);

        buttons.forEach((btn) => {
          btn.addEventListener('click', () => {
            const mode = btn.dataset.theme;
            if (mode !== 'light' && mode !== 'dark' && mode !== 'system') {
              return;
            }
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

