<?php
$available = array_values(array_filter(
    $items,
    fn ($item) => (int) $item['quantity_on_hand'] > 0
));

$featured = $available[0] ?? null;

function categoryLabel(array $item): string
{
    $model = strtolower($item['model']);
    if (str_contains($model, 'mac')) {
        return 'MacBook';
    }
    if (str_contains($model, 'book') || str_contains($model, 'pc')) {
        return 'PC';
    }
    return 'Accessory';
}

function galleryImages(array $item): array
{
    if (empty($item['gallery_images'])) {
        return [];
    }
    $decoded = json_decode((string) $item['gallery_images'], true);
    return is_array($decoded) ? $decoded : [];
}

function fallbackImage(string $category): string
{
    return match ($category) {
        'MacBook' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1000&q=80',
        'PC' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&w=1000&q=80',
        default => 'https://images.unsplash.com/photo-1517059224940-d4af9eec41e5?auto=format&w=1000&q=80',
    };
}
?>

<section class="showroom-hero">
  <div class="hero-content">
    <p class="chip">Flagship Gallery</p>
    <h1>Experience Macs and PCs the way they were meant to be seen.</h1>
    <p>Hand-curated inventory, premium finishes, and same-day pickup. Navigate through ultra-high-resolution imagery inspired by Apple’s showcase.</p>
    <div class="hero-actions" style="margin-top:1rem;">
      <a href="#inventory-list" style="text-decoration:none;"><button>Explore lineup</button></a>
      <a href="/sales/new" style="text-decoration:none;"><button class="ghost-btn">Staff checkout</button></a>
    </div>
  </div>
  <div class="hero-media">
    <?php if ($featured): ?>
      <?php
        $featCategory = categoryLabel($featured);
        $featGallery = galleryImages($featured);
        $featImage = $featured['hero_image'] ?: ($featGallery[0] ?? fallbackImage($featCategory));
      ?>
      <img src="<?= htmlspecialchars($featImage) ?>" alt="<?= htmlspecialchars($featured['model']) ?>" loading="lazy" />
      <div class="hero-floating-badges">
        <span style="top:12%;left:65%;"><?= $featCategory ?> Pro</span>
        <span style="bottom:12%;right:20%;"><?= (int) $featured['storage_capacity'] ?> GB SSD</span>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($featured): ?>
  <section class="showroom-highlight">
    <div class="featured-card">
      <p class="chip"><?= $featCategory ?> Highlight</p>
      <h2 style="margin:0.4rem 0;"><?= htmlspecialchars($featured['model']) ?></h2>
      <p class="muted"><?= (int) $featured['storage_capacity'] ?> GB • <?= htmlspecialchars($featured['color']) ?></p>
      <h3 style="margin:0.6rem 0;">$<?= number_format((float) $featured['list_price'], 2) ?></h3>
      <a class="product-cta" href="/store/product?slug=<?= urlencode($featured['slug'] ?: strtolower($featured['sku'])) ?>">
        View &amp; book
      </a>
    </div>
    <div class="panel" style="min-height:320px;">
      <h3>Gallery</h3>
      <div class="gallery-thumbs" style="margin-top:0.8rem;">
        <?php foreach ($featGallery as $thumb): ?>
          <img src="<?= htmlspecialchars($thumb) ?>" alt="Gallery image" loading="lazy" />
        <?php endforeach; ?>
      </div>
      <p class="muted" style="margin-top:1rem;">Stock: <?= (int) $featured['quantity_on_hand'] ?> units • SKU <?= htmlspecialchars($featured['sku']) ?></p>
    </div>
  </section>
<?php endif; ?>

<section id="inventory-list" style="margin-top:1.5rem;">
  <div class="pill-filters">
    <span class="pill">All</span>
    <span class="pill">MacBook</span>
    <span class="pill">PC</span>
    <span class="pill">Accessory</span>
    <span class="pill">In stock</span>
  </div>
  <div class="product-grid">
    <?php if (empty($available)): ?>
      <div class="panel" style="grid-column: 1 / -1; text-align:center;">
        <p style="color:var(--muted);margin:0;">No devices available right now. Check back soon!</p>
      </div>
    <?php else: ?>
      <?php foreach ($available as $item): ?>
        <?php
          $category = categoryLabel($item);
          $lowStock = (int) $item['quantity_on_hand'] <= 2;
          $gallery = galleryImages($item);
          $primary = $item['hero_image'] ?: ($gallery[0] ?? fallbackImage($category));
          $slug = $item['slug'] ?: strtolower($item['sku']);
          $price = $item['online_price'] ?? $item['list_price'];
        ?>
        <article class="product-card" data-product="<?= htmlspecialchars($item['sku']) ?>">
          <div class="badge-row">
            <span class="badge"><?= $category ?></span>
            <?php if ($lowStock): ?>
              <span class="badge warning">Low stock</span>
            <?php endif; ?>
          </div>
          <h3><?= htmlspecialchars($item['model']) ?></h3>
          <p class="sku">SKU: <?= htmlspecialchars($item['sku']) ?></p>
          <div class="product-media" data-full="<?= htmlspecialchars($primary) ?>">
            <img src="<?= htmlspecialchars($primary) ?>" alt="<?= htmlspecialchars($item['model']) ?>" loading="lazy" />
            <div class="zoom-pane"></div>
          </div>
          <?php if ($gallery): ?>
            <div class="gallery-thumbs" role="list">
              <?php foreach ($gallery as $thumb): ?>
                <button type="button" class="thumb" data-image="<?= htmlspecialchars($thumb) ?>">
                  <img src="<?= htmlspecialchars($thumb) ?>" alt="Alternate view" loading="lazy" />
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <ul class="spec-list">
            <li><?= (int) $item['storage_capacity'] ?> GB • <?= htmlspecialchars($item['color']) ?></li>
            <li>Cost: $<?= number_format((float) $item['cost_price'], 2) ?></li>
            <li>List: $<?= number_format((float) $item['list_price'], 2) ?></li>
            <li>Available: <?= (int) $item['quantity_on_hand'] ?> units</li>
          </ul>
          <div class="price-row">
            <div>
              <p class="price">$<?= number_format((float) $price, 2) ?></p>
              <p class="muted">Add sales tax at checkout</p>
            </div>
            <a class="product-cta" href="/store/product?slug=<?= urlencode($slug) ?>">
              View &amp; book
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<script>
  (function () {
    document.querySelectorAll('.product-card').forEach((card) => {
      const media = card.querySelector('.product-media');
      if (!media) return;
      const img = media.querySelector('img');
      const zoom = media.querySelector('.zoom-pane');
      const thumbs = card.querySelectorAll('.thumb');

      thumbs.forEach((btn) => {
        btn.addEventListener('click', () => {
          img.src = btn.dataset.image;
          media.dataset.full = btn.dataset.image;
        });
      });

      function moveZoom(e) {
        const rect = media.getBoundingClientRect();
        const xPercent = ((e.clientX - rect.left) / rect.width) * 100;
        const yPercent = ((e.clientY - rect.top) / rect.height) * 100;
        zoom.style.backgroundImage = `url(${media.dataset.full || img.src})`;
        zoom.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
        zoom.style.display = 'block';
      }

      media.addEventListener('mousemove', moveZoom);
      media.addEventListener('mouseenter', () => {
        zoom.style.backgroundSize = '200%';
        zoom.style.display = 'block';
      });
      media.addEventListener('mouseleave', () => {
        zoom.style.display = 'none';
      });
    });
  })();
</script>

