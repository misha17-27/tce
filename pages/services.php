<?php declare(strict_types=1); ?>

<section class="page-head">
  <div class="shell">
    <nav class="crumbs" aria-label="Naviqasiya yolu">
      <a href="<?= url('') ?>">Ana səhifə</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page">Xidmətlərimiz</span>
    </nav>
    <h1 class="page-title">Xidmətlərimiz</h1>
    <p class="page-lead">
      Dörd istiqamət ayrı-ayrılıqda da sifariş oluna bilər, açar təslimi paket kimi də.
    </p>
  </div>
</section>

<section class="section">
  <div class="shell">
    <ul class="service-index">
      <?php foreach ($site['services'] as $service): ?>
        <li><a href="#<?= e($service['slug']) ?>"><span><?= e($service['code']) ?></span><?= e($service['title']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php foreach ($site['services'] as $i => $service): ?>
  <section class="section service-block<?= $i % 2 ? ' section--muted' : '' ?>" id="<?= e($service['slug']) ?>">
    <div class="shell split">
      <div class="split__aside">
        <p class="eyebrow"><?= e($service['code']) ?></p>
        <h2 class="h2"><?= e($service['title']) ?></h2>
        <p class="service-block__lead"><?= e($service['lead']) ?></p>
      </div>
      <div class="split__main">
        <p><?= e($service['text']) ?></p>
        <ul class="check-list">
          <?php foreach ($service['items'] as $item): ?>
            <li><?= e($item) ?></li>
          <?php endforeach; ?>
        </ul>
        <a class="link-arrow" href="<?= url('elaqe') ?>">Bu xidmət üzrə təklif alın</a>
      </div>
    </div>
  </section>
<?php endforeach; ?>

<?php require __DIR__ . '/_cta.php'; ?>
