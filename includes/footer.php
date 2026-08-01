<?php declare(strict_types=1); ?>
</main>

<footer class="site-footer">
  <div class="shell">

    <div class="footer-grid">

      <div class="footer-col footer-col--brand">
        <a class="logo logo--light" href="<?= url('') ?>">
          <span class="logo__mark" aria-hidden="true">
            <svg viewBox="0 0 32 32" width="32" height="32" fill="none">
              <path d="M2 30 L16 2 L30 30" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
              <path d="M9 30 L16 16 L23 30" stroke="currentColor" stroke-width="1.5" opacity=".55"/>
            </svg>
          </span>
          <span class="logo__text"><span class="logo__abbr"><?= e($site['name']) ?></span></span>
        </a>
        <p class="footer-lead"><?= e($site['tagline']) ?> ilə gələcəyinizi inşa edirik.</p>
        <ul class="social">
          <?php foreach ($site['contacts']['socials'] as $s): ?>
            <li><a href="<?= e($s['url']) ?>" rel="noopener"><?= e($s['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="footer-col">
        <h2 class="footer-title">Səhifələr</h2>
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
        <h2 class="footer-title">Əlaqə</h2>
        <ul class="footer-list footer-list--contact">
          <li><a href="tel:<?= e($site['contacts']['phone_href']) ?>"><?= e($site['contacts']['phone']) ?></a></li>
          <li><a href="mailto:<?= e($site['contacts']['email']) ?>"><?= e($site['contacts']['email']) ?></a></li>
          <li><span><?= e($site['contacts']['address']) ?></span></li>
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

<script src="<?= asset('assets/js/main.js') ?>" defer></script>
</body>
</html>
