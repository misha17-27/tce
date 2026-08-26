<?php
declare(strict_types=1);

$heroImage = (string)($site['media']['hero'] ?? 'assets/img/hero.jpg');
?>

<section class="hero">
  <picture class="hero__media" aria-hidden="true">
    <?php if ($heroWebp = img_webp_src($heroImage)): ?>
      <source srcset="<?= e($heroWebp) ?>" type="image/webp">
    <?php endif; ?>
    <img src="<?= e(img_src($heroImage)) ?>" alt="" width="1600" height="998" fetchpriority="high">
  </picture>
  <div class="shell hero__inner">
    <div class="hero__content">
      <h1 class="hero__title"><?= e(site_text('home.hero_title', (string)json_decode('"Keyfiyy\u0259tli tikinti v\u0259 m\u00fch\u0259ndislik h\u0259ll\u0259ri il\u0259 g\u0259l\u0259c\u0259yinizi in\u015fa edirik."'))) ?></h1>
      <div class="hero__actions">
        <a class="btn btn--solid" href="<?= url('haqqimizda') ?>">Daha Ətraflı</a>
      </div>
    </div>
  </div>
</section>

<section class="section section--intro section--brand-band">
  <div class="shell intro-band">
    <div class="intro-band__label"><?= e(site_text('home.intro_label', (string)json_decode('"\u0130n\u015fa Etdiyimiz H\u0259ll\u0259r, G\u00fccl\u00fc G\u0259l\u0259c\u0259yin Z\u0259minini Qoyur."'))) ?></div>
    <p class="lead"><?= e(site_text('home.intro_text', (string)json_decode('"Az\u0259rbaycan\u0131n tikinti v\u0259 m\u00fch\u0259ndislik sah\u0259sind\u0259 lider \u015firk\u0259tl\u0259rind\u0259n biri olaraq, biz layih\u0259l\u0259ndirm\u0259, avadanl\u0131q t\u0259chizat\u0131, tikinti v\u0259 sat\u0131\u015f sonras\u0131 xidm\u0259tl\u0259r \u00fczr\u0259 geni\u015f t\u0259cr\u00fcb\u0259y\u0259 sahibik. M\u00fc\u015ft\u0259ril\u0259rimiz\u0259 kompleks v\u0259 innovativ h\u0259ll\u0259r t\u0259qdim ed\u0259r\u0259k, h\u0259r zaman y\u00fcks\u0259k keyfiyy\u0259t v\u0259 pe\u015f\u0259karl\u0131q t\u0259min edirik. H\u0259d\u0259fimiz, h\u0259r bir layih\u0259ni m\u00fck\u0259mm\u0259llikl\u0259 h\u0259yata ke\u00e7irm\u0259kdir."'))) ?></p>
  </div>
</section>

<section class="section">
  <div class="shell">
    <header class="section-head section-head--row">
      <div>
        <p class="eyebrow"><?= e(site_text('home.projects_eyebrow', 'Layihələr')) ?></p>
        <h2 class="h2"><?= e(site_text('home.projects_title', 'Uğurlu layihələrimiz')) ?></h2>
      </div>
      <a class="btn btn--outline" href="<?= url('layiheler') ?>">Daha çox</a>
    </header>

    <div class="project-grid project-grid--featured">
      <?php foreach (array_slice(public_projects(), 0, 4) as $project): ?>
        <article class="project-card">
          <a class="project-card__link" href="<?= url('layihe/' . $project['slug']) ?>">
            <picture class="project-card__media">
              <?php if ($webp = img_webp_src($project['cover'])): ?>
                <source srcset="<?= e($webp) ?>" type="image/webp">
              <?php endif; ?>
              <img src="<?= e(img_src($project['cover'])) ?>" alt="<?= e($project['title']) ?>" loading="lazy" width="823" height="639">
            </picture>
            <span class="project-card__body">
              <span class="project-card__title"><?= e($project['title']) ?></span>
              <span class="project-card__meta">Property Details</span>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--about">
  <div class="shell split">
    <div class="split__aside">
      <p class="eyebrow"><?= e(site_text('home.about_eyebrow', 'Haqqımızda')) ?></p>
      <h2 class="h2"><?= e($site['full_name']) ?></h2>
    </div>
    <div class="split__main">
      <p class="lead"><?= e(site_text('home.about_lead')) ?></p>
      <p><?= e(site_text('home.about_text')) ?></p>
      <a class="link-arrow" href="<?= url('haqqimizda') ?>">Daha ətraflı</a>
    </div>
  </div>
</section>

<section class="section section--muted">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow"><?= e(site_text('home.adv_eyebrow', 'Üstünlüklər')) ?></p>
      <h2 class="h2"><?= e(site_text('home.adv_title', 'Bizimlə işləmək üçün əsas səbəblər')) ?></h2>
    </header>

    <ul class="feature-grid">
      <?php foreach ($site['advantages'] as $adv): ?>
        <li class="feature">
          <h3 class="feature__title"><?= e($adv['title']) ?></h3>
          <p class="feature__text"><?= e($adv['text']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="section">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow"><?= e(site_text('home.services_eyebrow', 'Xidmətlər')) ?></p>
      <h2 class="h2"><?= e(site_text('home.services_title', 'Dörd istiqamət, bir məsuliyyət')) ?></h2>
    </header>

    <ul class="service-card-grid">
      <?php foreach ($site['services'] as $service): ?>
        <li class="service-card">
          <span class="service-card__code"><?= e($service['code']) ?></span>
          <h3 class="service-card__title"><?= e($service['title']) ?></h3>
          <p class="service-card__text"><?= e($service['lead']) ?></p>
          <a class="link-arrow" href="<?= url('xidmetlerimiz') ?>#<?= e($service['slug']) ?>">Ətraflı</a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php $homePartners = public_partners(); ?>
<?php if ($homePartners): ?>
<section class="section section--muted">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow"><?= e(site_text('home.partners_eyebrow', 'Partnyorlar')) ?></p>
      <h2 class="h2"><?= e(site_text('home.partners_title', 'Birlikdə işlədiyimiz şirkətlər')) ?></h2>
    </header>
    <ul class="partner-grid">
      <?php foreach ($homePartners as $partner): ?>
        <li class="partner">
          <?php $partnerUrl = trim((string)($partner['url'] ?? '')); ?>
          <?php if ($partnerUrl !== '' && $partnerUrl !== '#'): ?><a href="<?= e($partnerUrl) ?>" target="_blank" rel="noopener"><?php endif; ?>
          <picture>
            <?php if ($webp = img_webp_src($partner['logo'])): ?>
              <source srcset="<?= e($webp) ?>" type="image/webp">
            <?php endif; ?>
            <img src="<?= e(img_src($partner['logo'])) ?>" alt="<?= e($partner['name']) ?>" loading="lazy" width="180" height="72">
          </picture>
          <?php if ($partnerUrl !== '' && $partnerUrl !== '#'): ?></a><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/_cta.php'; ?>
