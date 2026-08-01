<?php
declare(strict_types=1);

/** Экранирование вывода. Используйте ВЕЗДЕ, где печатаете данные. */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Длина строки в символах. Работает и без расширения mbstring. */
function str_len(string $value): int
{
    return function_exists('mb_strlen')
        ? mb_strlen($value, 'UTF-8')
        : (int)strlen(preg_replace('/[\x80-\xBF]/', '', $value));
}

/** Абсолютный URL внутри сайта: url('layiheler') → /layiheler */
function url(string $path = ''): string
{
    global $site;
    $base = rtrim((string)($site['base_url'] ?? ''), '/');
    $path = ltrim($path, '/');
    return ($base . '/' . $path) ?: '/';
}

/** URL статики с версией файла — сбрасывает кеш браузера при изменении. */
function asset(string $path): string
{
    $file = __DIR__ . '/../' . ltrim($path, '/');
    $ver  = is_file($file) ? filemtime($file) : time();
    return url($path) . '?v=' . $ver;
}

/** Активен ли пункт меню. */
function is_active(string $slug): bool
{
    global $route;
    if ($slug === '') {
        return $route === '';
    }
    return $route === $slug || str_starts_with($route, $slug . '/');
}

/** Найти проект по slug. */
function find_project(string $slug): ?array
{
    global $site;
    foreach ($site['projects'] as $project) {
        if ($project['slug'] === $slug) {
            return $project;
        }
    }
    return null;
}

/** Картинка с запасным вариантом, если файла ещё нет. */
function img_src(string $path): string
{
    $file = __DIR__ . '/../' . ltrim($path, '/');
    return is_file($file) ? url($path) : url('assets/img/placeholder.svg');
}

/** CSRF-токен формы. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    return !empty($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

/**
 * Обработка формы обратной связи.
 * Возвращает ['ok' => bool|null, 'errors' => [], 'old' => []]
 */
function handle_contact_form(array $site): array
{
    $result = ['ok' => null, 'errors' => [], 'old' => []];

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return $result;
    }

    $old = [
        'name'    => trim((string)($_POST['name'] ?? '')),
        'phone'   => trim((string)($_POST['phone'] ?? '')),
        'email'   => trim((string)($_POST['email'] ?? '')),
        'message' => trim((string)($_POST['message'] ?? '')),
    ];
    $result['old'] = $old;

    // Ловушка для ботов: реальный пользователь это поле не видит.
    if (!empty($_POST['website'])) {
        $result['ok'] = true;
        return $result;
    }

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $result['errors']['form'] = 'Sessiya vaxtı bitib. Səhifəni yeniləyib yenidən cəhd edin.';
        $result['ok'] = false;
        return $result;
    }

    if (str_len($old['name']) < 2) {
        $result['errors']['name'] = 'Adınızı yazın.';
    }
    if ($old['phone'] === '' && $old['email'] === '') {
        $result['errors']['phone'] = 'Telefon və ya e-poçt ünvanından birini qeyd edin.';
    }
    if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $result['errors']['email'] = 'E-poçt ünvanı düzgün deyil.';
    }
    if (str_len($old['message']) < 10) {
        $result['errors']['message'] = 'Müraciətinizi bir neçə cümlə ilə izah edin.';
    }

    if ($result['errors']) {
        $result['ok'] = false;
        return $result;
    }

    $body = "Ad: {$old['name']}\n"
          . "Telefon: {$old['phone']}\n"
          . "E-poçt: {$old['email']}\n"
          . "Tarix: " . date('d.m.Y H:i') . "\n"
          . "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '-') . "\n\n"
          . $old['message'] . "\n";

    $sent = false;
    if (!empty($site['mail']['enabled'])) {
        $headers = "From: saytdan <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n"
                 . "Content-Type: text/plain; charset=UTF-8\r\n";
        if ($old['email'] !== '') {
            $headers .= "Reply-To: " . $old['email'] . "\r\n";
        }
        $sent = @mail($site['mail']['to'], $site['mail']['subject'], $body, $headers);
    }

    // Дублируем в лог в любом случае — заявка не потеряется.
    $dir = __DIR__ . '/../storage';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    @file_put_contents($dir . '/messages.log', $body . str_repeat('-', 60) . "\n", FILE_APPEND | LOCK_EX);

    $result['ok']  = true;
    $result['old'] = [];
    unset($sent);

    return $result;
}
