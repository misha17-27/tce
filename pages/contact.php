<?php
declare(strict_types=1);

$form = handle_contact_form($site);
$old  = $form['old'];
?>

<section class="page-head">
  <div class="shell">
    <nav class="crumbs" aria-label="Naviqasiya yolu">
      <a href="<?= url('') ?>">Ana səhifə</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page">Əlaqə</span>
    </nav>
    <h1 class="page-title">Əlaqə</h1>
    <p class="page-lead">
      Zəng edin, yazın və ya formanı doldurun — bir iş günü ərzində cavab veririk.
    </p>
  </div>
</section>

<section class="section">
  <div class="shell contact-layout">

    <aside class="contact-info">
      <h2 class="h3">Rekvizitlər</h2>
      <ul class="contact-list">
        <li>
          <span class="contact-list__label">Telefon</span>
          <a href="tel:<?= e($site['contacts']['phone_href']) ?>"><?= e($site['contacts']['phone']) ?></a>
        </li>
        <li>
          <span class="contact-list__label">E-poçt</span>
          <a href="mailto:<?= e($site['contacts']['email']) ?>"><?= e($site['contacts']['email']) ?></a>
        </li>
        <li>
          <span class="contact-list__label">Ünvan</span>
          <span><?= e($site['contacts']['address']) ?></span>
        </li>
        <li>
          <span class="contact-list__label">İş saatları</span>
          <span><?= e($site['contacts']['hours']) ?></span>
        </li>
      </ul>
    </aside>

    <div class="contact-form-wrap">
      <h2 class="h3">Müraciət formu</h2>

      <?php if ($form['ok'] === true): ?>
        <div class="notice notice--ok" role="status">
          Müraciətiniz qeydə alındı. Tezliklə sizinlə əlaqə saxlayacağıq.
        </div>
      <?php elseif ($form['ok'] === false && isset($form['errors']['form'])): ?>
        <div class="notice notice--error" role="alert"><?= e($form['errors']['form']) ?></div>
      <?php endif; ?>

      <form class="form" method="post" action="<?= url('elaqe') ?>#form" id="form" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="form__trap" aria-hidden="true">
          <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        <div class="field">
          <label class="field__label" for="name">Ad, Soyad <span aria-hidden="true">*</span></label>
          <input class="field__input<?= isset($form['errors']['name']) ? ' has-error' : '' ?>"
                 type="text" id="name" name="name" required
                 value="<?= e($old['name'] ?? '') ?>" autocomplete="name">
          <?php if (isset($form['errors']['name'])): ?>
            <p class="field__error"><?= e($form['errors']['name']) ?></p>
          <?php endif; ?>
        </div>

        <div class="field-row">
          <div class="field">
            <label class="field__label" for="phone">Telefon</label>
            <input class="field__input<?= isset($form['errors']['phone']) ? ' has-error' : '' ?>"
                   type="tel" id="phone" name="phone"
                   value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel"
                   placeholder="+994 __ ___ __ __">
            <?php if (isset($form['errors']['phone'])): ?>
              <p class="field__error"><?= e($form['errors']['phone']) ?></p>
            <?php endif; ?>
          </div>

          <div class="field">
            <label class="field__label" for="email">E-poçt</label>
            <input class="field__input<?= isset($form['errors']['email']) ? ' has-error' : '' ?>"
                   type="email" id="email" name="email"
                   value="<?= e($old['email'] ?? '') ?>" autocomplete="email">
            <?php if (isset($form['errors']['email'])): ?>
              <p class="field__error"><?= e($form['errors']['email']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <div class="field">
          <label class="field__label" for="message">Müraciətiniz <span aria-hidden="true">*</span></label>
          <textarea class="field__input field__input--area<?= isset($form['errors']['message']) ? ' has-error' : '' ?>"
                    id="message" name="message" rows="6" required
                    placeholder="Obyektin növü, sahəsi və gözlənilən başlama tarixi barədə qısa məlumat yazın."><?= e($old['message'] ?? '') ?></textarea>
          <?php if (isset($form['errors']['message'])): ?>
            <p class="field__error"><?= e($form['errors']['message']) ?></p>
          <?php endif; ?>
        </div>

        <button class="btn btn--solid" type="submit">Göndər</button>
        <p class="form__note">Formu göndərməklə məlumatlarınızın müraciətə baxılması üçün emalına razılıq verirsiniz.</p>
      </form>
    </div>

  </div>
</section>

<section class="section section--tight">
  <div class="shell">
    <div class="map">
      <iframe src="<?= e($site['contacts']['map_embed']) ?>"
              title="Xəritədə ofisimiz" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </div>
</section>
