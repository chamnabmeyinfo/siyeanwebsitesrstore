<?php ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mac POS – Sign in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700&family=Space+Grotesk:wght@600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      :root {
        font-family: "DM Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background: radial-gradient(ellipse 120% 90% at 50% 0%, #1e2d4a 0%, #0a0e18 55%, #05070f 100%);
        color: #e8edf5;
      }

      body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
      }
      .auth-card {
        background: rgba(15, 23, 42, 0.8);
        border-radius: 1.5rem;
        padding: 2.5rem;
        width: min(420px, 100%);
        border: 1px solid rgba(59, 130, 246, 0.2);
        box-shadow: 0 25px 60px rgba(2, 6, 23, 0.8);
      }
      h1 {
        margin: 0 0 0.5rem;
        font-family: "Space Grotesk", "DM Sans", sans-serif;
        letter-spacing: -0.02em;
      }
      label {
        display: block;
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #cbd5f5;
      }
      input {
        width: 100%;
        padding: 0.7rem 0.9rem;
        margin-top: 0.35rem;
        border-radius: 0.8rem;
        border: 1px solid rgba(148, 163, 184, 0.3);
        background: rgba(15, 23, 42, 0.6);
        color: #fff;
      }
      button {
        margin-top: 1.5rem;
        width: 100%;
        padding: 0.85rem;
        border-radius: 0.9rem;
        border: none;
        background: linear-gradient(120deg, #2563eb, #38bdf8);
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        cursor: pointer;
      }
      .flash {
        padding: 0.85rem;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
      }
      .flash.error {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
      }
      .flash.success {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
      }
    </style>
  </head>
  <body>
    <div class="auth-card">
      <?php if ($flash): ?>
        <div class="flash <?= htmlspecialchars($flash['status'] ?? 'success') ?>">
          <?= htmlspecialchars($flash['message'] ?? '') ?>
        </div>
      <?php endif; ?>
      <?php include $view; ?>
    </div>
  </body>
</html>

