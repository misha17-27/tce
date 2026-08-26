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
        <?php
        // Соцсети выводятся иконками. Подписи из админки → SVG; WhatsApp берётся
        // из «Контакты → WhatsApp» и добавляется в конец автоматически.
        $socialSvg = [
            'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.3" cy="6.7" r="1.2" fill="currentColor" stroke="none"/></svg>',
            'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.5 21.5v-8h2.7l.4-3.1h-3.1V8.4c0-.9.3-1.5 1.6-1.5h1.7v-2.8c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.4-4 4.1v2.3H7.7v3.1h2.7v8h3.1z"/></svg>',
            'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.94 8.75H3.56V20.5h3.38V8.75zM5.25 3.5a1.97 1.97 0 1 0 0 3.94 1.97 1.97 0 0 0 0-3.94zM20.5 13.72c0-3.28-1.75-4.81-4.09-4.81-1.88 0-2.72 1.03-3.19 1.76V8.75H9.84V20.5h3.38v-6.28c0-1.66.31-3.27 2.37-3.27 2.03 0 2.06 1.9 2.06 3.38v6.17h3.38l-.53-6.78z"/></svg>',
            'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M23 7.5s-.2-1.6-.9-2.3c-.9-.9-1.9-.9-2.4-1C16.6 4 12 4 12 4s-4.6 0-7.7.2c-.4.1-1.5.1-2.4 1-.7.7-.9 2.3-.9 2.3S1 9.4 1 11.3v1.7c0 1.9.2 3.8.2 3.8s.2 1.6.9 2.3c.9.9 2 .9 2.5 1 1.8.2 7.4.2 7.4.2s4.6 0 7.7-.2c.4-.1 1.5-.1 2.4-1 .7-.7.9-2.3.9-2.3s.2-1.9.2-3.8v-1.7c0-1.9-.2-3.8-.2-3.8zM9.8 15.3V8.7l6.4 3.3-6.4 3.3z"/></svg>',
            'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.2 2h3.3l-7.3 8.4L22.8 22h-6.7l-5.3-6.9L4.7 22H1.4l7.8-9L1.2 2H8l4.8 6.3L18.2 2zm-1.2 18h1.8L6.9 3.9H5L17 20z"/></svg>',
            'whatsapp'  => '<svg viewBox="0 0 32 32" aria-hidden="true"><path fill="currentColor" d="M16 3C9.4 3 4 8.4 4 15c0 2.1.6 4.2 1.6 6L4 29l8.2-1.5c1.2.5 2.5.8 3.8.8 6.6 0 12-5.4 12-12S22.6 3 16 3zm0 21.8c-1.2 0-2.4-.3-3.5-.8l-.6-.3-4.9.9 1-4.7-.4-.6c-1-1.6-1.5-3.4-1.5-5.3 0-5.5 4.4-9.9 9.9-9.9s9.9 4.4 9.9 9.9-4.4 9.8-9.9 9.8zm5.4-7.4c-.3-.2-1.8-.9-2-1-.3-.1-.5-.2-.7.2-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.2-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.5-.6c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.6-.1-.2-.7-1.7-1-2.3-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.1 1.3 3.3c.2.2 2.3 3.6 5.6 5 .8.3 1.4.5 1.9.7.8.2 1.5.2 2.1.1.6-.1 1.8-.7 2.1-1.5.3-.7.3-1.3.2-1.5-.1-.1-.3-.2-.6-.4z"/></svg>',
        ];
        $footerSocials = [];
        foreach ((array)$site['contacts']['socials'] as $s) {
            $label = trim((string)($s['label'] ?? ''));
            $key = mb_strtolower($label);
            if (str_starts_with($key, 'x')) { $key = 'x'; }
            if ($label !== '' && isset($socialSvg[$key])) {
                $footerSocials[] = ['label' => $label, 'url' => trim((string)($s['url'] ?? '')), 'svg' => $socialSvg[$key]];
            }
        }
        $footerWa = preg_replace('/[^0-9]/', '', (string)($site['contacts']['whatsapp'] ?? '')) ?? '';
        if ($footerWa !== '') {
            $footerSocials[] = ['label' => 'WhatsApp', 'url' => 'https://wa.me/' . $footerWa, 'svg' => $socialSvg['whatsapp']];
        }
        ?>
        <?php if ($footerSocials): ?>
          <ul class="social social--icons">
            <?php foreach ($footerSocials as $s): ?>
              <li>
                <?php if ($s['url'] !== '' && $s['url'] !== '#'): ?>
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
