<?php declare(strict_types=1); ?>

<section class="page-head">
  <div class="shell">
    <nav class="crumbs" aria-label="Naviqasiya yolu">
      <a href="<?= url('') ?>">Ana səhifə</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page">Layihələr</span>
    </nav>
    <h1 class="page-title">Layihələr</h1>
    <p class="page-lead"><?= e(site_text('projects.lead')) ?></p>
  </div>
</section>

<section class="section">
  <div class="shell">
<div class="project-grid project-grid--all" id="projectGrid">
      <?php foreach (public_projects() as $project): ?>
        <article class="project-card" data-category="<?= e($project['category']) ?>">
          <a class="project-card__link" href="<?= url('layihe/' . $project['slug']) ?>">
            <picture class="project-card__media">
              <?php if ($webp = img_webp_src($project['cover'])): ?>
                <source srcset="<?= e($webp) ?>" type="image/webp">
              <?php endif; ?>
              <img src="<?= e(img_src($project['cover'])) ?>" alt="<?= e($project['title']) ?>" loading="lazy" width="823" height="639">
            </picture>
            <span class="project-card__body">
              <span class="project-card__meta"><?= e($project['category']) ?> · <?= e($project['year']) ?> · <?= e($project['location']) ?></span>
              <span class="project-card__title"><?= e($project['title']) ?></span>
              <span class="project-card__summary"><?= e($project['summary']) ?></span>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>

    <p class="empty-state" id="projectEmpty" hidden>Bu kateqoriyada hələ layihə yoxdur.</p>

  </div>
</section>

<?php require __DIR__ . '/_cta.php'; ?>
