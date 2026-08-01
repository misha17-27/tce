<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="<?= e($site['lang']) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_description) ?>">
<meta name="theme-color" content="#0E1A26">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($page_description) ?>">
<meta property="og:type" content="website">
<link rel="icon" href="<?= url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap&subset=latin,latin-ext" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body class="page page--<?= e($route === '' ? 'home' : $route) ?>">

<a class="skip-link" href="#main">Əsas məzmuna keç</a>

<header class="site-header" id="siteHeader">
  <div class="site-header__bar">
    <div class="shell site-header__inner">

      <a class="logo" href="<?= url('') ?>" aria-label="<?= e($site['full_name']) ?> — ana səhifə">
        <span class="logo__mark" aria-hidden="true">
          <svg viewBox="0 0 32 32" width="32" height="32" fill="none">
            <path d="M2 30 L16 2 L30 30" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
            <path d="M9 30 L16 16 L23 30" stroke="currentColor" stroke-width="1.5" opacity=".55"/>
          </svg>
        </span>
        <span class="logo__text">
          <span class="logo__abbr"><?= e($site['name']) ?></span>
          <span class="logo__full">Turan Construction<br>&amp; Engineering</span>
        </span>
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
          <a class="nav__phone" href="tel:<?= e($site['contacts']['phone_href']) ?>"><?= e($site['contacts']['phone']) ?></a>
          <a class="btn btn--solid btn--sm" href="<?= url('elaqe') ?>">Sifariş verin</a>
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
