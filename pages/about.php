<?php declare(strict_types=1); ?>

<section class="about-hero">
  <div class="shell about-hero__inner">
    <div>
      <p class="about-kicker"><?= e(site_text('about.kicker', 'Şirkət Haqqında')) ?></p>
      <h1 class="about-title"><?= e($site['full_name']) ?></h1>
    </div>
    <p class="about-hero__text"><?= e(site_text('about.intro')) ?></p>
  </div>
</section>

<section class="section about-story">
  <div class="shell about-story__grid">
    <figure class="about-card">
      <img src="<?= e(img_src(site_text('about.image', 'assets/img/about/years.png'))) ?>" alt="<?= e($site['full_name']) ?> layihəsi" width="760" height="460" loading="lazy">
      <figcaption><?= e(site_text('about.figcaption')) ?></figcaption>
    </figure>

    <div class="about-copy">
      <?php foreach (['about.p1', 'about.p2', 'about.p3', 'about.p4'] as $key): ?>
        <?php if (site_text($key) !== ''): ?>
          <p><?= e(site_text($key)) ?></p>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$certificates = array_values(array_filter([
    site_text('about.cert1'),
    site_text('about.cert2'),
    site_text('about.cert3'),
], static fn(string $path): bool => trim($path) !== ''));
?>
<?php if ($certificates): ?>
<section class="section about-certificates">
  <div class="shell">
    <h2 class="about-certificates__title"><?= e(site_text('about.certs_title', 'Sertifikatlar')) ?></h2>
    <div class="certificate-grid">
      <?php foreach ($certificates as $i => $certificate): ?>
        <figure class="certificate-card">
          <img src="<?= e(img_src($certificate)) ?>" alt="Sertifikat <?= $i + 1 ?>" width="520" height="720" loading="lazy">
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
