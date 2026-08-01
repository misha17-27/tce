<?php declare(strict_types=1); ?>

<section class="page-head">
  <div class="shell">
    <nav class="crumbs" aria-label="Naviqasiya yolu">
      <a href="<?= url('') ?>">Ana səhifə</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page">Haqqımızda</span>
    </nav>
    <h1 class="page-title">Haqqımızda</h1>
    <p class="page-lead">
      Layihələndirmə, tikinti və mühəndislik sahəsində fəaliyyət göstərən Azərbaycan şirkəti.
    </p>
  </div>
</section>

<section class="section">
  <div class="shell split">
    <div class="split__aside">
      <p class="eyebrow">Şirkət</p>
      <h2 class="h2"><?= e($site['full_name']) ?></h2>
    </div>
    <div class="split__main">
      <p class="lead">
        Fəaliyyətimiz sadə prinsipə əsaslanır: sifarişçi bir podratçı ilə danışsın,
        qalan bütün əlaqələndirməni biz öz üzərimizə götürək.
      </p>
      <p>
        Layihə bölməsi, tədarük xidməti və tikinti briqadaları eyni komandanın hissəsidir.
        Bu, layihədə edilən dəyişikliyin dərhal smetada və qrafikdə əks olunmasına imkan verir —
        sahədə “layihəçi başqa şey çəkib, usta başqa şey qurub” vəziyyəti yaranmır.
      </p>
      <p>
        Hər obyekt üzrə icra sənədləri toplanır, gizli işlər aktlaşdırılır və təhvil zamanı
        sifarişçiyə tam sənəd paketi verilir. Bu paket sonrakı istismar, təmir və
        rəsmi rəsmiləşdirmə üçün lazım olur.
      </p>
    </div>
  </div>
</section>

<section class="section section--dark">
  <div class="shell">
    <header class="section-head section-head--light">
      <p class="eyebrow eyebrow--light">Rəqəmlərlə</p>
      <h2 class="h2">Bu günə qədər</h2>
    </header>
    <dl class="stat-grid">
      <?php foreach ($site['stats'] as $stat): ?>
        <div class="stat stat--lg">
          <dt class="stat__value"><?= (int)$stat['value'] ?><?= e($stat['suffix']) ?></dt>
          <dd class="stat__label"><?= e($stat['label']) ?></dd>
        </div>
      <?php endforeach; ?>
    </dl>
  </div>
</section>

<section class="section">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow">Dəyərlər</p>
      <h2 class="h2">İşdə əsas götürdüyümüz qaydalar</h2>
    </header>
    <ul class="feature-grid">
      <?php foreach ($site['advantages'] as $adv): ?>
        <li class="feature">
          <h3 class="feature__title"><?= e($adv['title']) ?></h3>
          <p class="feature__text"><?= e($adv['text']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="section section--muted">
  <div class="shell">
    <header class="section-head">
      <p class="eyebrow">Proses</p>
      <h2 class="h2">Layihə necə irəliləyir</h2>
    </header>
    <ol class="process">
      <?php foreach ($site['process'] as $i => $step): ?>
        <li class="process__item">
          <span class="process__num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <h3 class="process__title"><?= e($step['step']) ?></h3>
          <p class="process__text"><?= e($step['text']) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<?php require __DIR__ . '/_cta.php'; ?>
