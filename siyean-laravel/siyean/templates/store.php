<?php
$available = array_values(array_filter(
    $items,
    fn ($item) => (int) $item['quantity_on_hand'] > 0
));

$featured = $available[0] ?? null;

function categoryLabel(array $item): string
{
    $model = strtolower((string) $item['model']);
    if (str_contains($model, 'macbook') || (str_contains($model, 'book') && str_contains($model, 'mac'))) {
        return 'MacBook';
    }
    if (str_contains($model, 'imac') || str_contains($model, 'studio display')) {
        return 'Desktop';
    }
    if (str_contains($model, 'mac mini') || str_contains($model, 'macmini')) {
        return 'Desktop';
    }
    if (str_contains($model, 'mac studio') || str_contains($model, 'studio')) {
        return 'Desktop';
    }
    if (str_contains($model, 'mac pro') || str_contains($model, 'macpro')) {
        return 'Desktop';
    }
    if (str_contains($model, 'mac')) {
        return 'Mac';
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
        'MacBook' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&w=1200&q=85',
        'Desktop' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&w=1200&q=85',
        'Mac' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&w=1200&q=85',
        default => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&w=1200&q=85',
    };
}

$filterLabels = ['All', 'MacBook', 'Desktop', 'Mac', 'Accessory'];
?>

<section class="eco-hero showroom-hero" aria-labelledby="eco-hero-title">
  <div class="eco-hero__bg" aria-hidden="true"></div>
  <div class="hero-content eco-hero__content">
    <p class="chip eco-hero__eyebrow">Mac · Online store</p>
    <h1 id="eco-hero-title">Find your next Mac.</h1>
    <p class="eco-hero__lead">
      Browse our curated inventory of Apple computers — MacBook, iMac, Mac mini, and more — with clear specs,
      transparent pricing, and secure booking for pickup or consultation.
    </p>
    <div class="hero-actions eco-hero__actions">
      <a href="#inventory-list" class="eco-btn eco-btn--primary">Shop Macs</a>
      <a href="#why-us" class="eco-btn eco-btn--ghost">Why shop with us</a>
    </div>
    <p class="eco-hero__fineprint">
      Local pickup available · Inventory updates live from our showroom · <a href="/login">Staff sign-in</a>
    </p>
  </div>
  <div class="hero-media eco-hero__media">
    <?php if ($featured): ?>
      <?php
        $featCategory = categoryLabel($featured);
        $featGallery = galleryImages($featured);
        $featImage = $featured['hero_image'] ?: ($featGallery[0] ?? fallbackImage($featCategory));
      ?>
      <img src="<?= htmlspecialchars($featImage) ?>" alt="<?= htmlspecialchars($featured['model']) ?>" loading="eager" decoding="async" />
      <div class="hero-floating-badges">
        <span style="top:10%;left:58%;"><?= htmlspecialchars($featCategory) ?></span>
        <span style="bottom:14%;right:16%;"><?= (int) $featured['storage_capacity'] ?> GB · <?= htmlspecialchars($featured['color']) ?></span>
      </div>
    <?php else: ?>
      <img src="<?= htmlspecialchars(fallbackImage('MacBook')) ?>" alt="MacBook collection" loading="eager" decoding="async" />
    <?php endif; ?>
  </div>
</section>

<section class="eco-trust" aria-label="Store highlights">
  <div class="eco-trust__item">
    <strong>Certified inventory</strong>
    <span>Every Mac inspected and ready for daily use.</span>
  </div>
  <div class="eco-trust__item">
    <strong>Clear pricing</strong>
    <span>What you see is what you pay before tax.</span>
  </div>
  <div class="eco-trust__item">
    <strong>Book &amp; reserve</strong>
    <span>Hold your Mac online before you visit.</span>
  </div>
</section>

<?php if ($featured): ?>
  <?php
    $featCategory = categoryLabel($featured);
    $featGallery = galleryImages($featured);
    $featImage = $featured['hero_image'] ?: ($featGallery[0] ?? fallbackImage($featCategory));
    $featSlug = $featured['slug'] ?: strtolower($featured['sku']);
    $featPrice = $featured['online_price'] ?? $featured['list_price'];
  ?>
  <section class="eco-featured showroom-highlight" aria-labelledby="featured-heading">
    <div class="featured-card eco-featured-card">
      <p class="chip">Featured Mac</p>
      <h2 id="featured-heading" style="margin:0.4rem 0;"><?= htmlspecialchars($featured['model']) ?></h2>
      <p class="muted"><?= (int) $featured['storage_capacity'] ?> GB · <?= htmlspecialchars($featured['color']) ?></p>
      <?php
        $__desc = (string) ($featured['web_description'] ?: 'Premium Apple silicon performance in a precision-built chassis.');
        $__snippet = strlen($__desc) > 220 ? substr($__desc, 0, 217).'…' : $__desc;
      ?>
      <p class="eco-featured-copy"><?= htmlspecialchars($__snippet) ?></p>
      <div class="eco-price-block">
        <span class="eco-price">$<?= number_format((float) $featPrice, 2) ?></span>
        <?php if ($featured['online_price'] && (float) $featured['online_price'] < (float) $featured['list_price']): ?>
          <span class="eco-price-was">Was $<?= number_format((float) $featured['list_price'], 2) ?></span>
        <?php endif; ?>
      </div>
      <a class="product-cta eco-cta-solid" href="/store/product?slug=<?= urlencode($featSlug) ?>">View details</a>
    </div>
    <div class="panel eco-featured-gallery">
      <h3 style="margin-top:0;">Gallery</h3>
      <div class="gallery-thumbs" style="margin-top:0.8rem;">
        <?php foreach ($featGallery as $thumb): ?>
          <img src="<?= htmlspecialchars($thumb) ?>" alt="" loading="lazy" />
        <?php endforeach; ?>
      </div>
      <?php if (!$featGallery): ?>
        <img class="eco-featured-single" src="<?= htmlspecialchars($featImage) ?>" alt="<?= htmlspecialchars($featured['model']) ?>" loading="lazy" />
      <?php endif; ?>
      <p class="muted" style="margin-top:1rem;">In stock: <?= (int) $featured['quantity_on_hand'] ?> · SKU <?= htmlspecialchars($featured['sku']) ?></p>
    </div>
  </section>
<?php endif; ?>

<section id="why-us" class="eco-value" aria-labelledby="why-heading">
  <h2 id="why-heading">Built for Mac buyers</h2>
  <p class="eco-value__intro">Whether you’re upgrading a laptop or outfitting a desk, we focus on Apple hardware you can trust.</p>
  <div class="eco-value__grid">
    <article class="eco-value__card">
      <h3>Mac-first catalog</h3>
      <p>Inventory tuned for MacBook, desktop Macs, and essentials — not generic PCs.</p>
    </article>
    <article class="eco-value__card">
      <h3>Specs that matter</h3>
      <p>Storage, color, and pricing upfront so you can compare models quickly.</p>
    </article>
    <article class="eco-value__card">
      <h3>Reserve online</h3>
      <p>See something you love? Open the product page to book or hold yours.</p>
    </article>
  </div>
</section>

<section id="inventory-list" class="eco-catalog">
  <div class="eco-catalog__head">
    <h2 class="eco-catalog__title">Shop Mac computers</h2>
    <p class="eco-catalog__subtitle">Filter by category — stock counts update from our showroom.</p>
  </div>

  <div class="pill-filters eco-filters" role="tablist" aria-label="Filter by category">
    <?php foreach ($filterLabels as $label): ?>
      <?php $fid = 'f-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($label)); ?>
      <button type="button" class="pill eco-filter-btn<?= $label === 'All' ? ' is-active' : '' ?>" id="<?= htmlspecialchars($fid) ?>" data-filter="<?= htmlspecialchars($label === 'All' ? 'all' : $label) ?>" role="tab" aria-selected="<?= $label === 'All' ? 'true' : 'false' ?>"><?= htmlspecialchars($label) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="product-grid eco-product-grid">
    <?php if (empty($available)): ?>
      <div class="panel eco-empty" style="grid-column: 1 / -1;">
        <h3 style="margin-top:0;">New Macs arriving soon</h3>
        <p style="color:var(--muted);margin:0;">We’re refreshing inventory. Check back shortly or contact us for special orders.</p>
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
          $list = (float) $item['list_price'];
          $online = $item['online_price'] !== null && $item['online_price'] !== '' ? (float) $item['online_price'] : null;
        ?>
        <article class="product-card eco-card" data-product="<?= htmlspecialchars($item['sku']) ?>" data-category="<?= htmlspecialchars($category) ?>">
          <div class="badge-row">
            <span class="badge"><?= htmlspecialchars($category) ?></span>
            <?php if ($lowStock): ?>
              <span class="badge warning">Almost gone</span>
            <?php endif; ?>
          </div>
          <h3><?= htmlspecialchars($item['model']) ?></h3>
          <p class="sku">SKU <?= htmlspecialchars($item['sku']) ?></p>
          <div
            class="product-media image-magnifier image-magnifier--card"
            data-magnifier-auto
            data-magnifier-zoom="2.5"
            data-full="<?= htmlspecialchars($primary) ?>"
          >
            <div class="image-magnifier__frame">
              <img src="<?= htmlspecialchars($primary) ?>" alt="<?= htmlspecialchars($item['model']) ?>" loading="lazy" />
              <span class="image-magnifier__lens" hidden></span>
            </div>
            <div class="image-magnifier__panel" hidden role="img" aria-label="Magnified view">
              <span class="image-magnifier__panel-fill"></span>
            </div>
          </div>
          <?php if ($gallery): ?>
            <div class="gallery-thumbs" role="list">
              <?php foreach ($gallery as $thumb): ?>
                <button type="button" class="thumb" data-image="<?= htmlspecialchars($thumb) ?>">
                  <img src="<?= htmlspecialchars($thumb) ?>" alt="" loading="lazy" />
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <ul class="spec-list eco-spec">
            <li><?= (int) $item['storage_capacity'] ?> GB · <?= htmlspecialchars($item['color']) ?></li>
            <?php if ($online !== null && $online < $list): ?>
              <li><span class="eco-save">Save vs list</span></li>
            <?php endif; ?>
          </ul>
          <div class="price-row eco-price-row">
            <div>
              <p class="price">$<?= number_format((float) $price, 2) ?></p>
              <?php if ($online !== null && $online < $list): ?>
                <p class="muted eco-was">List $<?= number_format($list, 2) ?></p>
              <?php else: ?>
                <p class="muted">Tax calculated at checkout</p>
              <?php endif; ?>
            </div>
            <a class="product-cta eco-cta-solid" href="/store/product?slug=<?= urlencode($slug) ?>">View details</a>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<script>
  (function () {
    document.querySelectorAll('.eco-filter-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const filter = btn.dataset.filter || 'all';
        document.querySelectorAll('.eco-filter-btn').forEach((b) => {
          const on = b === btn;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        document.querySelectorAll('.eco-card').forEach((card) => {
          const cat = card.dataset.category || '';
          const show = filter === 'all' || cat === filter;
          card.style.display = show ? '' : 'none';
        });
      });
    });

    document.querySelectorAll('.product-card').forEach((card) => {
      const media = card.querySelector('.product-media');
      if (!media) return;
      const img = media.querySelector('img');
      const thumbs = card.querySelectorAll('.thumb');

      thumbs.forEach((btn) => {
        btn.addEventListener('click', () => {
          img.src = btn.dataset.image;
          media.dataset.full = btn.dataset.image;
        });
      });
    });
  })();
</script>
