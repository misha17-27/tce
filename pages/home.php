<?php declare(strict_types=1); ?>

<section class="hero">
  <div class="hero__grid" aria-hidden="true"></div>
  <div class="shell hero__inner">
    <p class="eyebrow eyebrow--light">Bakı · <?= date('Y') ?> · Tikinti və mühəndislik</p>
    <h1 class="hero__title">
      Keyfiyyətli tikinti və mühəndislik həlləri ilə
      <em>gələcəyinizi inşa edirik</em>
    </h1>
    <p class="hero__lead">
      Layihələndirmədən açar təslimə qədər bütün mərhələləri bir podratçı çərçivəsində
      icra edirik — qrafik, büdcə və keyfiyyət nəzarəti bir əldə qalır.
    </p>
    <div class="hero__actions">
      <a class="btn btn--solid" href="<?= url('layiheler') ?>">Layihələrə baxın</a>
      <a class="btn btn--ghost" href="<?= url('elaqe') ?>">Təklif alın</a>
    </div>

    <dl class="hero__stats">
      <?php foreach ($site['stats'] as $stat): ?>
        <div class="stat">
          <dt class="stat__value" data-count="<?= (int)$stat['value'] ?>"><?= (int)$stat['value'] ?><?= e($stat['suffix']) ?></dt>
          <dd class="stat__label"><?= e($stat['label']) ?></dd>
        </div>
      <?php endforeach; ?>
    </dl>
  </div>
</section>

<section class="section section--intro">
  <div class="shell split">
    <div class="split__aside">
      <p class="eyebrow">Haqqımızda</p>
      <h2 class="h2">İnşa etdiyimiz həllər güclü gələcəyin zəminini qoyur</h2>
    </div>
    <div class="split__main">
      <p class="lead">
        <?= e($site['full_name']) ?> — Azərbaycanda layihələndirmə, avadanlıq təchizatı,
        tikinti-quraşdırma və satış sonrası xidmətlər üzrə fəaliyyət göstərən şirkətdir.
      </p>
      <p>
        Sənaye obyektlərindən fərdi yaşayış evlərinə qədər müxtəlif miqyaslı işlər aparırıq.
        Hər layihə üçün ayrıca komanda formalaşdırılır, sifarişçi ilə birbaşa əlaqə saxlayan
        layihə rəhbəri təyin olunur. Bu, qərarların günlərlə deyil, saatlarla qəbul edilməsinə imkan verir.
      </p>
      <a class="link-arrow" href="<?= url('haqqimizda') ?>">Şirkət haqqında ətraflı</a>
    </div>
  </div>
</section>

<section class="section section--dark">
  <div class="shell">
    <header class="section-head section-head--light">
      <p class="eyebrow eyebrow--light">Xidmətlər</p>
      <h2 class="h2">Dörd istiqamət, bir məsuliyyət</h2>
    </header>

    <ul class="service-list">
      <?php foreach ($site['services'] as $service): ?>
        <li class="service-row">
          <span class="service-row__code"><?= e($service['code']) ?></span>
          <h3 class="service-row__title"><?= e($service['title']) ?></h3>
          <p class="service-row__text"><?= e($service['lead']) ?></p>
          <a class="service-row__link" href="<?= url('xidmetlerimiz') ?>#<?= e($service['slug']) ?>"
             aria-label="<?= e($service['title']) ?> — ətraflı">Ətraflı</a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="section">
  <div class="shell">
    <header class="section-head section-head--row">
      <div>
        <p class="eyebrow">Layihələr</p>
        <h2 class="h2">Son işlərimiz</h2>
      </div>
      <a class="btn btn--outline" href="<?= url('layiheler') ?>">Hamısına baxın</a>
    </header>

    <div class="project-grid">
      <?php foreach (array_slice($site['projects'], 0, 4) as $i => $project): ?>
        <article class="project-card<?= $i === 0 ? ' project-card--wide' : '' ?>">
          <a class="project-card__link" href="<?= url('layihe/' . $project['slug']) ?>">
            <span class="project-card__media">
              <img src="<?= e(img_src($project['cover'])) ?>" alt="<?= e($project['title']) ?>" loading="lazy">
            </span>
            <span class="project-card__body">
              <span class="project-card__meta"><?= e($project['category']) ?> · <?= e($project['year']) ?></span>
              <span class="project-card__title"><?= e($project['title']) ?></span>
              <span class="project-card__summary"><?= e($project['summary']) ?></span>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--muted">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow">Niyə biz</p>
      <h2 class="h2">Sifarişçinin bizdə qiymətləndirdiyi altı şey</h2>
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
      <p class="eyebrow">İş qaydası</p>
      <h2 class="h2">İlk görüşdən təhvilə qədər</h2>
    </header>

    <ol class="process">
      <?php foreach ($site['process'] as $i => $step): ?>
        <li class="process__item">
          <span class="process__num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <h3 class="process__title"><?= e($step['step']) ?></h3>
          <p class="process__text"><?= e($step['text']) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="section section--muted">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow">Tərəfdaşlar</p>
      <h2 class="h2">Birlikdə işlədiyimiz şirkətlər</h2>
    </header>
    <ul class="partner-grid">
      <?php foreach ($site['partners'] as $partner): ?>
        <li class="partner">
          <img src="<?= e(img_src($partner['logo'])) ?>" alt="<?= e($partner['name']) ?>" loading="lazy">
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php require __DIR__ . '/_cta.php'; ?>
