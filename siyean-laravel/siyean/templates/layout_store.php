<?php
$navPath = $request_path ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

/** Highlight shop nav items based on URL (external links are never marked current). */
function store_menu_path_matches(string $href, string $currentPath): bool
{
    $href = trim($href);
    if ($href === '') {
        return false;
    }
    if (preg_match('#^(https?://|mailto:|tel:)#i', $href) === 1) {
        return false;
    }

    $path = parse_url($href, PHP_URL_PATH);
    if ($path === null || $path === '') {
        $path = '/';
    }
    $fragment = parse_url($href, PHP_URL_FRAGMENT);

    if (($fragment ?? '') !== '') {
        if ($path === '/') {
            return in_array($currentPath, ['/', '/store'], true)
                || str_starts_with($currentPath, '/store/product');
        }
    }

    if ($path === '/') {
        return in_array($currentPath, ['/', '/store'], true);
    }
    if ($path === '/login') {
        return str_starts_with($currentPath, '/login');
    }

    return $currentPath === $path || str_starts_with($currentPath, rtrim($path, '/') . '/');
}

function store_menu_link_attrs(string $href, string $currentPath): string
{
    return store_menu_path_matches($href, $currentPath) ? ' class="active" aria-current="page"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Shop Mac computers online — MacBook, iMac, Mac mini, Mac Studio, and accessories. Premium inventory with clear pricing and secure booking at SR Mac Shop." />
    <title>SR Mac Shop — Mac Computers &amp; Accessories</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Inter:wght@500;600;700&display=swap"
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
        --page: #f5f5f7;
        --accent: #0071e3;
        --accent-hover: #0077ed;
        --accent-soft: rgba(0, 113, 227, 0.1);
        --radius: 1rem;
        --radius-lg: 1.35rem;
        --shadow-xs: 0 1px 2px rgba(12, 18, 34, 0.04);
        --shadow-sm: 0 4px 14px rgba(12, 18, 34, 0.06);
        --shadow-md: 0 12px 32px rgba(12, 18, 34, 0.08);
        --font-body: "DM Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        --font-display: "Inter", -apple-system, BlinkMacSystemFont, var(--font-body);
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
        border-color: rgba(0, 113, 227, 0.15);
      }

      .nav-links a.active {
        color: var(--accent);
        background: var(--accent-soft);
        border-color: rgba(0, 113, 227, 0.22);
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
          border-color: rgba(0, 113, 227, 0.25);
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
        width: 100%;
        height: 100%;
      }

      .media-preview.image-magnifier {
        overflow: visible;
      }

      .media-preview:not(.image-magnifier) {
        overflow: hidden;
      }

      .image-magnifier__frame {
        position: relative;
        overflow: hidden;
        width: 100%;
        height: 100%;
        border-radius: inherit;
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
        border-color: rgba(0, 113, 227, 0.35);
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
        overflow: visible;
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
        border-color: rgba(0, 113, 227, 0.15);
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

      .product-media.image-magnifier {
        position: relative;
        margin-bottom: 0.75rem;
        overflow: visible;
      }

      .product-media.image-magnifier .image-magnifier__frame {
        border-radius: var(--radius);
        border: 1px solid var(--line);
      }

      .product-media.image-magnifier img {
        width: 100%;
        display: block;
        aspect-ratio: 4 / 3;
        object-fit: cover;
      }

      /* Advanced magnifier: lens + floating zoom panel */
      .image-magnifier__lens {
        position: absolute;
        box-sizing: border-box;
        border: 2px solid rgba(255, 255, 255, 0.92);
        box-shadow:
          0 0 0 1px rgba(0, 0, 0, 0.12),
          0 10px 28px rgba(0, 0, 0, 0.22);
        border-radius: 11px;
        pointer-events: none;
        z-index: 3;
        backdrop-filter: saturate(1.08);
      }

      .image-magnifier__panel {
        position: fixed;
        z-index: 200;
        pointer-events: none;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--line-strong);
        box-shadow:
          0 20px 50px rgba(12, 18, 34, 0.2),
          0 0 0 1px rgba(255, 255, 255, 0.5) inset;
        background: var(--surface);
      }

      .image-magnifier--card .image-magnifier__panel {
        width: 176px;
        height: 176px;
      }

      .image-magnifier--product .image-magnifier__panel {
        width: min(310px, 46vw);
        height: min(310px, 46vw);
      }

      .image-magnifier__panel-fill {
        display: block;
        width: 100%;
        height: 100%;
        background-repeat: no-repeat;
        background-color: #0f172a;
      }

      .image-magnifier__zoom-ui {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 6;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 8px;
        border-radius: 11px;
        background: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.12);
      }

      .image-magnifier__zoom-ui button {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        font-family: inherit;
      }

      .image-magnifier__zoom-ui button:hover {
        background: rgba(255, 255, 255, 0.26);
      }

      .image-magnifier__zoom-ui [data-zoom-label] {
        font-size: 0.75rem;
        font-weight: 700;
        color: rgba(248, 250, 252, 0.95);
        min-width: 2.75rem;
        text-align: center;
        font-variant-numeric: tabular-nums;
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
        border-color: rgba(0, 113, 227, 0.45);
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
        box-shadow: 0 12px 28px rgba(0, 113, 227, 0.28);
      }

      .form-actions small {
        color: var(--muted);
        font-size: 0.8125rem;
        text-align: center;
      }

      /* —— Ecommerce Mac storefront (home / catalog) —— */
      .eco-hero {
        position: relative;
      }

      .eco-hero__bg {
        position: absolute;
        inset: 0;
        background:
          radial-gradient(ellipse 80% 60% at 20% 0%, rgba(0, 113, 227, 0.28), transparent 55%),
          radial-gradient(ellipse 50% 45% at 95% 90%, rgba(88, 86, 214, 0.15), transparent 50%);
        pointer-events: none;
      }

      .eco-hero__content {
        position: relative;
        z-index: 1;
      }

      .eco-hero__eyebrow {
        letter-spacing: 0.08em;
      }

      .eco-hero__lead {
        margin: 0;
        color: rgba(226, 232, 240, 0.88);
        font-size: 1.02rem;
        line-height: 1.65;
        max-width: 38ch;
      }

      .eco-hero__actions {
        margin-top: 1.15rem;
      }

      .eco-hero__fineprint {
        margin: 1.25rem 0 0;
        font-size: 0.8125rem;
        color: rgba(148, 163, 184, 0.95);
      }

      .eco-hero__fineprint a {
        color: #93c5fd;
        font-weight: 600;
      }

      .eco-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.72rem 1.35rem;
        border-radius: 980px;
        font-weight: 600;
        font-size: 0.9375rem;
        text-decoration: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
      }

      .eco-btn--primary {
        background: #fff;
        color: #1d1d1f;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
      }

      .eco-btn--primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.22);
      }

      .eco-btn--ghost {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #f1f5f9;
      }

      .eco-btn--ghost:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-1px);
      }

      .eco-hero__media {
        position: relative;
        z-index: 1;
      }

      .eco-trust {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin: 0 0 2rem;
      }

      .eco-trust__item {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius-lg);
        padding: 1.1rem 1.25rem;
        box-shadow: var(--shadow-xs);
      }

      .eco-trust__item strong {
        display: block;
        font-size: 0.9375rem;
        margin-bottom: 0.35rem;
        font-family: var(--font-display);
      }

      .eco-trust__item span {
        font-size: 0.8125rem;
        color: var(--muted);
        line-height: 1.45;
      }

      .eco-featured-copy {
        color: var(--ink-soft);
        font-size: 0.9375rem;
        line-height: 1.55;
        margin: 0.5rem 0 0;
        flex-grow: 1;
      }

      .eco-price-block {
        margin: 1rem 0 0;
        padding-top: 1rem;
        border-top: 1px solid var(--line);
      }

      .eco-price {
        font-family: var(--font-display);
        font-size: clamp(1.5rem, 3vw, 1.85rem);
        font-weight: 700;
        letter-spacing: -0.03em;
      }

      .eco-price-was {
        display: block;
        font-size: 0.8125rem;
        color: var(--muted);
        text-decoration: line-through;
        margin-top: 0.25rem;
      }

      .eco-featured-gallery .gallery-thumbs img {
        width: 64px;
        height: 64px;
      }

      .eco-featured-single {
        width: 100%;
        border-radius: var(--radius);
        border: 1px solid var(--line);
        margin-top: 0.75rem;
        object-fit: cover;
        aspect-ratio: 16 / 10;
      }

      .eco-value {
        margin: 2.5rem 0;
        padding: clamp(1.5rem, 4vw, 2.25rem);
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: calc(var(--radius-lg) + 4px);
        box-shadow: var(--shadow-xs);
      }

      .eco-value h2 {
        margin: 0 0 0.5rem;
        font-size: clamp(1.35rem, 3vw, 1.65rem);
      }

      .eco-value__intro {
        margin: 0 0 1.25rem;
        color: var(--muted);
        max-width: 52ch;
        font-size: 0.9375rem;
      }

      .eco-value__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
      }

      .eco-value__card {
        padding: 1.1rem 1.2rem;
        border-radius: var(--radius);
        background: var(--surface-elevated);
        border: 1px solid var(--line);
      }

      .eco-value__card h3 {
        margin: 0 0 0.45rem;
        font-size: 1rem;
      }

      .eco-value__card p {
        margin: 0;
        color: var(--muted);
        font-size: 0.875rem;
        line-height: 1.55;
      }

      .eco-catalog__head {
        margin-bottom: 1.25rem;
      }

      .eco-catalog__title {
        margin: 0 0 0.35rem;
        font-size: clamp(1.35rem, 3vw, 1.75rem);
      }

      .eco-catalog__subtitle {
        margin: 0;
        color: var(--muted);
        font-size: 0.9375rem;
      }

      .eco-filters .pill {
        cursor: pointer;
        font-family: inherit;
        transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease;
      }

      .eco-filters .pill.is-active {
        border-color: rgba(0, 113, 227, 0.35);
        background: var(--accent-soft);
        color: var(--accent);
      }

      .eco-filters .pill:hover:not(.is-active) {
        border-color: var(--line-strong);
      }

      .eco-save {
        color: #047857;
        font-weight: 600;
        font-size: 0.8125rem;
      }

      .eco-was {
        text-decoration: line-through;
        font-size: 0.8125rem !important;
      }

      .eco-price-row .eco-cta-solid {
        white-space: nowrap;
      }

      .eco-cta-solid {
        background: var(--accent) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 980px;
        padding: 0.55rem 1.2rem !important;
      }

      .eco-cta-solid:hover {
        background: var(--accent-hover) !important;
        color: #fff !important;
      }

      .eco-empty {
        text-align: center;
        padding: 2.5rem 1.5rem !important;
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
    <script src="/assets/image-magnifier.js"></script>
    <header>
      <div class="main-header">
        <div class="brand">
          <div class="brand-logo">
            <img src="/assets/sr-mac-logo.svg" alt="SR Mac Shop logo" />
          </div>
          <div>
            <h1>SR Mac Shop</h1>
            <p class="brand-tagline">Online Mac store · Pickup &amp; concierge</p>
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
            <?php foreach (($store_menu_items ?? []) as $item): ?>
              <a href="<?= htmlspecialchars((string) $item['href']) ?>"<?= store_menu_link_attrs((string) $item['href'], $navPath) ?>><?= htmlspecialchars((string) $item['label']) ?></a>
            <?php endforeach; ?>
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
      &copy; <?= date('Y') ?> SR Mac Shop — Apple computers &amp; accessories. MacBook, iMac, Mac mini &amp; more.
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
