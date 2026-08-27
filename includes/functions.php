<?php
declare(strict_types=1);

/** Экранирование вывода. Используйте ВЕЗДЕ, где печатаете данные. */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Единая точка запуска сессии: httponly + samesite + secure на https. */
function boot_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (($_SERVER['HTTPS'] ?? '') !== '' && ($_SERVER['HTTPS'] ?? '') !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    @ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => $secure,
    ]);
    session_start();
}

/** Настройки сайта (админка → SEO/SMTP/Безопасность). Хранятся в site.json. */
function site_setting(string $key, string $default = ''): string
{
    global $site;
    $value = trim((string)($site['settings'][$key] ?? ''));
    return $value !== '' ? $value : $default;
}

function clean_rich_text(?string $value): string
{
    $html = (string)$value;
    $html = preg_replace('~<script\b[^>]*>.*?</script>~is', '', $html) ?? '';
    $html = preg_replace('~<style\b[^>]*>.*?</style>~is', '', $html) ?? '';
    $html = strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><h4><blockquote>');
    $html = preg_replace('~\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $html) ?? '';
    $html = preg_replace('~\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $html) ?? '';
    $html = preg_replace_callback('~<a\b([^>]*)>~i', static function (array $match): string {
        $attrs = $match[1];
        if (!preg_match('~href\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', $attrs, $hrefMatch)) {
            return '<a>';
        }
        $href = trim((string)($hrefMatch[2] ?? $hrefMatch[3] ?? $hrefMatch[4] ?? ''));
        if ($href === '' || preg_match('~^\s*javascript:~i', $href)) {
            return '<a>';
        }
        return '<a href="' . e($href) . '" target="_blank" rel="noopener">';
    }, $html) ?? '';

    return trim($html);
}

function storage_path(string $file = ''): string
{
    $dir = __DIR__ . '/../storage';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    return $file === '' ? $dir : $dir . '/' . ltrim($file, '/');
}

function is_list_array(array $value): bool
{
    if ($value === []) {
        return true;
    }

    return array_keys($value) === range(0, count($value) - 1);
}

function array_replace_deep(array $base, array $overrides): array
{
    foreach ($overrides as $key => $value) {
        if (is_array($value) && isset($base[$key]) && is_array($base[$key]) && !is_list_array($value)) {
            $base[$key] = array_replace_deep($base[$key], $value);
            continue;
        }

        $base[$key] = $value;
    }

    return $base;
}

function load_site_config(array $defaults): array
{
    $json = storage_path('site.json');
    if (!is_file($json)) {
        return $defaults;
    }

    $data = json_decode((string)file_get_contents($json), true);
    return is_array($data) ? array_replace_deep($defaults, $data) : $defaults;
}

function save_site_config(array $site): bool
{
    $payload = json_encode($site, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($payload === false) {
        return false;
    }

    return file_put_contents(storage_path('site.json'), $payload . "\n", LOCK_EX) !== false;
}

function site_text(string $key, string $fallback = ''): string
{
    global $site;
    return (string)($site['content'][$key] ?? $fallback);
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

function absolute_url(string $path = ''): string
{
    $host = (string)($_SERVER['HTTP_HOST'] ?? 'tce.az');
    return 'https://' . $host . url($path);
}

function seo_value(array $site, string $route, string $field, string $fallback = ''): string
{
    $routeKey = $route === '' ? 'home' : $route;
    $value = $site['seo']['pages'][$routeKey][$field]
        ?? $site['seo'][$field]
        ?? $fallback;

    $value = trim((string)$value);
    return $value !== '' ? $value : $fallback;
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

/** Проект опубликован и виден на сайте? */
function project_is_public(array $project): bool
{
    if ((string)($project['status'] ?? 'published') !== 'published') {
        return false;
    }
    return (int)($project['visible'] ?? 1) === 1;
}

/** Опубликованные проекты в порядке «sort», затем по позиции. */
function public_projects(): array
{
    global $site;
    $projects = array_values(array_filter((array)$site['projects'], 'project_is_public'));
    usort($projects, static function (array $a, array $b): int {
        return (int)($a['sort'] ?? 0) <=> (int)($b['sort'] ?? 0);
    });
    return $projects;
}

/** Видимые партнёры в порядке «sort». */
function public_partners(): array
{
    global $site;
    $partners = array_values(array_filter(
        (array)$site['partners'],
        static fn(array $p): bool => (int)($p['visible'] ?? 1) === 1 && trim((string)($p['logo'] ?? '')) !== ''
    ));
    usort($partners, static function (array $a, array $b): int {
        return (int)($a['sort'] ?? 0) <=> (int)($b['sort'] ?? 0);
    });
    return $partners;
}

/** Найти опубликованный проект по slug (для публичной части сайта). */
function find_project(string $slug): ?array
{
    global $site;
    foreach ($site['projects'] as $project) {
        if ((string)($project['slug'] ?? '') === $slug && project_is_public($project)) {
            return $project;
        }
    }
    return null;
}

/**
 * Картинка с запасным вариантом, если файла ещё нет.
 * URL версионируется временем изменения файла (?v=...) — это сбрасывает
 * кеш браузера и CDN при замене картинки.
 */
function img_src(string $path): string
{
    $file = __DIR__ . '/../' . ltrim($path, '/');
    if (!is_file($file)) {
        return url('assets/img/placeholder.svg');
    }

    return url($path) . '?v=' . filemtime($file);
}

/** WebP version next to the original image, if it exists. */
function img_webp_src(string $path): ?string
{
    $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
    if ($webp === null || $webp === $path) {
        return null;
    }

    $file = __DIR__ . '/../' . ltrim($webp, '/');
    return is_file($file) ? url($webp) . '?v=' . filemtime($file) : null;
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

/* ── Cloudflare Turnstile (капча). Ключи: админка → Безопасность. ─────────── */

function turnstile_site_key(): string
{
    return site_setting('turnstile_site');
}

function turnstile_secret_key(): string
{
    return site_setting('turnstile_secret');
}

/** Виджет капчи. Ничего не выводит, если ключи не настроены. */
function turnstile_widget(): void
{
    $key = turnstile_site_key();
    if ($key === '') {
        return;
    }
    echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
    echo '<div class="cf-turnstile" data-sitekey="' . e($key) . '" style="margin:10px 0"></div>';
}

/** Проверка токена. true, если капча не настроена (форма работает как раньше). */
function turnstile_verify(string $token): bool
{
    $secret = turnstile_secret_key();
    if ($secret === '') {
        return true;
    }
    if ($token === '') {
        return false;
    }
    $ctx = stream_context_create(['http' => [
        'method'  => 'POST',
        'timeout' => 10,
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]),
    ]]);
    $response = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $ctx);
    if ($response === false) {
        return false;
    }
    $data = json_decode($response, true);
    return !empty($data['success']);
}

/* ── Минимальный SMTP-клиент (STARTTLS/SSL). Настройки: админка → SMTP. ───── */

function smtp_send(string $to, string $subject, string $body, string &$err = ''): bool
{
    $host = site_setting('smtp_host');
    if ($host === '') {
        $err = 'SMTP-сервер не заполнен';
        return false;
    }
    $port   = (int)(site_setting('smtp_port') ?: 587);
    $user   = site_setting('smtp_user');
    $pass   = site_setting('smtp_pass');
    $secure = site_setting('smtp_secure') ?: 'tls';
    $from   = site_setting('smtp_from') ?: $user;
    $fname  = site_setting('smtp_from_name') ?: 'Tce.az';
    $target = $secure === 'ssl' ? "ssl://$host" : $host;
    $fp = @fsockopen($target, $port, $eno, $estr, 15);
    if (!$fp) {
        $err = "Подключение не удалось: $estr ($eno)";
        return false;
    }
    stream_set_timeout($fp, 15);
    $get = function () use ($fp) {
        $d = '';
        while (($l = fgets($fp, 515)) !== false) {
            $d .= $l;
            if (strlen($l) < 4 || $l[3] === ' ') {
                break;
            }
        }
        return $d;
    };
    $cmd = function ($c) use ($fp, $get) {
        fwrite($fp, $c . "\r\n");
        return $get();
    };
    $ok = static fn(string $r, string $code): bool => str_starts_with(trim($r), $code);
    $get();
    $cmd('EHLO tce.az');
    if ($secure === 'tls') {
        if (!$ok($cmd('STARTTLS'), '220') || !stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $err = 'STARTTLS не удалось';
            fclose($fp);
            return false;
        }
        $cmd('EHLO tce.az');
    }
    if ($user !== '') {
        $cmd('AUTH LOGIN');
        $cmd(base64_encode($user));
        if (!$ok($cmd(base64_encode($pass)), '235')) {
            $err = 'Авторизация SMTP не прошла (проверь логин/пароль)';
            fclose($fp);
            return false;
        }
    }
    $cmd("MAIL FROM:<$from>");
    $cmd("RCPT TO:<$to>");
    if (!$ok($cmd('DATA'), '354')) {
        $err = 'Сервер не принял DATA';
        fclose($fp);
        return false;
    }
    $hdr = 'From: =?UTF-8?B?' . base64_encode($fname) . "?= <$from>\r\nTo: <$to>\r\n"
         . 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\nMIME-Version: 1.0\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $r = $cmd($hdr . str_replace("\r\n.", "\r\n..", $body) . "\r\n.");
    $cmd('QUIT');
    fclose($fp);
    if (!$ok($r, '250')) {
        $err = 'Письмо не отправлено: ' . trim($r);
        return false;
    }
    return true;
}

/** Почта для уведомлений о заявках. */
function notify_email(): string
{
    global $site;
    return site_setting('notify_email', (string)($site['mail']['to'] ?? ''));
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

    if (!turnstile_verify((string)($_POST['cf-turnstile-response'] ?? ''))) {
        $result['errors']['form'] = 'Robot olmadığınızı təsdiqləyin.';
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

    // Уведомление на почту: SMTP, если настроен в админке, иначе mail().
    $to = notify_email();
    if ($to !== '' && filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $subject = (string)($site['mail']['subject'] ?? 'Saytdan yeni müraciət');
        if (site_setting('smtp_host') !== '') {
            $smtpErr = '';
            @smtp_send($to, $subject, $body, $smtpErr);
        } elseif (!empty($site['mail']['enabled'])) {
            $headers = "From: saytdan <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n"
                     . "Content-Type: text/plain; charset=UTF-8\r\n";
            if ($old['email'] !== '' && !preg_match('/[\r\n]/', $old['email'])) {
                $headers .= "Reply-To: " . $old['email'] . "\r\n";
            }
            @mail($to, $subject, $body, $headers);
        }
    }

    // Дублируем в лог в любом случае — заявка не потеряется.
    @file_put_contents(storage_path('messages.log'), $body . str_repeat('-', 60) . "\n", FILE_APPEND | LOCK_EX);

    $messagesFile = storage_path('messages.json');
    $messages = [];
    if (is_file($messagesFile)) {
        $loaded = json_decode((string)file_get_contents($messagesFile), true);
        if (is_array($loaded)) {
            $messages = $loaded;
        }
    }
    array_unshift($messages, [
        'id' => bin2hex(random_bytes(8)),
        'created_at' => date('c'),
        'name' => $old['name'],
        'phone' => $old['phone'],
        'email' => $old['email'],
        'message' => $old['message'],
        'ip' => (string)($_SERVER['REMOTE_ADDR'] ?? '-'),
        'status' => 'new',
    ]);
    $messages = array_slice($messages, 0, 500);
    $messagesPayload = json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($messagesPayload !== false) {
        @file_put_contents($messagesFile, $messagesPayload . "\n", LOCK_EX);
    }

    $result['ok']  = true;
    $result['old'] = [];

    return $result;
}
