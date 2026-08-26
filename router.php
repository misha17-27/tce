<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) { return false; }
if ($path !== '/' && is_dir($file) && is_file(rtrim($file, '/\\') . '/index.php')) {
    require rtrim($file, '/\\') . '/index.php';
    return true;
}
$_GET['route'] = ltrim($path, '/');
require __DIR__ . '/index.php';
