<?php
$gallery = [];
if (!empty($product['gallery_images'])) {
    $decoded = json_decode((string) $product['gallery_images'], true);
    $gallery = is_array($decoded) ? array_filter($decoded) : [];
}

function detectCategory(array $product): string
{
    $model = strtolower($product['model'] ?? '');
    if (str_contains($model, 'mac')) {
        return 'MacBook';
    }
    if (str_contains($model, 'book') || str_contains($model, 'pc')) {
        return 'PC';
    }
    return 'Accessory';
}

function fallbackHero(array $product): string
{
    $category = detectCategory($product);
    return match ($category) {
        'MacBook' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1600&q=80',
        'PC' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&w=1600&q=80',
        default => 'https://images.unsplash.com/photo-1517059224940-d4af9eec41e5?auto=format&w=1600&q=80',
    };
}

function isVideoSource(string $src): bool
{
    $path = strtolower(parse_url($src, PHP_URL_PATH) ?? $src);
    return preg_match('/\.(mp4|mov|webm|m4v)$/', $path) || str_contains($src, 'youtube.com') || str_contains($src, 'youtu.be');
}

function youtubeId(string $url): ?string
{
    if (preg_match('/youtu\.be\/([^\?&]+)/', $url, $m)) {
        return $m[1];
    }
    if (preg_match('/youtube\.com\/watch\?v=([^\?&]+)/', $url, $m)) {
        return $m[1];
    }
    if (preg_match('/youtube\.com\/embed\/([^\?&]+)/', $url, $m)) {
        return $m[1];
    }
    return null;
}

function youtubeEmbedUrl(string $url): ?string
{
    $id = youtubeId($url);
    return $id ? "https://www.youtube.com/embed/{$id}?rel=0&showinfo=0" : null;
}

function youtubeThumbnail(string $url): ?string
{
    $id = youtubeId($url);
    return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
}

$category = detectCategory($product);
$price = $product['online_price'] ?? $product['list_price'];
$suggestedDeposit = $price ? round($price * 0.1, 2) : 0;

$mediaSources = [];
if (!empty($product['hero_image'])) {
    $mediaSources[] = $product['hero_image'];
}
foreach ($gallery as $src) {
    $mediaSources[] = $src;
}
if (!$mediaSources) {
    $mediaSources[] = fallbackHero($product);
}

$mediaItems = [];
foreach ($mediaSources as $src) {
    $isVideo = isVideoSource($src);
    $embed = $isVideo ? (youtubeEmbedUrl($src) ?? $src) : '';
    $preview = $isVideo ? (youtubeThumbnail($src) ?? ($product['hero_image'] ?: fallbackHero($product))) : $src;
    $mediaItems[] = [
        'type' => $isVideo ? 'video' : 'image',
        'src' => $src,
        'embed' => $embed,
        'preview' => $preview,
    ];
}

$primaryMedia = $mediaItems[0] ?? null;
?>

<section class="product-showcase">
  <div class="showcase-grid">
    <div class="showcase-media">
      <a href="/store" class="link-btn">← Back to showroom</a>
      <div class="media-gallery">
        <div class="media-tray" data-role="media-tray">
          <?php foreach ($mediaItems as $index => $media): ?>
            <button
              class="media-thumb<?= $index === 0 ? ' is-active' : '' ?>"
              type="button"
              data-role="media-thumb"
              data-type="<?= htmlspecialchars($media['type']) ?>"
              data-src="<?= htmlspecialchars($media['src']) ?>"
              data-embed="<?= htmlspecialchars($media['embed']) ?>"
              data-preview="<?= htmlspecialchars($media['preview']) ?>"
              data-alt="<?= htmlspecialchars($product['model']) ?>"
            >
              <?php if ($media['type'] === 'image'): ?>
                <img src="<?= htmlspecialchars($media['preview']) ?>" alt="Thumbnail" loading="lazy" />
              <?php else: ?>
                <div class="thumb-video">
                  <img src="<?= htmlspecialchars($media['preview']) ?>" alt="Video thumbnail" loading="lazy" />
                  <span>▶</span>
                </div>
              <?php endif; ?>
            </button>
          <?php endforeach; ?>
        </div>
        <?php if ($primaryMedia): ?>
          <div class="media-stage" data-role="media-stage" data-type="<?= htmlspecialchars($primaryMedia['type']) ?>">
            <?php if ($primaryMedia['type'] === 'image'): ?>
              <div class="media-preview" data-type="image">
                <img src="<?= htmlspecialchars($primaryMedia['src']) ?>" alt="<?= htmlspecialchars($product['model']) ?>" loading="lazy" />
                <div class="zoom-pane"></div>
              </div>
            <?php else: ?>
              <?php if ($primaryMedia['embed'] && str_contains($primaryMedia['embed'], 'youtube.com')): ?>
                <div class="media-video media-video--embed">
                  <iframe src="<?= htmlspecialchars($primaryMedia['embed']) ?>" allowfullscreen loading="lazy"></iframe>
                </div>
              <?php else: ?>
                <video controls playsinline>
                  <source src="<?= htmlspecialchars($primaryMedia['src']) ?>" type="video/mp4" />
                </video>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="showcase-info panel">
      <p class="chip"><?= htmlspecialchars($category) ?></p>
      <h1><?= htmlspecialchars($product['model']) ?></h1>
      <p class="showcase-description"><?= nl2br(htmlspecialchars($product['web_description'] ?? 'Ready for immediate pickup.')) ?></p>
      <div class="showcase-price">
        <strong>$<?= number_format((float) $price, 2) ?></strong>
        <small>List price: $<?= number_format((float) $product['list_price'], 2) ?> · <?= (int) $product['quantity_on_hand'] ?> in stock</small>
      </div>
      <ul class="spec-list">
        <li>Storage: <?= (int) $product['storage_capacity'] ?> GB</li>
        <li>Color: <?= htmlspecialchars($product['color']) ?></li>
        <li>SKU: <?= htmlspecialchars($product['sku']) ?></li>
      </ul>
    </div>
  </div>
</section>

<section class="booking-card">
  <div class="booking-card__header">
    <div>
      <p class="chip">Reserve today</p>
      <h2><?= htmlspecialchars($product['model']) ?></h2>
      <p><?= (int) $product['quantity_on_hand'] ?> units ready for pickup · secure your slot in under a minute.</p>
    </div>
    <div class="booking-card__price">
      <span>$<?= number_format((float) $price, 2) ?></span>
      <small>Suggested deposit $<?= number_format($suggestedDeposit, 2) ?></small>
    </div>
  </div>
  <form method="post" action="/store/book" class="booking-form">
    <input type="hidden" name="inventory_id" value="<?= (int) $product['id'] ?>" />
    <div class="form-section">
      <h3>Contact</h3>
      <div class="form-grid">
        <label class="form-field">
          <span>Full name</span>
          <input name="customer_name" placeholder="Alex Kim" required />
        </label>
        <label class="form-field">
          <span>Email</span>
          <input type="email" name="customer_email" placeholder="alex@srmac.com" required />
        </label>
        <label class="form-field">
          <span>Phone</span>
          <input name="customer_phone" placeholder="+1 555 000 1234" required />
        </label>
        <label class="form-field">
          <span>Quantity</span>
          <input type="number" name="quantity" min="1" max="<?= max(1, (int) $product['quantity_on_hand']) ?>" value="1" />
        </label>
      </div>
    </div>
    <div class="form-section">
      <h3>Pickup preference</h3>
      <div class="form-grid">
        <label class="form-field">
          <span>Date</span>
          <input type="date" name="preferred_date" />
        </label>
        <label class="form-field">
          <span>Time</span>
          <input type="time" name="preferred_time" />
        </label>
        <label class="form-field">
          <span>Deposit (optional)</span>
          <input type="number" step="0.01" name="deposit_amount" value="<?= htmlspecialchars($suggestedDeposit) ?>" />
        </label>
      </div>
    </div>
    <div class="form-section">
      <h3>Notes</h3>
      <label class="form-field">
        <span>Anything else?</span>
        <textarea name="notes" rows="4" placeholder="Example: Need it gift wrapped, prefer weekend pickup…"></textarea>
      </label>
    </div>
    <div class="form-actions">
      <button type="submit">Reserve now</button>
      <small>We’ll confirm availability via email or Telegram within the hour.</small>
    </div>
  </form>
</section>

<script>
  (function () {
    const stage = document.querySelector('[data-role="media-stage"]');
    const thumbButtons = document.querySelectorAll('[data-role="media-thumb"]');

    if (!stage || !thumbButtons.length) {
      return;
    }

    const scrollIntoView = (btn) => {
      const parent = btn.parentElement;
      if (!parent || !(parent instanceof HTMLElement)) return;
      const parentRect = parent.getBoundingClientRect();
      const btnRect = btn.getBoundingClientRect();
      const isVertical = parent.scrollHeight > parent.clientHeight && parent.clientHeight > 0;

      if (isVertical) {
        if (btnRect.top < parentRect.top || btnRect.bottom > parentRect.bottom) {
          parent.scrollTo({
            top: btn.offsetTop - parent.clientHeight / 2 + btn.clientHeight / 2,
            behavior: 'smooth',
          });
        }
      } else if (btnRect.left < parentRect.left || btnRect.right > parentRect.right) {
        parent.scrollTo({
          left: btn.offsetLeft - parent.clientWidth / 2 + btn.clientWidth / 2,
          behavior: 'smooth',
        });
      }
    };

    const attachZoom = (preview) => {
      if (!preview) return;
      const img = preview.querySelector('img');
      const zoom = preview.querySelector('.zoom-pane');
      if (!img || !zoom) return;
      preview.addEventListener('mousemove', (e) => {
        const rect = preview.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        zoom.style.backgroundImage = `url(${img.src})`;
        zoom.style.backgroundPosition = `${x}% ${y}%`;
        zoom.style.display = 'block';
      });
      preview.addEventListener('mouseenter', () => {
        zoom.style.backgroundSize = '180%';
        zoom.style.display = 'block';
      });
      preview.addEventListener('mouseleave', () => {
        zoom.style.display = 'none';
      });
    };

    if (stage.dataset.type === 'image') {
      attachZoom(stage.querySelector('.media-preview'));
    }

    const renderMedia = (media) => {
      stage.dataset.type = media.type;
      if (media.type === 'image') {
        stage.innerHTML = `
          <div class="media-preview" data-type="image">
            <img src="${media.src}" alt="${media.alt}" loading="lazy" />
            <div class="zoom-pane"></div>
          </div>
        `;
        attachZoom(stage.querySelector('.media-preview'));
      } else if (media.embed && media.embed.includes('youtube')) {
        stage.innerHTML = `
          <div class="media-video media-video--embed">
            <iframe src="${media.embed}" allowfullscreen loading="lazy"></iframe>
          </div>
        `;
      } else {
        stage.innerHTML = `
          <video controls playsinline>
            <source src="${media.src}" type="video/mp4" />
          </video>
        `;
      }
    };

    thumbButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        thumbButtons.forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');
        scrollIntoView(btn);
        const media = {
          type: btn.dataset.type,
          src: btn.dataset.src,
          embed: btn.dataset.embed,
          preview: btn.dataset.preview,
          alt: btn.dataset.alt || ''
        };
        renderMedia(media);
      });
    });
  })();
</script>

