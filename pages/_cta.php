<?php declare(strict_types=1); ?>

<section class="cta">
  <div class="shell cta__inner">
    <div class="cta__text">
      <h2 class="h2">Layihəniz var? Danışaq.</h2>
      <p>Sahəyə baxış və ilkin büdcə hesabatı ödənişsizdir. Bir iş günü ərzində cavab veririk.</p>
    </div>
    <div class="cta__actions">
      <a class="btn btn--solid" href="<?= url('elaqe') ?>">Müraciət göndərin</a>
      <a class="btn btn--ghost" href="tel:<?= e($site['contacts']['phone_href']) ?>"><?= e($site['contacts']['phone']) ?></a>
    </div>
  </div>
</section>
