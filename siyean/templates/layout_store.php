<?php
$navPath = $request_path ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

function store_nav_attrs(string $kind, string $current): string
{
    $active = match ($kind) {
        'home' => in_array($current, ['/', '/store'], true),
        'devices' => str_starts_with($current, '/store/product'),
        'login' => str_starts_with($current, '/login'),
        default => false,
    };

    return $active ? ' class="active" aria-current="page"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mac POS Showroom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Space+Grotesk:wght@500;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      :root {
        color-scheme: light;
        --ink: #0c1222;
        --ink-soft: #3d4659;
        --muted: #64708b;
        --line: rgba(12, 18, 34, 0.08);
        --line-strong: rgba(12, 18, 34, 0.12);
        --surface: #ffffff;
        --surface-elevated: #fafbfd;
        --page: #f3f5f8;
        --accent: #2563eb;
        --accent-hover: #1d4ed8;
        --accent-soft: rgba(37, 99, 235, 0.09);
        --radius: 1rem;
        --radius-lg: 1.35rem;
        --shadow-xs: 0 1px 2px rgba(12, 18, 34, 0.04);
        --shadow-sm: 0 4px 14px rgba(12, 18, 34, 0.06);
        --shadow-md: 0 12px 32px rgba(12, 18, 34, 0.08);
        --font-body: "DM Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        --font-display: "Space Grotesk", var(--font-body);
        font-family: var(--font-body);
        color: var(--ink);
        background: var(--page);
        line-height: 1.5;
      }

      *,
      *::before,
      *::after {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        background: var(--page);
        color: var(--ink);
        -webkit-font-smoothing: antialiased;
      }

      h1,
      h2,
      h3 {
        font-family: var(--font-display);
        letter-spacing: -0.02em;
      }

      header {
        background: transparent;
        padding: clamp(1rem, 3vw, 2rem) clamp(1rem, 4vw, 3rem) 0;
      }

      .main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem 1.5rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0.85rem 1.15rem;
        border-radius: var(--radius-lg);
        background: var(--surface);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-xs);
      }

      .brand {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-width: 0;
      }

      .brand-logo {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--line);
        background: linear-gradient(145deg, #fff, #eef2f7);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
      }

      .brand-logo img {
        width: 38px;
        height: 38px;
        object-fit: contain;
      }

      .brand h1 {
        margin: 0;
        font-size: clamp(1.15rem, 2.5vw, 1.45rem);
        font-weight: 700;
        color: var(--ink);
      }

      .brand-tagline {
        margin: 0.1rem 0 0;
        color: var(--muted);
        font-size: 0.8125rem;
        font-weight: 500;
      }

      .site-nav {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-end;
      }

      .nav-links {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
      }

      .nav-links a {
        text-decoration: none;
        color: var(--ink-soft);
        padding: 0.45rem 0.95rem;
        border-radius: 999px;
        border: 1px solid transparent;
        background: var(--surface-elevated);
        font-weight: 600;
        font-size: 0.875rem;
        transition: color 0.15s ease, background 0.15s ease, border-color 0.15s ease;
      }

      .nav-links a:hover {
        color: var(--accent);
        background: var(--accent-soft);
        border-color: rgba(37, 99, 235, 0.15);
      }

      .nav-links a.active {
        color: var(--accent);
        background: var(--accent-soft);
        border-color: rgba(37, 99, 235, 0.22);
      }

      .nav-menu-btn {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.45rem 0.75rem;
        border-radius: 10px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink-soft);
        font-family: inherit;
        font-size: 0.8125rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
      }

      .nav-menu-btn:hover {
        background: var(--surface-elevated);
        color: var(--ink);
        border-color: var(--line-strong);
      }

      .nav-menu-btn:focus-visible {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
      }

      .nav-menu-btn svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
      }

      @media (max-width: 720px) {
        .nav-menu-btn {
          display: inline-flex;
        }

        .site-nav .nav-links {
          display: none;
          position: absolute;
          right: 0;
          top: calc(100% + 0.4rem);
          min-width: 12.5rem;
          flex-direction: column;
          align-items: stretch;
          padding: 0.4rem;
          gap: 0.2rem;
          background: var(--surface);
          border: 1px solid var(--line);
          border-radius: var(--radius);
          box-shadow: var(--shadow-md);
          z-index: 50;
        }

        .site-nav.is-open .nav-links {
          display: flex;
        }

        .site-nav.is-open .nav-menu-btn {
          border-color: rgba(37, 99, 235, 0.25);
          color: var(--accent);
          background: var(--accent-soft);
        }
      }

      .login-launcher {
        position: fixed;
        right: 1.25rem;
        bottom: 1.25rem;
        background: var(--ink);
        color: #fff;
        padding: 0.75rem 1.15rem;
        border-radius: 999px;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        gap: 0.55rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.22s ease, transform 0.22s ease;
        transform: translateY(8px);
        z-index: 30;
        font-size: 0.875rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
      }

      .login-launcher span {
        opacity: 0.85;
        font-weight: 500;
      }

      .login-launcher a {
        color: #fff;
        text-decoration: none;
        font-weight: 700;
      }

      .login-launcher a:hover {
        text-decoration: underline;
      }

      body[data-login-visible="true"] .login-launcher {
        opacity: 1;
        pointer-events: all;
        transform: translateY(0);
      }

      main {
        padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 4vw, 3rem) 4rem;
        max-width: 1200px;
        margin: 0 auto;
      }

      .store-flash {
        margin-bottom: 1.25rem;
        padding: 0.9rem 1.1rem;
        border-radius: var(--radius);
        border: 1px solid var(--line);
        font-size: 0.9375rem;
        font-weight: 500;
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
      }

      .store-flash::before {
        content: "";
        width: 4px;
        align-self: stretch;
        border-radius: 2px;
        flex-shrink: 0;
      }

      .store-flash--success {
        background: rgba(16, 185, 129, 0.06);
        border-color: rgba(16, 185, 129, 0.22);
        color: #047857;
      }

      .store-flash--success::before {
        background: #10b981;
      }

      .store-flash--error {
        background: rgba(239, 68, 68, 0.06);
        border-color: rgba(239, 68, 68, 0.2);
        color: #b91c1c;
      }

      .store-flash--error::before {
        background: #ef4444;
      }

      .link-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-decoration: none;
        color: var(--ink-soft);
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.4rem 0.2rem 0.4rem 0;
        transition: color 0.15s ease;
      }

      .link-btn:hover {
        color: var(--accent);
      }

      .product-showcase {
        margin-top: 0.5rem;
      }

      .showcase-grid {
        display: grid;
        grid-template-columns: minmax(300px, 1.1fr) minmax(260px, 0.9fr);
        gap: clamp(1.25rem, 3vw, 2rem);
        align-items: start;
      }

      @media (max-width: 960px) {
        .showcase-grid {
          grid-template-columns: 1fr;
        }
      }

      .showcase-media {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }

      .showcase-info.panel h1 {
        margin: 0.35rem 0 0;
        font-size: clamp(1.5rem, 3vw, 1.85rem);
        font-weight: 700;
        line-height: 1.15;
      }

      .showcase-description {
        margin: 0.85rem 0 0;
        color: var(--ink-soft);
        font-size: 0.9375rem;
        line-height: 1.65;
      }

      .showcase-price {
        margin: 1.25rem 0 0;
        padding-top: 1rem;
        border-top: 1px solid var(--line);
      }

      .showcase-price strong {
        font-family: var(--font-display);
        font-size: clamp(1.65rem, 3vw, 2rem);
        font-weight: 700;
        letter-spacing: -0.03em;
        display: block;
        color: var(--ink);
      }

      .showcase-price small {
        display: block;
        margin-top: 0.35rem;
        color: var(--muted);
        font-size: 0.8125rem;
      }

      .media-gallery {
        display: grid;
        grid-template-columns: minmax(72px, 88px) 1fr;
        gap: 1rem;
        align-items: start;
      }

      @media (max-width: 768px) {
        .media-gallery {
          grid-template-columns: 1fr;
        }

        .media-tray {
          order: 2;
          flex-direction: row;
          justify-content: flex-start;
          overflow-x: auto;
          width: 100%;
          height: auto;
          max-height: none;
          padding-bottom: 0.25rem;
        }

        .media-thumb {
          flex: 0 0 auto;
          width: 76px;
        }

        .media-thumb img {
          height: 76px;
        }
      }

      .media-tray {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
        max-height: 480px;
        overflow-y: auto;
        padding-right: 0.25rem;
        scrollbar-width: thin;
      }

      .media-stage {
        border-radius: var(--radius-lg);
        background: var(--surface);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        position: relative;
        aspect-ratio: 4 / 3;
        display: flex;
        align-items: stretch;
        justify-content: stretch;
      }

      .media-stage img,
      .media-stage video,
      .media-stage iframe {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
      }

      .media-video,
      .media-video--embed {
        width: 100%;
        height: 100%;
        min-height: 0;
        background: #0f172a;
      }

      .media-video iframe,
      .media-video video {
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
      }

      .media-preview {
        position: relative;
        overflow: hidden;
        width: 100%;
        height: 100%;
      }

      .media-preview img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
      }

      .media-thumb {
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 0;
        background: transparent;
        cursor: pointer;
        overflow: hidden;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
      }

      .media-thumb img {
        width: 100%;
        height: 70px;
        object-fit: cover;
        display: block;
        border-radius: inherit;
      }

      .media-thumb.is-active {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-soft);
      }

      .thumb-video {
        position: relative;
      }

      .thumb-video span {
        position: absolute;
        inset: 0;
        margin: auto;
        width: 26px;
        height: 26px;
        background: rgba(12, 18, 34, 0.82);
        color: #fff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
      }

      .panel {
        background: var(--surface);
        border-radius: var(--radius-lg);
        padding: clamp(1.25rem, 3vw, 1.75rem);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-xs);
      }

      .chip {
        display: inline-flex;
        align-items: center;
        padding: 0.32rem 0.75rem;
        border-radius: 999px;
        background: var(--accent-soft);
        color: var(--accent-hover);
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
      }

      .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
      }

      .hero-actions button {
        border: none;
        cursor: pointer;
        border-radius: 12px;
        padding: 0.75rem 1.35rem;
        font-family: var(--font-body);
        font-weight: 600;
        font-size: 0.9375rem;
        letter-spacing: 0.01em;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
      }

      .hero-actions button:first-child {
        background: #fff;
        color: var(--ink);
        box-shadow: var(--shadow-sm);
      }

      .hero-actions button:first-child:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
      }

      .hero-actions button.ghost-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #f1f5f9;
        box-shadow: none;
      }

      .hero-actions button.ghost-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-1px);
      }

      .showroom-hero {
        position: relative;
        overflow: hidden;
        border-radius: calc(var(--radius-lg) + 4px);
        padding: clamp(2rem, 5vw, 3.25rem);
        background:
          radial-gradient(ellipse 90% 70% at 15% 10%, rgba(59, 130, 246, 0.35), transparent 52%),
          radial-gradient(ellipse 60% 50% at 90% 85%, rgba(14, 165, 233, 0.18), transparent 45%),
          linear-gradient(165deg, #0b1220 0%, #111a2e 48%, #0c1428 100%);
        border: 1px solid rgba(148, 163, 184, 0.12);
        min-height: 340px;
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: center;
        color: #e8edf5;
        margin-bottom: 2rem;
      }

      .showroom-hero .hero-content {
        flex: 1 1 300px;
        z-index: 1;
      }

      .showroom-hero .chip {
        background: rgba(255, 255, 255, 0.12);
        color: #dbeafe;
        border: 1px solid rgba(255, 255, 255, 0.14);
      }

      .showroom-hero h1 {
        margin: 0.35rem 0;
        font-size: clamp(1.65rem, 4vw, 2.35rem);
        font-weight: 700;
        line-height: 1.15;
        max-width: 22ch;
      }

      .showroom-hero p {
        margin: 0;
        color: rgba(226, 232, 240, 0.82);
        font-size: 1rem;
        line-height: 1.65;
        max-width: 38ch;
      }

      .showroom-hero .hero-media {
        flex: 1 1 280px;
        position: relative;
        z-index: 1;
      }

      .showroom-hero .hero-media img {
        width: 100%;
        border-radius: var(--radius-lg);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 24px 48px rgba(0, 0, 0, 0.35);
      }

      .hero-floating-badges {
        position: absolute;
        inset: 0;
        pointer-events: none;
      }

      .hero-floating-badges span {
        position: absolute;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        background: rgba(12, 18, 34, 0.72);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: var(--shadow-sm);
        font-size: 0.8125rem;
        font-weight: 600;
        color: #f8fafc;
      }

      .showroom-highlight {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
      }

      .featured-card {
        background: var(--surface);
        color: var(--ink);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        border: 1px solid var(--line);
        box-shadow: var(--shadow-xs);
        min-height: 300px;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
      }

      .featured-card .chip {
        align-self: flex-start;
      }

      .featured-card .muted {
        color: var(--muted);
      }

      .featured-card .product-cta {
        margin-top: auto;
        align-self: flex-start;
      }

      .featured-media img {
        width: 100%;
        border-radius: var(--radius);
        border: 1px solid var(--line);
        margin: 0.75rem 0;
        object-fit: cover;
      }

      .gallery-thumbs button,
      .gallery-thumbs .thumb {
        border: none;
        background: transparent;
        padding: 0;
        cursor: pointer;
      }

      .gallery-thumbs img {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid var(--line);
        transition: border-color 0.15s ease;
      }

      .gallery-thumbs button:hover img,
      .gallery-thumbs .thumb:hover img {
        border-color: rgba(37, 99, 235, 0.35);
      }

      .pill-filters {
        display: flex;
        gap: 0.45rem;
        flex-wrap: wrap;
        margin: 0 0 1.25rem;
      }

      .pill {
        padding: 0.38rem 0.95rem;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink-soft);
        font-size: 0.8125rem;
        font-weight: 600;
      }

      .badge-row {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
      }

      .spec-list {
        list-style: none;
        padding: 0;
        margin: 0;
        color: var(--muted);
        font-size: 0.9375rem;
      }

      .spec-list li {
        margin-bottom: 0.35rem;
      }

      .sku {
        color: var(--muted);
        font-size: 0.8125rem;
        margin: -0.15rem 0 0.35rem;
      }

      .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
      }

      .price {
        font-family: var(--font-display);
        font-size: 1.5rem;
        margin: 0;
        font-weight: 700;
        letter-spacing: -0.03em;
      }

      .muted {
        color: var(--muted);
        margin: 0;
        font-size: 0.875rem;
      }

      .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
      }

      .product-card {
        background: var(--surface);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        border: 1px solid var(--line);
        box-shadow: var(--shadow-xs);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
      }

      .product-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-sm);
        border-color: rgba(37, 99, 235, 0.15);
      }

      .product-card h3 {
        margin: 0.35rem 0 0;
        font-size: 1.05rem;
        font-weight: 700;
      }

      .badge {
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        background: var(--surface-elevated);
        border: 1px solid var(--line);
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--ink-soft);
      }

      .badge.warning {
        background: rgba(245, 158, 11, 0.12);
        border-color: rgba(245, 158, 11, 0.25);
        color: #b45309;
      }

      .product-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border-radius: 11px;
        padding: 0.55rem 1.15rem;
        border: 1px solid var(--line-strong);
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--ink);
        background: var(--surface);
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
      }

      .product-cta:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--accent-soft);
      }

      .gallery-thumbs {
        display: flex;
        gap: 0.45rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
      }

      .product-media {
        position: relative;
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 0.75rem;
        border: 1px solid var(--line);
      }

      .product-media img {
        width: 100%;
        display: block;
        aspect-ratio: 4 / 3;
        object-fit: cover;
      }

      .zoom-pane {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 100px;
        height: 100px;
        border-radius: 10px;
        border: 1px solid var(--line-strong);
        background-repeat: no-repeat;
        background-size: 200%;
        display: none;
        box-shadow: var(--shadow-md);
      }

      .booking-card {
        margin-top: 1.75rem;
        background: var(--surface);
        border-radius: calc(var(--radius-lg) + 2px);
        padding: clamp(1.5rem, 4vw, 2.5rem);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-sm);
      }

      .booking-card__header {
        display: flex;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        border-bottom: 1px solid var(--line);
        padding-bottom: 1.35rem;
        margin-bottom: 1.35rem;
      }

      .booking-card__header h2 {
        margin: 0.35rem 0 0;
        font-size: 1.35rem;
      }

      .booking-card__header p {
        margin: 0.35rem 0 0;
        color: var(--muted);
        font-size: 0.9375rem;
        max-width: 36rem;
      }

      .booking-card__price {
        text-align: right;
      }

      .booking-card__price span {
        display: block;
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: var(--ink);
      }

      .booking-card__price small {
        color: var(--muted);
        font-size: 0.8125rem;
      }

      .booking-form {
        display: flex;
        flex-direction: column;
        gap: 1.35rem;
      }

      .form-section h3 {
        margin: 0 0 0.5rem;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
      }

      .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
      }

      .form-field {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--ink-soft);
      }

      .form-field input,
      .form-field textarea {
        border: 1px solid var(--line-strong);
        border-radius: 12px;
        padding: 0.7rem 0.95rem;
        font-size: 0.9375rem;
        font-family: inherit;
        background: var(--surface-elevated);
        color: var(--ink);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
      }

      .form-field input:focus,
      .form-field textarea:focus {
        outline: none;
        border-color: rgba(37, 99, 235, 0.45);
        box-shadow: 0 0 0 3px var(--accent-soft);
      }

      .form-field textarea {
        min-height: 120px;
        resize: vertical;
      }

      .form-actions {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
      }

      .form-actions button {
        border: none;
        border-radius: 12px;
        background: var(--accent);
        color: #fff;
        padding: 0.9rem 1rem;
        font-size: 1rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
      }

      .form-actions button:hover {
        transform: translateY(-1px);
        background: var(--accent-hover);
        box-shadow: 0 12px 28px rgba(37, 99, 235, 0.28);
      }

      .form-actions small {
        color: var(--muted);
        font-size: 0.8125rem;
        text-align: center;
      }

      footer {
        padding: 2.5rem 1.5rem;
        text-align: center;
        color: var(--muted);
        font-size: 0.8125rem;
        border-top: 1px solid var(--line);
        background: var(--surface);
        margin-top: 2rem;
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
            <h1>SR Mac Shop</h1>
            <p class="brand-tagline">Premier Mac Studio &amp; Concierge</p>
          </div>
        </div>
        <nav class="site-nav" id="site-nav" aria-label="Shop">
          <button type="button" class="nav-menu-btn" aria-expanded="false" aria-controls="store-nav-menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
            Menu
          </button>
          <div class="nav-links" id="store-nav-menu">
            <a href="/"<?= store_nav_attrs('home', $navPath) ?>>Home</a>
            <a href="/#inventory-list"<?= store_nav_attrs('devices', $navPath) ?>>Devices</a>
            <a href="/login"<?= store_nav_attrs('login', $navPath) ?>>Sign in</a>
          </div>
        </nav>
      </div>
    </header>
    <main>
      <?php if ($flash): ?>
        <?php $flashStatus = ($flash['status'] ?? 'success') === 'error' ? 'error' : 'success'; ?>
        <div class="store-flash store-flash--<?= htmlspecialchars($flashStatus) ?>" role="status">
          <?= htmlspecialchars($flash['message'] ?? '') ?>
        </div>
      <?php endif; ?>
      <?php include $view; ?>
    </main>
    <footer>
      &copy; <?= date('Y') ?> Mac POS — Boutique Mac &amp; PC Retail
    </footer>
    <div class="login-launcher" aria-hidden="true">
      <span>Staff access</span>
      <a href="/login">Open console</a>
    </div>
    <script>
      (function () {
        const body = document.body;
        const launcher = document.querySelector('.login-launcher');
        const brand = document.querySelector('.brand');
        if (!launcher || !body) {
          return;
        }

        let hideTimer;
        const scheduleHide = () => {
          clearTimeout(hideTimer);
          hideTimer = setTimeout(() => {
            body.removeAttribute('data-login-visible');
            launcher.setAttribute('aria-hidden', 'true');
          }, 6000);
        };

        const showLauncher = () => {
          body.dataset.loginVisible = 'true';
          launcher.setAttribute('aria-hidden', 'false');
          scheduleHide();
        };

        document.addEventListener('keydown', (event) => {
          const key = (event.key || '').toLowerCase();
          if ((event.metaKey || event.ctrlKey) && event.altKey && key === 'l') {
            event.preventDefault();
            showLauncher();
          }
        });

        if (brand) {
          let tapCount = 0;
          let tapTimer;
          brand.addEventListener('click', () => {
            tapCount += 1;
            clearTimeout(tapTimer);
            tapTimer = setTimeout(() => {
              tapCount = 0;
            }, 700);
            if (tapCount >= 3) {
              showLauncher();
              tapCount = 0;
            }
          });
        }

        launcher.addEventListener('mouseenter', () => clearTimeout(hideTimer));
        launcher.addEventListener('mouseleave', scheduleHide);
        launcher.addEventListener('focusout', scheduleHide);
      })();

      (function () {
        const nav = document.getElementById('site-nav');
        const btn = nav?.querySelector('.nav-menu-btn');
        const mq = window.matchMedia('(max-width: 720px)');

        if (!nav || !btn) {
          return;
        }

        const setOpen = (open) => {
          nav.classList.toggle('is-open', open);
          btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        btn.addEventListener('click', () => {
          if (!mq.matches) {
            return;
          }
          setOpen(!nav.classList.contains('is-open'));
        });

        nav.querySelectorAll('#store-nav-menu a').forEach((link) => {
          link.addEventListener('click', () => {
            if (mq.matches) {
              setOpen(false);
            }
          });
        });

        document.addEventListener('click', (e) => {
          if (!mq.matches || !nav.classList.contains('is-open')) {
            return;
          }
          if (e.target instanceof Node && !nav.contains(e.target)) {
            setOpen(false);
          }
        });

        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && nav.classList.contains('is-open')) {
            setOpen(false);
            btn.focus();
          }
        });

        mq.addEventListener('change', () => {
          if (!mq.matches) {
            setOpen(false);
          }
        });
      })();
    </script>
  </body>
</html>
