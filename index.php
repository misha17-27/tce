<?php
declare(strict_types=1);

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// Во время разработки раскомментируйте, чтобы видеть ошибки:
// ini_set('display_errors', '1'); error_reporting(E_ALL);

require __DIR__ . '/includes/functions.php';
boot_session();
$site = load_site_config(require __DIR__ . '/config/site.php');

// ── Роутинг ─────────────────────────────────────────────────────────────────
$route = trim((string)($_GET['route'] ?? ''), '/');
$route = preg_replace('~[^a-z0-9\-/]~i', '', $route) ?? '';

$routes = [
    ''              => ['file' => 'home.php',     'title' => 'Ana səhifə'],
    'haqqimizda'    => ['file' => 'about.php',    'title' => 'Haqqımızda'],
    'xidmetlerimiz' => ['file' => 'services.php', 'title' => 'Xidmətlərimiz'],
    'layiheler'     => ['file' => 'projects.php', 'title' => 'Layihələr'],
    'elaqe'         => ['file' => 'contact.php',  'title' => 'Əlaqə'],
];

$page    = null;
$project = null;

if (isset($routes[$route])) {
    $page = $routes[$route];
} elseif (str_starts_with($route, 'layihe/')) {
    $project = find_project(substr($route, 7));
    if ($project) {
        $page = ['file' => 'project.php', 'title' => $project['title']];
    }
}

if ($page === null) {
    http_response_code(404);
    $page = ['file' => '404.php', 'title' => 'Səhifə tapılmadı'];
}

// ── Метаданные страницы ─────────────────────────────────────────────────────
$route_key = $route === '' ? 'home' : $route;
$default_description = (string)(json_decode('"Layih\u0259l\u0259ndirm\u0259, tikinti-qura\u015fd\u0131rma, avadanl\u0131q t\u0259chizat\u0131 v\u0259 sat\u0131\u015f sonras\u0131 xidm\u0259tl\u0259r. "') . $site['full_name'] . json_decode('", Bak\u0131."'));

$page_title = seo_value(
    $site,
    $route_key,
    'title',
    $project
        ? (string)$project['title']
        : ($route === '' ? $site['full_name'] . ' — ' . $site['tagline'] : $page['title'] . ' — ' . $site['name'])
);

$page_description = seo_value(
    $site,
    $route_key,
    'description',
    $project ? (string)($project['summary'] ?? '') : $default_description
);
$page_robots = seo_value($site, $route_key, 'robots', 'index,follow');
if (site_setting('search_visible', '1') === '0') {
    $page_robots = 'noindex,nofollow'; // сайт закрыт от поисковиков в админке
}
$page_canonical = seo_value($site, $route_key, 'canonical', absolute_url($route));
$page_og_image = absolute_url(
    $project['cover']
    ?? (site_setting('og_image') !== '' ? site_setting('og_image') : ($site['media']['hero'] ?? 'assets/img/hero.jpg'))
);
$page_og_type = $project ? 'article' : 'website';

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => absolute_url('') . '#organization',
            'name' => $site['full_name'],
            'url' => absolute_url(''),
            'logo' => absolute_url('assets/img/logo.svg'),
            'telephone' => $site['contacts']['phone'],
            'email' => $site['contacts']['email'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $site['contacts']['address'],
                'addressLocality' => 'Bakı',
                'addressCountry' => 'AZ',
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => absolute_url('') . '#website',
            'url' => absolute_url(''),
            'name' => $site['full_name'],
            'publisher' => ['@id' => absolute_url('') . '#organization'],
            'inLanguage' => $site['lang'],
        ],
        [
            '@type' => $project ? 'CreativeWork' : 'WebPage',
            '@id' => $page_canonical . '#webpage',
            'url' => $page_canonical,
            'name' => $page_title,
            'description' => $page_description,
            'isPartOf' => ['@id' => absolute_url('') . '#website'],
            'inLanguage' => $site['lang'],
        ],
    ],
];
// ── Рендер ──────────────────────────────────────────────────────────────────
require __DIR__ . '/includes/header.php';
require __DIR__ . '/pages/' . $page['file'];
require __DIR__ . '/includes/footer.php';
