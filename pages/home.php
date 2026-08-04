<?php declare(strict_types=1); ?>

<section class="hero">
  <picture class="hero__media" aria-hidden="true">
    <?php if ($heroWebp = img_webp_src('assets/img/hero.jpg')): ?>
      <source srcset="<?= e($heroWebp) ?>" type="image/webp">
    <?php endif; ?>
    <img src="<?= e(img_src('assets/img/hero.jpg')) ?>" alt="" width="1600" height="998" fetchpriority="high">
  </picture>
  <div class="shell hero__inner">
    <div class="hero__content">
      <h1 class="hero__title">Keyfiyyətli tikinti və mühəndislik həlləri ilə gələcəyinizi inşa edirik.</h1>
      <div class="hero__actions">
        <a class="btn btn--solid" href="<?= url('haqqimizda') ?>">Daha Ətraflı</a>
      </div>
    </div>
  </div>
</section>

<section class="section section--intro section--brand-band">
  <div class="shell intro-band">
    <div class="intro-band__label">İnşa Etdiyimiz Həllər, Güclü Gələcəyin Zəminini Qoyur.</div>
    <p class="lead">
      Azərbaycanın tikinti və mühəndislik sahəsində lider şirkətlərindən biri olaraq, biz layihələndirmə,
      avadanlıq təchizatı, tikinti və satış sonrası xidmətlər üzrə geniş təcrübəyə sahibik.
      Müştərilərimizə kompleks və innovativ həllər təqdim edərək, hər zaman yüksək keyfiyyət və
      peşəkarlıq təmin edirik. Hədəfimiz, hər bir layihəni mükəmməlliklə həyata keçirməkdir.
    </p>
  </div>
</section>

<section class="section">
  <div class="shell">
    <header class="section-head section-head--row">
      <div>
        <p class="eyebrow">Layihələr</p>
        <h2 class="h2">Uğurlu layihələrimiz</h2>
      </div>
      <a class="btn btn--outline" href="<?= url('layiheler') ?>">Daha çox</a>
    </header>

    <div class="project-grid project-grid--featured">
      <?php foreach (array_slice($site['projects'], 0, 4) as $project): ?>
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
      <p class="eyebrow">Haqqımızda</p>
      <h2 class="h2"><?= e($site['full_name']) ?></h2>
    </div>
    <div class="split__main">
      <p class="lead">
        Azərbaycanın tikinti və mühəndislik sahəsində aparıcı şirkətlərindən biridir. Biz layihələndirmə,
        avadanlıq təchizatı, tikinti və satış sonrası xidmətlər təklif edirik.
      </p>
      <p>
        Müştərilərimizə kompleks həllər təqdim etməklə, yüksək keyfiyyət və peşəkarlıq vəd edirik.
        Hər bir layihəyə fərdi yanaşır, qrafik, büdcə və keyfiyyət nəzarətini vahid komandada saxlayırıq.
      </p>
      <a class="link-arrow" href="<?= url('haqqimizda') ?>">Daha ətraflı</a>
    </div>
  </div>
</section>

<section class="section section--muted">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow">Üstünlüklər</p>
      <h2 class="h2">Bizimlə işləmək üçün əsas səbəblər</h2>
    </header>

    <ul class="feature-grid">
      <?php foreach (array_slice($site['advantages'], 0, 5) as $adv): ?>
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
      <p class="eyebrow">Xidmətlər</p>
      <h2 class="h2">Dörd istiqamət, bir məsuliyyət</h2>
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

<section class="section section--muted">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow">Partnyorlar</p>
      <h2 class="h2">Birlikdə işlədiyimiz şirkətlər</h2>
    </header>
    <ul class="partner-grid">
      <?php foreach ($site['partners'] as $partner): ?>
        <li class="partner">
          <picture>
            <?php if ($webp = img_webp_src($partner['logo'])): ?>
              <source srcset="<?= e($webp) ?>" type="image/webp">
            <?php endif; ?>
            <img src="<?= e(img_src($partner['logo'])) ?>" alt="<?= e($partner['name']) ?>" loading="lazy" width="180" height="72">
          </picture>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php require __DIR__ . '/_cta.php'; ?>
