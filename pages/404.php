<?php declare(strict_types=1); ?>

<section class="section section--error">
  <div class="shell error-block">
    <p class="error-code">404</p>
    <h1 class="h2">Belə səhifə yoxdur</h1>
    <p class="lead">
      Ünvan səhv yazılıb və ya səhifə köçürülüb. Aşağıdakı bölmələrdən birinə keçin.
    </p>
    <ul class="error-links">
      <?php foreach ($site['nav'] as $slug => $label): ?>
        <li><a href="<?= url($slug) ?>"><?= e($label) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
