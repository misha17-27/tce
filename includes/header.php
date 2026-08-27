<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="<?= e($site['lang']) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_description) ?>">
<meta name="robots" content="<?= e($page_robots ?? 'index,follow') ?>">
<link rel="canonical" href="<?= e($page_canonical ?? absolute_url($route ?? '')) ?>">
<meta name="theme-color" content="#011640">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($page_description) ?>">
<meta property="og:type" content="<?= e($page_og_type ?? 'website') ?>">
<meta property="og:url" content="<?= e($page_canonical ?? absolute_url($route ?? '')) ?>">
<meta property="og:image" content="<?= e($page_og_image ?? absolute_url('assets/img/hero.jpg')) ?>">
<meta property="og:locale" content="az_AZ">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($page_title) ?>">
<meta name="twitter:description" content="<?= e($page_description) ?>">
<meta name="twitter:image" content="<?= e($page_og_image ?? absolute_url('assets/img/hero.jpg')) ?>">
<script type="application/ld+json"><?= json_encode($schema ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<link rel="icon" href="<?= asset('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap&subset=latin,latin-ext" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body class="page page--<?= e($route === '' ? 'home' : $route) ?>">

<a class="skip-link" href="#main">Əsas məzmuna keç</a>

<header class="site-header" id="siteHeader">
  <div class="site-header__bar">
    <div class="shell site-header__inner">

      <a class="logo" href="<?= url('') ?>" aria-label="<?= e($site['full_name']) ?> — ana səhifə">
        <img class="logo__image" src="<?= e(img_src('assets/img/logo.svg')) ?>"
             alt="<?= e($site['full_name']) ?>" width="186" height="40">
      </a>

      <nav class="nav" id="primaryNav" aria-label="Əsas naviqasiya">
        <ul class="nav__list">
          <?php foreach ($site['nav'] as $slug => $label): ?>
            <li class="nav__item">
              <a class="nav__link<?= is_active($slug) ? ' is-active' : '' ?>"
                 href="<?= url($slug) ?>"
                 <?= is_active($slug) ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="nav__cta">
          <a class="nav__phone" href="tel:<?= e($site['contacts']['phone_href']) ?>">
            <span class="nav__phone-icon" aria-hidden="true">☎</span>
            <?= e($site['contacts']['phone']) ?>
          </a>
        </div>

        <div class="nav__footer">
          <a class="nav__phone-btn" href="tel:<?= e($site['contacts']['phone_href']) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.15 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1C10.61 21 3 13.39 3 4c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
            <?= e($site['contacts']['phone']) ?>
          </a>
          <?php if ($navSocials = site_social_links($site)): ?>
            <ul class="nav__social">
              <?php foreach ($navSocials as $s): ?>
                <li>
                  <?php if ($s['url'] !== ''): ?>
                    <a class="nav__social-link<?= $s['key'] === 'whatsapp' ? ' is-wa' : '' ?>" href="<?= e($s['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($s['label']) ?>"><?= $s['svg'] ?></a>
                  <?php else: ?>
                    <span class="nav__social-link" aria-label="<?= e($s['label']) ?>"><?= $s['svg'] ?></span>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <p class="nav__tagline"><?= e($site['tagline']) ?></p>
        </div>
      </nav>

      <button class="burger" id="burger" type="button"
              aria-controls="primaryNav" aria-expanded="false" aria-label="Menyunu aç">
        <span></span><span></span><span></span>
      </button>

    </div>
  </div>
</header>

<div class="nav-backdrop" id="navBackdrop" hidden></div>

<main id="main">
