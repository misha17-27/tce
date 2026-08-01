<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// Во время разработки раскомментируйте, чтобы видеть ошибки:
// ini_set('display_errors', '1'); error_reporting(E_ALL);

$site = require __DIR__ . '/config/site.php';
require __DIR__ . '/includes/functions.php';

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
$page_title = $route === ''
    ? $site['full_name'] . ' — ' . $site['tagline']
    : $page['title'] . ' — ' . $site['name'];

$page_description = $project['summary']
    ?? 'Layihələndirmə, tikinti-quraşdırma, avadanlıq təchizatı və satış sonrası xidmətlər. '
     . $site['full_name'] . ', Bakı.';

// ── Рендер ──────────────────────────────────────────────────────────────────
require __DIR__ . '/includes/header.php';
require __DIR__ . '/pages/' . $page['file'];
require __DIR__ . '/includes/footer.php';
