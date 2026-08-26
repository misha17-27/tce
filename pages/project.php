<?php declare(strict_types=1);

/** @var array $project */
$others = array_values(array_filter(
    public_projects(),
    static fn(array $p): bool => $p['slug'] !== $project['slug']
));
$others = array_slice($others, 0, 3);
$projectVideo = trim((string)($project['video'] ?? ''));

$cover = (string)($project['cover'] ?? '');
$body = clean_rich_text((string)($project['body'] ?? ''));
$galleryShots = array_values(array_filter(
    array_unique(array_map('strval', (array)($project['gallery'] ?? []))),
    static fn(string $shot): bool => $shot !== '' && $shot !== $cover
));
?>

<section class="page-head page-head--project">
  <div class="shell">
    <nav class="crumbs" aria-label="Naviqasiya yolu">
      <a href="<?= url('') ?>">Ana s&#601;hif&#601;</a>
      <span aria-hidden="true">/</span>
      <a href="<?= url('layiheler') ?>">Layih&#601;l&#601;r</a>
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
        <?php if ($webp = img_webp_src($cover)): ?>
          <source srcset="<?= e($webp) ?>" type="image/webp">
        <?php endif; ?>
        <img src="<?= e(img_src($cover)) ?>" alt="<?= e($project['title']) ?>" width="1600" height="1200">
      </picture>
    </figure>
  </div>
</section>

<section class="section project-detail-section">
  <div class="shell project-content">
    <?php if ($galleryShots): ?>
      <div class="project-gallery">
        <?php foreach ($galleryShots as $shot): ?>
          <figure class="project-gallery__item">
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

    <?php if ($projectVideo !== ''): ?>
      <div class="project-video" style="margin-bottom:2rem">
        <video src="<?= e(str_starts_with($projectVideo, 'http') ? $projectVideo : url($projectVideo)) ?>"
               controls playsinline preload="metadata"
               style="width:100%;max-height:70vh;background:#000"></video>
      </div>
    <?php endif; ?>

    <div class="project-summary project-rich">
      <?php if ($body !== ''): ?>
        <?= $body ?>
      <?php else: ?>
        <p class="lead"><?= e($project['summary']) ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($others): ?>
<section class="section section--muted">
  <div class="shell">
    <header class="section-head section-head--row">
      <h2 class="h2">Dig&#601;r layih&#601;l&#601;r</h2>
      <a class="btn btn--outline" href="<?= url('layiheler') ?>">Ham&#305;s&#305;na bax&#305;n</a>
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
              <span class="project-card__meta"><?= e($other['category']) ?> &middot; <?= e($other['year']) ?></span>
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
