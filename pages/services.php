<?php declare(strict_types=1);

$serviceGroups = (array)($site['service_groups'] ?? []);
?>

<section class="services-hero">
  <div class="shell services-hero__inner">
    <div>
      <p class="services-kicker"><?= e(site_text('services.kicker', 'Şirkət Haqqında')) ?></p>
      <h1 class="services-title">Xidmətlərimiz</h1>
    </div>
    <p class="services-hero__text"><?= e(site_text('services.hero_text')) ?></p>
  </div>
</section>

<section class="section services-detail">
  <div class="shell">
    <div class="services-detail__head">
      <p class="eyebrow"><?= e(site_text('services.head_eyebrow', 'Fəaliyyət istiqamətləri')) ?></p>
      <h2 class="h2"><?= e(site_text('services.head_title', 'Tikinti və mühəndislik xidmətləri')) ?></h2>
    </div>
    <div class="services-detail__grid">
      <?php foreach ($serviceGroups as $i => $group): ?>
        <article class="services-detail__card" id="<?= e($group['slug'] ?? '') ?>">
          <span class="services-detail__number"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <h3><?= e($group['title'] ?? '') ?></h3>
          <ul>
            <?php foreach ((array)($group['items'] ?? []) as $item): ?>
              <li><?= e((string)$item) ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/_cta.php'; ?>
