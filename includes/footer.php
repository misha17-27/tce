<?php declare(strict_types=1); ?>
</main>

<footer class="site-footer">
  <div class="shell">

    <div class="footer-grid">

      <div class="footer-col footer-col--brand">
        <a class="logo logo--light" href="<?= url('') ?>" aria-label="<?= e($site['full_name']) ?> — ana səhifə">
          <img class="logo__image logo__image--light" src="<?= e(img_src('assets/img/logo-light.png')) ?>"
               alt="<?= e($site['full_name']) ?>" width="212" height="40" loading="lazy">
        </a>
        <p class="footer-lead"><?= e($site['tagline']) ?> ilə gələcəyinizi inşa edirik.</p>
        <?php // Соцсети иконками: список из админки + WhatsApp из контактов. ?>
        <?php if ($footerSocials = site_social_links($site)): ?>
          <ul class="social social--icons">
            <?php foreach ($footerSocials as $s): ?>
              <li>
                <?php if ($s['url'] !== ''): ?>
                  <a href="<?= e($s['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($s['label']) ?>" title="<?= e($s['label']) ?>"><?= $s['svg'] ?></a>
                <?php else: ?>
                  <span aria-label="<?= e($s['label']) ?>" title="<?= e($s['label']) ?>"><?= $s['svg'] ?></span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="footer-col">
        <h2 class="footer-title">Yararlı linklər</h2>
        <ul class="footer-list">
          <?php foreach ($site['nav'] as $slug => $label): ?>
            <li><a href="<?= url($slug) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="footer-col">
        <h2 class="footer-title">Xidmətlər</h2>
        <ul class="footer-list">
          <?php foreach ($site['services'] as $service): ?>
            <li><a href="<?= url('xidmetlerimiz') ?>#<?= e($service['slug']) ?>"><?= e($service['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="footer-col">
        <h2 class="footer-title">Bizimlə əlaqə</h2>
        <ul class="footer-list footer-list--contact">
          <li><a href="tel:<?= e($site['contacts']['phone_href']) ?>">Telefon: <?= e($site['contacts']['phone']) ?></a></li>
          <li><a href="mailto:<?= e($site['contacts']['email']) ?>">Mail: <?= e($site['contacts']['email']) ?></a></li>
          <li><span>Ünvan: <?= e($site['contacts']['address']) ?></span></li>
          <li><span><?= e($site['contacts']['hours']) ?></span></li>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= e($site['full_name']) ?>. Bütün hüquqlar qorunur.</p>
      <a class="to-top" href="#main">Yuxarı qalx</a>
    </div>

  </div>
</footer>

<?php $waDigits = preg_replace('/[^0-9]/', '', (string)($site['contacts']['whatsapp'] ?? '')); ?>
<?php if ($waDigits !== ''): ?>
<a class="wa-float" href="https://wa.me/<?= e($waDigits) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
  <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill="currentColor" d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.5c1.2.5 2.5.8 3.8.8 6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.8c-1.2 0-2.4-.3-3.5-.8l-.6-.3-4.9.9 1-4.7-.4-.6c-1-1.6-1.5-3.4-1.5-5.3 0-5.5 4.4-9.9 9.9-9.9s9.9 4.4 9.9 9.9-4.4 9.8-9.9 9.8zm5.4-7.4c-.3-.2-1.8-.9-2-1-.3-.1-.5-.2-.7.2-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.2-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.5-.6c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.6-.1-.2-.7-1.7-1-2.3-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.1 1.3 3.3c.2.2 2.3 3.6 5.6 5 .8.3 1.4.5 1.9.7.8.2 1.5.2 2.1.1.6-.1 1.8-.7 2.1-1.5.3-.7.3-1.3.2-1.5-.1-.1-.3-.2-.6-.4z"/></svg>
</a>
<?php endif; ?>

<script src="<?= asset('assets/js/main.js') ?>" defer></script>
</body>
</html>
