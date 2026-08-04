<?php
declare(strict_types=1);

/** @var array $project */
$others = array_values(array_filter(
    $site['projects'],
    static fn(array $p): bool => $p['slug'] !== $project['slug']
));
$others = array_slice($others, 0, 3);
?>

<section class="page-head page-head--project">
  <div class="shell">
    <nav class="crumbs" aria-label="Naviqasiya yolu">
      <a href="<?= url('') ?>">Ana səhifə</a>
      <span aria-hidden="true">/</span>
      <a href="<?= url('layiheler') ?>">Layihələr</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page"><?= e($project['title']) ?></span>
    </nav>
    <p class="eyebrow"><?= e($project['category']) ?></p>
    <h1 class="page-title"><?= e($project['title']) ?></h1>
    <p class="page-lead"><?= e($project['summary']) ?></p>
  </div>
</section>

<section class="section section--tight">
  <div class="shell">
    <figure class="project-hero">
      <picture>
        <?php if ($webp = img_webp_src($project['cover'])): ?>
          <source srcset="<?= e($webp) ?>" type="image/webp">
        <?php endif; ?>
        <img src="<?= e(img_src($project['cover'])) ?>" alt="<?= e($project['title']) ?>" width="1600" height="1200">
      </picture>
    </figure>
  </div>
</section>

<section class="section">
  <div class="shell split">

    <div class="split__aside">
      <dl class="spec">
        <div class="spec__row"><dt>İl</dt><dd><?= e($project['year']) ?></dd></div>
        <div class="spec__row"><dt>Ünvan</dt><dd><?= e($project['location']) ?></dd></div>
        <div class="spec__row"><dt>Sahə</dt><dd><?= e($project['area']) ?></dd></div>
        <div class="spec__row"><dt>Sifarişçi</dt><dd><?= e($project['client']) ?></dd></div>
        <div class="spec__row"><dt>Kateqoriya</dt><dd><?= e($project['category']) ?></dd></div>
      </dl>
    </div>

    <div class="split__main">
      <p class="lead"><?= e($project['body']) ?></p>

      <h2 class="h3">İş həcmi</h2>
      <ul class="check-list">
        <?php foreach ($project['scope'] as $item): ?>
          <li><?= e($item) ?></li>
        <?php endforeach; ?>
      </ul>

      <?php if (count($project['gallery']) > 1): ?>
        <h2 class="h3">Qalereya</h2>
        <div class="gallery">
          <?php foreach ($project['gallery'] as $shot): ?>
            <figure class="gallery__item">
              <picture>
                <?php if ($webp = img_webp_src($shot)): ?>
                  <source srcset="<?= e($webp) ?>" type="image/webp">
                <?php endif; ?>
                <img src="<?= e(img_src($shot)) ?>" alt="<?= e($project['title']) ?>" loading="lazy" width="823" height="639">
              </picture>
            </figure>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php if ($others): ?>
<section class="section section--muted">
  <div class="shell">
    <header class="section-head section-head--row">
      <h2 class="h2">Digər layihələr</h2>
      <a class="btn btn--outline" href="<?= url('layiheler') ?>">Hamısına baxın</a>
    </header>
    <div class="project-grid">
      <?php foreach ($others as $other): ?>
        <article class="project-card">
          <a class="project-card__link" href="<?= url('layihe/' . $other['slug']) ?>">
            <picture class="project-card__media">
              <?php if ($webp = img_webp_src($other['cover'])): ?>
                <source srcset="<?= e($webp) ?>" type="image/webp">
              <?php endif; ?>
              <img src="<?= e(img_src($other['cover'])) ?>" alt="<?= e($other['title']) ?>" loading="lazy" width="823" height="639">
            </picture>
            <span class="project-card__body">
              <span class="project-card__meta"><?= e($other['category']) ?> · <?= e($other['year']) ?></span>
              <span class="project-card__title"><?= e($other['title']) ?></span>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/_cta.php'; ?>
