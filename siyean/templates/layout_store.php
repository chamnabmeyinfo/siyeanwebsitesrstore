<?php ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mac POS Showroom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet" />
    <style>
      :root {
        font-family: "Space Grotesk", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: #0f172a;
        background: #f5f7fb;
      }
      body {
        margin: 0;
        background: #f5f7fb;
        color: #0f172a;
      }
      header {
        background: transparent;
        padding: clamp(1rem, 4vw, 3rem);
      }
      .main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem clamp(1rem, 4vw, 2rem);
        border-radius: 1.1rem;
        background: linear-gradient(120deg, rgba(15, 23, 42, 0.06), rgba(37, 99, 235, 0.08));
        border: 1px solid rgba(15, 23, 42, 0.05);
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
        gap: 1.5rem;
      }
      .brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }
      .brand-logo {
        width: 52px;
        height: 52px;
        border-radius: 1.2rem;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.1);
        background: radial-gradient(circle at 30% 30%, #ffffff, #d1d5db);
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .brand-logo img {
        width: 44px;
        height: 44px;
        object-fit: contain;
      }
      .brand h1 {
        margin: 0;
        font-size: 1.6rem;
      }
      .brand-tagline {
        margin: 0;
        color: #64748b;
        font-size: 0.95rem;
      }
      .nav-links {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
      }
      .nav-links a {
        text-decoration: none;
        color: #0f172a;
        padding: 0.45rem 0.9rem;
        border-radius: 999px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #fff;
        font-weight: 500;
      }
      .login-launcher {
        position: fixed;
        right: 1.5rem;
        bottom: 1.5rem;
        background: #0f172a;
        color: #fff;
        padding: 0.85rem 1.2rem;
        border-radius: 999px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.3);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
        transform: translateY(6px);
        z-index: 30;
      }
      .login-launcher a {
        color: #fff;
        text-decoration: none;
        font-weight: 600;
      }
      body[data-login-visible="true"] .login-launcher {
        opacity: 1;
        pointer-events: all;
        transform: translateY(0);
      }
      .product-showcase {
        margin-top: 1.5rem;
      }
      .showcase-grid {
        display: grid;
        grid-template-columns: minmax(320px, 1.1fr) minmax(280px, 0.9fr);
        gap: clamp(1rem, 3vw, 2rem);
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
      .media-gallery {
        display: grid;
        grid-template-columns: minmax(72px, 90px) 1fr;
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
        }
        .media-thumb {
          width: 76px;
          height: 76px;
        }
      }
      .media-tray {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 480px;
        overflow-y: auto;
        padding-right: 0.25rem;
      }
      .media-stage {
        border-radius: 1.2rem;
        background: linear-gradient(145deg, #fff, #f8fafc);
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 25px 50px rgba(15, 23, 42, 0.08);
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
        border: 1px solid transparent;
        border-radius: 0.85rem;
        padding: 0;
        background: none;
        cursor: pointer;
        overflow: hidden;
      }
      .media-thumb img {
        width: 100%;
        height: 70px;
        object-fit: cover;
        display: block;
        border-radius: inherit;
      }
      .media-thumb.is-active {
        border-color: #0f172a;
      }
      .thumb-video {
        position: relative;
      }
      .thumb-video span {
        position: absolute;
        inset: 0;
        margin: auto;
        width: 24px;
        height: 24px;
        background: rgba(15, 23, 42, 0.8);
        color: #fff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
      }
      main {
        padding: 0 clamp(1rem, 4vw, 4rem) 4rem;
      }
      .panel {
        background: #fff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 25px 50px rgba(15, 23, 42, 0.08);
      }
      .chip {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
      }
      .hero-actions button {
        border: none;
        cursor: pointer;
        border-radius: 0.9rem;
        padding: 0.85rem 1.6rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
      }
      .hero-actions button:first-child {
        background: #0f172a;
        color: #fff;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
      }
      .hero-actions button.ghost-btn {
        background: transparent;
        border: 1px solid rgba(15, 23, 42, 0.2);
        color: #0f172a;
        box-shadow: none;
      }
      .showroom-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        padding: clamp(2rem, 6vw, 3.5rem);
        background: radial-gradient(circle at 25% 20%, rgba(59, 130, 246, 0.35), transparent 55%),
          linear-gradient(135deg, #05070f, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.15);
        min-height: 360px;
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: center;
        color: #e2e8f0;
        margin-bottom: 2rem;
      }
      .showroom-hero .hero-content {
        flex: 1 1 320px;
      }
      .showroom-hero .hero-media {
        flex: 1 1 320px;
        position: relative;
      }
      .showroom-hero .hero-media img {
        width: 100%;
        border-radius: 1.2rem;
        border: 1px solid rgba(148, 163, 184, 0.25);
        box-shadow: 0 25px 60px rgba(2, 6, 23, 0.6);
      }
      .hero-floating-badges {
        position: absolute;
        inset: 0;
        pointer-events: none;
      }
      .hero-floating-badges span {
        position: absolute;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        background: rgba(2, 6, 23, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.4);
        box-shadow: 0 15px 35px rgba(2, 6, 23, 0.6);
        font-size: 0.85rem;
      }
      .showroom-highlight {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      .featured-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.6));
        color: #e2e8f0;
        border-radius: 1.2rem;
        padding: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        min-height: 320px;
      }
      .featured-media img {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
        margin: 0.75rem 0;
        object-fit: cover;
      }
      .gallery-thumbs button {
        border: none;
        background: transparent;
        padding: 0;
        cursor: pointer;
      }
      .badge-row {
        display: flex;
        gap: 0.4rem;
      }
      .spec-list {
        list-style: none;
        padding: 0;
        margin: 0;
        color: #94a3b8;
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
        color: #94a3b8;
        margin: 0;
        font-size: 0.9rem;
      }
      .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
      }
      .product-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.25rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
      }
      .badge {
        padding: 0.3rem 0.8rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.08);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
      }
      .badge.warning {
        background: rgba(245, 158, 11, 0.2);
        color: #b45309;
      }
      .product-cta {
        text-decoration: none;
        border-radius: 0.6rem;
        padding: 0.65rem 1.2rem;
        border: 1px solid rgba(15, 23, 42, 0.2);
        font-weight: 600;
        color: #0f172a;
      }
      .gallery-thumbs {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
      }
      .gallery-thumbs img {
        width: 54px;
        height: 54px;
        border-radius: 0.5rem;
        object-fit: cover;
        border: 1px solid rgba(15, 23, 42, 0.08);
      }
      .media-thumb {
        border: none;
        padding: 0;
        background: transparent;
        cursor: pointer;
      }
      .thumb-video {
        position: relative;
      }
      .thumb-video span {
        position: absolute;
        inset: 0;
        margin: auto;
        width: 24px;
        height: 24px;
        background: rgba(15, 23, 42, 0.8);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
      }
      .product-media {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 0.75rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
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
        border-radius: 0.75rem;
        border: 1px solid rgba(15, 23, 42, 0.15);
        background-repeat: no-repeat;
        background-size: 200%;
        display: none;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.3);
      }
      .booking-card {
        margin-top: 1.5rem;
        background: #fff;
        border-radius: 1.5rem;
        padding: clamp(1.5rem, 4vw, 2.5rem);
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.12);
      }
      .booking-card__header {
        display: flex;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
      }
      .booking-card__price {
        text-align: right;
      }
      .booking-card__price span {
        display: block;
        font-size: 2.2rem;
        font-weight: 600;
        color: #0f172a;
      }
      .booking-card__price small {
        color: #94a3b8;
      }
      .booking-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
      }
      .form-section h3 {
        margin: 0 0 0.4rem;
        font-size: 1rem;
        color: #0f172a;
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
        font-size: 0.9rem;
        color: #475569;
      }
      .form-field input,
      .form-field textarea {
        border: 1px solid rgba(148, 163, 184, 0.35);
        border-radius: 0.9rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background: #f8fafc;
        color: #0f172a;
      }
      .form-field textarea {
        min-height: 120px;
        resize: vertical;
      }
      .form-actions {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
      }
      .form-actions button {
        border: none;
        border-radius: 1rem;
        background: #0f172a;
        color: #fff;
        padding: 0.95rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
      }
      .form-actions button:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 30px rgba(15, 23, 42, 0.25);
      }
      .form-actions small {
        color: #94a3b8;
      }
      footer {
        padding: 2rem;
        text-align: center;
        color: #94a3b8;
        font-size: 0.9rem;
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
            <p class="brand-tagline">Premier Mac Studio & Concierge</p>
          </div>
        </div>
        <nav>
          <div class="nav-links">
            <a href="/store">Showroom</a>
          </div>
        </nav>
      </div>
    </header>
    <main>
      <?php if ($flash): ?>
        <div class="panel" style="margin-bottom:1rem;border-left:4px solid <?= $flash['status'] === 'success' ? '#0fba81' : '#ef4444' ?>;">
          <?= htmlspecialchars($flash['message'] ?? '') ?>
        </div>
      <?php endif; ?>
      <?php include $view; ?>
    </main>
    <footer>
      &copy; <?= date('Y') ?> Mac POS — Boutique Mac & PC Retail
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
    </script>
  </body>
</html>

