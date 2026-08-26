<?php declare(strict_types=1); ?>

<section class="cta">
  <div class="shell cta__inner">
    <div class="cta__text">
      <h2 class="h2"><?= e(site_text('cta.title', 'Layihəniz var? Danışaq.')) ?></h2>
      <p><?= e(site_text('cta.text')) ?></p>
    </div>
    <div class="cta__actions">
      <a class="btn btn--solid" href="<?= url('elaqe') ?>">Müraciət göndərin</a>
      <a class="btn btn--ghost" href="tel:<?= e($site['contacts']['phone_href']) ?>"><?= e($site['contacts']['phone']) ?></a>
    </div>
  </div>
</section>
