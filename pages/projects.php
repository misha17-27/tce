<?php
declare(strict_types=1);

$categories = [];
foreach ($site['projects'] as $p) {
    $categories[$p['category']] = true;
}
$categories = array_keys($categories);
?>

<section class="page-head">
  <div class="shell">
    <nav class="crumbs" aria-label="Naviqasiya yolu">
      <a href="<?= url('') ?>">Ana səhifə</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page">Layihələr</span>
    </nav>
    <h1 class="page-title">Layihələr</h1>
    <p class="page-lead">
      Sənaye, infrastruktur və yaşayış obyektləri üzrə tamamlanmış işlərimiz.
    </p>
  </div>
</section>

<section class="section">
  <div class="shell">

    <div class="filters" role="group" aria-label="Kateqoriya üzrə süzgəc">
      <button class="filter is-active" type="button" data-filter="*">Hamısı</button>
      <?php foreach ($categories as $cat): ?>
        <button class="filter" type="button" data-filter="<?= e($cat) ?>"><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="project-grid project-grid--all" id="projectGrid">
      <?php foreach ($site['projects'] as $project): ?>
        <article class="project-card" data-category="<?= e($project['category']) ?>">
          <a class="project-card__link" href="<?= url('layihe/' . $project['slug']) ?>">
            <span class="project-card__media">
              <img src="<?= e(img_src($project['cover'])) ?>" alt="<?= e($project['title']) ?>" loading="lazy">
            </span>
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
