<?php
/* Общие помощники админки: хранилище, авторизация, CSRF, layout.
   Порт админ-панели ceng.az на файловое хранилище tce (storage/*.json). */
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$GLOBALS['site'] = load_site_config(require __DIR__ . '/../config/site.php');

/** Сохранить конфиг сайта (глобальный $site) в storage/site.json. */
function save_site(): void
{
    save_site_config($GLOBALS['site']);
}

/* ── key-value поверх site.json: texts → content, contacts, settings ─────── */

function kv_get(string $group, string $key, string $default = ''): string
{
    global $site;
    $map = ['texts' => 'content', 'contacts' => 'contacts', 'settings' => 'settings'];
    $bucket = $map[$group] ?? $group;
    $value = $site[$bucket][$key] ?? null;
    return is_scalar($value) && (string)$value !== '' ? (string)$value : $default;
}

function kv_set(string $group, string $key, string $val): void
{
    global $site;
    $map = ['texts' => 'content', 'contacts' => 'contacts', 'settings' => 'settings'];
    $bucket = $map[$group] ?? $group;
    if (!isset($site[$bucket]) || !is_array($site[$bucket])) {
        $site[$bucket] = [];
    }
    $site[$bucket][$key] = $val;
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

/* URL-slug из названия: азербайджанская + русская транслитерация, затем a-z0-9- */
function slugify(string $s): string
{
    static $map = [
        'ə'=>'e','Ə'=>'E','ı'=>'i','İ'=>'I','ö'=>'o','Ö'=>'O','ü'=>'u','Ü'=>'U',
        'ç'=>'c','Ç'=>'C','ş'=>'s','Ş'=>'S','ğ'=>'g','Ğ'=>'G',
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z',
        'и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
        'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'Yo','Ж'=>'Zh','З'=>'Z',
        'И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R',
        'С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'Ts','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sch',
        'Ъ'=>'','Ы'=>'Y','Ь'=>'','Э'=>'E','Ю'=>'Yu','Я'=>'Ya',
    ];
    $s = strtr($s, $map);
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-');
}

/* ── Пользователи админки: storage/admin-users.json ───────────────────────── */

function admin_users_file(): string
{
    return storage_path('admin-users.json');
}

function admin_users(): array
{
    if (isset($GLOBALS['__admin_users_cache'])) {
        return $GLOBALS['__admin_users_cache'];
    }
    $file = admin_users_file();
    if (is_file($file)) {
        $data = json_decode((string)file_get_contents($file), true);
        if (is_array($data) && !empty($data['users'])) {
            return $GLOBALS['__admin_users_cache'] = $data;
        }
    }

    // Миграция со старого admin-auth.json (один пользователь).
    $legacy = storage_path('admin-auth.json');
    if (is_file($legacy)) {
        $auth = json_decode((string)file_get_contents($legacy), true);
        if (is_array($auth) && isset($auth['username'], $auth['password_hash'])) {
            $data = ['next_id' => 2, 'users' => [[
                'id' => 1, 'name' => 'Admin', 'email' => (string)$auth['username'],
                'pass_hash' => (string)$auth['password_hash'], 'role' => 'admin',
                'active' => 1, 'last_login' => null,
            ]]];
            save_admin_users($data);
            return $data;
        }
    }

    // Первый запуск: создаём admin@tce.az со случайным паролем.
    $password = bin2hex(random_bytes(6));
    $data = ['next_id' => 2, 'users' => [[
        'id' => 1, 'name' => 'Admin', 'email' => 'admin@tce.az',
        'pass_hash' => password_hash($password, PASSWORD_DEFAULT), 'role' => 'admin',
        'active' => 1, 'last_login' => null,
    ]]];
    save_admin_users($data);
    @file_put_contents(storage_path('admin-password.txt'), "Login: admin@tce.az\nPassword: {$password}\n", LOCK_EX);
    return $data;
}

function save_admin_users(array $data): void
{
    $payload = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($payload !== false) {
        file_put_contents(admin_users_file(), $payload . "\n", LOCK_EX);
    }
    $GLOBALS['__admin_users_cache'] = $data;
}

function current_admin(): ?array
{
    boot_session();
    return $_SESSION['admin'] ?? null;
}

function require_login(): array
{
    $a = current_admin();
    if (!$a) {
        redirect('index.php?section=login');
    }
    return $a;
}

function attempt_login(string $email, string $pass): bool
{
    $email = trim($email);
    $data = admin_users();
    foreach ($data['users'] as $i => $u) {
        if (strcasecmp((string)$u['email'], $email) !== 0) {
            continue;
        }
        if ((int)($u['active'] ?? 1) === 1 && password_verify($pass, (string)$u['pass_hash'])) {
            boot_session();
            session_regenerate_id(true);
            $_SESSION['admin'] = [
                'id' => (int)$u['id'], 'email' => (string)$u['email'],
                'name' => (string)($u['name'] ?? ''), 'role' => (string)($u['role'] ?? 'admin'),
            ];
            $data['users'][$i]['last_login'] = date('Y-m-d H:i');
            save_admin_users($data);
            return true;
        }
        return false;
    }
    return false;
}

/* Защита от перебора: 10 неудач с IP → блокировка на 15 минут. */
function throttle_file(): string
{
    return storage_path('login-throttle.json');
}

function throttle_data(): array
{
    $file = throttle_file();
    if (!is_file($file)) {
        return [];
    }
    $data = json_decode((string)file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function throttle_save(array $data): void
{
    // подчищаем записи старше суток
    $now = time();
    foreach ($data as $ip => $row) {
        if (($row['ts'] ?? 0) < $now - 86400) {
            unset($data[$ip]);
        }
    }
    @file_put_contents(throttle_file(), json_encode($data), LOCK_EX);
}

function login_locked(string $ip): bool
{
    $row = throttle_data()[$ip] ?? null;
    return $row && ($row['locked_until'] ?? 0) > time();
}

function login_fail(string $ip): void
{
    $data = throttle_data();
    $row = $data[$ip] ?? ['fails' => 0, 'locked_until' => 0];
    $row['fails'] = (int)($row['fails'] ?? 0) + 1;
    $row['ts'] = time();
    if ($row['fails'] >= 10) {
        $row['locked_until'] = time() + 15 * 60;
        $row['fails'] = 0;
    }
    $data[$ip] = $row;
    throttle_save($data);
}

function login_ok(string $ip): void
{
    $data = throttle_data();
    unset($data[$ip]);
    throttle_save($data);
}

function is_admin(): bool
{
    $a = current_admin();
    return ($a['role'] ?? 'admin') === 'admin';
}

function logout(): void
{
    boot_session();
    $_SESSION = [];
    session_destroy();
}

/* CSRF */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    boot_session();
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['_csrf'] ?? ''))) {
            http_response_code(400);
            exit('Bad CSRF token. Reload the page.');
        }
    }
}

/* Flash */
function flash(string $msg, string $type = 'ok'): void
{
    boot_session();
    $_SESSION['flash'][] = [$type, $msg];
}

function flash_render(): string
{
    boot_session();
    $out = '';
    foreach ($_SESSION['flash'] ?? [] as [$t, $m]) {
        $bg = $t === 'err' ? '#b91c1c' : '#011640';
        $out .= '<div style="background:' . $bg . ';color:#fff;padding:11px 16px;border-radius:8px;margin-bottom:14px;font-weight:600">' . e($m) . '</div>';
    }
    $_SESSION['flash'] = [];
    return $out;
}

/* ── Загрузка файлов (assets/img/uploads) ─────────────────────────────────── */

function admin_move(string $tmp, string $name): ?string
{
    $dir = dirname(__DIR__) . '/assets/img/uploads';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($name)) ?? '';
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $imgExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $vidExt = ['mp4', 'webm', 'ogg', 'mov'];
    if (!in_array($ext, array_merge($imgExt, ['svg'], $vidExt), true)) {
        return null;
    }
    if (!is_uploaded_file($tmp)) {
        return null;
    }
    // растровые картинки должны действительно быть картинками (отсекает переименованные скрипты)
    if (in_array($ext, $imgExt, true) && @getimagesize($tmp) === false) {
        return null;
    }
    // SVG: не пускаем файлы со скриптами и обработчиками событий
    if ($ext === 'svg') {
        $head = (string)@file_get_contents($tmp, false, null, 0, 200 * 1024);
        if (preg_match('~<script|on[a-z]+\s*=|javascript:~i', $head)) {
            return null;
        }
    }
    $name = time() . '_' . $name;
    return move_uploaded_file($tmp, "$dir/$name") ? 'assets/img/uploads/' . $name : null;
}

function admin_upload(string $field): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }
    return admin_move((string)$_FILES[$field]['tmp_name'], (string)$_FILES[$field]['name']);
}

function admin_upload_multi(string $field): array
{
    $out = [];
    if (empty($_FILES[$field]['name'][0])) {
        return $out;
    }
    foreach ((array)$_FILES[$field]['name'] as $i => $name) {
        if ($name === '') {
            continue;
        }
        if ($p = admin_move((string)$_FILES[$field]['tmp_name'][$i], (string)$name)) {
            $out[] = $p;
        }
    }
    return $out;
}

/* ── Структура панели ─────────────────────────────────────────────────────── */

const SECTIONS = [
    'overview'    => ['Обзор', '▤'],
    'texts'       => ['Тексты сайта', '¶'],
    'pages'       => ['Страницы', '❐'],
    'services'    => ['Услуги', '✦'],
    'projects'    => ['Проекты', '◧'],
    'partners'    => ['Партнёры', '⬡'],
    'images'      => ['Изображения', '❏'],
    'contacts'    => ['Контакты и соцсети', '☏'],
    'submissions' => ['Заявки с сайта', '✉'],
    'seo'         => ['SEO', '☌'],
    'smtp'        => ['Почта (SMTP)', '✉'],
    'security'    => ['Безопасность', '⚿'],
    'users'       => ['Пользователи', '☺'],
    'profile'     => ['Мой профиль', '☺'],
];
const GROUPS = [
    'ОСНОВНОЕ'  => ['overview'],
    'КОНТЕНТ'   => ['texts', 'pages', 'services', 'projects', 'partners', 'images', 'contacts', 'submissions'],
    'НАСТРОЙКИ' => ['seo', 'smtp', 'security', 'users', 'profile'],
];
const ADMIN_ONLY = ['users', 'smtp', 'security'];

/* Редактируемые поля страниц: slug → [ [ключ, подпись, тип(text|area|rich|img)], ... ]
   Ключи с префиксом media. пишутся в $site['media'], остальные — в $site['content']. */
const PAGE_FIELDS = [
    '' => [
        ['home.hero_title',       'Заголовок первого экрана', 'area'],
        ['media.hero',            'Первый экран · фоновое фото', 'img'],
        ['home.intro_label',      'Синий блок: короткий заголовок', 'area'],
        ['home.intro_text',       'Синий блок: текст', 'area'],
        ['home.projects_eyebrow', 'Секция «Layihələr» · надзаголовок', 'text'],
        ['home.projects_title',   'Секция «Layihələr» · заголовок', 'text'],
        ['home.about_eyebrow',    'Блок «Haqqımızda» · надзаголовок', 'text'],
        ['home.about_lead',       'Блок «Haqqımızda» · вводный текст', 'area'],
        ['home.about_text',       'Блок «Haqqımızda» · второй абзац', 'area'],
        ['home.adv_eyebrow',      'Блок «Üstünlüklər» · надзаголовок', 'text'],
        ['home.adv_title',        'Блок «Üstünlüklər» · заголовок', 'text'],
        ['home.services_eyebrow', 'Секция «Xidmətlər» · надзаголовок', 'text'],
        ['home.services_title',   'Секция «Xidmətlər» · заголовок', 'text'],
        ['home.partners_eyebrow', 'Секция «Partnyorlar» · надзаголовок', 'text'],
        ['home.partners_title',   'Секция «Partnyorlar» · заголовок', 'text'],
    ],
    'haqqimizda' => [
        ['about.kicker',     'Надзаголовок («Şirkət Haqqında»)', 'text'],
        ['about.intro',      'Вводный текст (справа сверху)', 'area'],
        ['about.p1',         'Основной текст · абзац 1', 'area'],
        ['about.p2',         'Основной текст · абзац 2', 'area'],
        ['about.p3',         'Основной текст · абзац 3', 'area'],
        ['about.p4',         'Основной текст · абзац 4', 'area'],
        ['about.figcaption', 'Подпись под фото', 'area'],
        ['about.certs_title', 'Заголовок блока «Sertifikatlar»', 'text'],
        ['about.image',      'Фото компании', 'img'],
        ['about.cert1',      'Сертификат 1', 'img'],
        ['about.cert2',      'Сертификат 2', 'img'],
        ['about.cert3',      'Сертификат 3', 'img'],
    ],
    'xidmetlerimiz' => [
        ['services.kicker',       'Надзаголовок', 'text'],
        ['services.hero_text',    'Вводный текст', 'area'],
        ['services.head_eyebrow', 'Список услуг · надзаголовок', 'text'],
        ['services.head_title',   'Список услуг · заголовок', 'text'],
    ],
    'layiheler' => [
        ['projects.lead', 'Подзаголовок страницы', 'area'],
    ],
    'elaqe' => [
        ['contact.lead',       'Подзаголовок страницы', 'area'],
        ['contact.info_title', 'Заголовок блока «Rekvizitlər»', 'text'],
        ['contact.form_title', 'Заголовок формы', 'text'],
    ],
];

/** Чтение поля страницы (учитывает префикс media.). */
function page_field_get(string $key): string
{
    global $site;
    if (str_starts_with($key, 'media.')) {
        return (string)($site['media'][substr($key, 6)] ?? '');
    }
    return kv_get('texts', $key);
}

/** Запись поля страницы (учитывает префикс media.). */
function page_field_set(string $key, string $val): void
{
    global $site;
    if (str_starts_with($key, 'media.')) {
        if (!isset($site['media']) || !is_array($site['media'])) {
            $site['media'] = [];
        }
        $site['media'][substr($key, 6)] = $val;
        return;
    }
    kv_set('texts', $key, $val);
}

/* ── Layout ───────────────────────────────────────────────────────────────── */

function layout_top(string $active, string $title): void
{
    $admin = current_admin();
    ob_start();
    echo '<!doctype html><html lang="ru"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>' . e($title) . ' — TCE admin</title>';
    echo '<style>' . admin_css() . '</style></head><body><div class="wrap">';
    echo '<aside class="side"><div class="brand"><img src="' . e(img_src('assets/img/logo-light.png')) . '" alt="TCE" style="max-height:38px;max-width:180px"></div><nav>';
    foreach (GROUPS as $glabel => $keys) {
        echo '<div class="navgroup">' . e($glabel) . '</div>';
        foreach ($keys as $key) {
            if (in_array($key, ADMIN_ONLY, true) && !is_admin()) {
                continue;
            }
            [$label, $ic] = SECTIONS[$key];
            $cls = $key === $active ? ' class="on"' : '';
            echo '<a' . $cls . ' href="index.php?section=' . $key . '"><i>' . $ic . '</i>' . e($label) . '</a>';
        }
    }
    echo '</nav><div class="who">Вы вошли как<br><b>' . e(($admin['name'] ?? '') !== '' ? $admin['name'] : ($admin['email'] ?? '')) . '</b></div></aside>';
    echo '<main><header class="bar"><h1>' . e($title) . '</h1><div>';
    echo i18n_switcher();
    echo '<a class="btn ghost" href="' . e(url('')) . '" target="_blank">Открыть сайт</a> ';
    echo '<a class="btn" href="index.php?section=logout">Выйти</a></div></header><div class="body">';
    echo flash_render();
}

function layout_bottom(): void
{
    echo '</div></main></div></body></html>';
    echo i18n_apply(ob_get_clean());
}

function admin_css(): string
{
    return <<<CSS
*{box-sizing:border-box}body{margin:0;font-family:-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f4f6f6;color:#12211d}
a{text-decoration:none}.wrap{display:flex;min-height:100vh}
.side{width:250px;background:#011640;color:#cfe3dd;display:flex;flex-direction:column;position:sticky;top:0;height:100vh}
.brand{display:flex;gap:10px;align-items:center;padding:20px 18px;font-size:12px;line-height:1.15;color:#fff;border-bottom:1px solid #0c2450}
.side nav{display:flex;flex-direction:column;padding:6px 0 14px;flex:1;overflow:auto}
.navgroup{color:#6f83a3;font-size:11px;letter-spacing:.08em;padding:15px 20px 5px;font-weight:700}
.side nav a{color:#b9c6dd;padding:12px 20px;font-size:14px;display:flex;gap:12px;align-items:center}
.side nav a i{width:18px;font-style:normal;opacity:.8}
.side nav a:hover{background:#0b2a54;color:#fff}
.side nav a.on{background:#1b4b8f;color:#fff;font-weight:700}
.who{padding:16px 20px;font-size:12px;color:#8296b5;border-top:1px solid #0c2450}
main{flex:1;min-width:0}
.bar{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;padding:18px 28px;background:#fff;border-bottom:1px solid #e3e8e7;position:sticky;top:0;z-index:5}
.bar h1{margin:0;font-size:22px}
.body{padding:26px 28px;max-width:1100px}
.btn{display:inline-block;background:#011640;color:#fff;border:0;padding:10px 18px;border-radius:24px;font-weight:700;cursor:pointer;font-size:14px}
.btn:hover{background:#0a2a5c}.btn.ghost{background:transparent;color:#011640;border:1.5px solid #011640}
.btn.sm{padding:6px 12px;font-size:13px;border-radius:8px}.btn.red{background:#b91c1c}.btn.red:hover{background:#991717}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:22px}
.card{background:#fff;border:1px solid #e6ebea;border-radius:14px;padding:20px 22px}
.card .n{font-size:34px;font-weight:800;color:#011640}.card .l{color:#5b6f6a;font-size:13px;text-transform:uppercase;letter-spacing:.03em}
.panel{background:#fff;border:1px solid #e6ebea;border-radius:14px;padding:22px;margin-bottom:20px}
table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:11px 12px;border-bottom:1px solid #eef2f1;font-size:14px;vertical-align:top}
th{color:#5b6f6a;font-size:12px;text-transform:uppercase;letter-spacing:.03em}
label{display:block;font-weight:600;margin:14px 0 6px;font-size:14px}
input[type=text],input[type=email],input[type=password],input[type=number],textarea,select{width:100%;padding:11px 13px;border:1px solid #cfd8d6;border-radius:9px;font-size:14px;font-family:inherit}
textarea{min-height:90px}.row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.muted{color:#5b6f6a;font-size:13px}.right{text-align:right}
.login{min-height:100vh;display:grid;place-items:center;background:#011640;padding:16px}
.login .box{background:#fff;padding:38px 34px;border-radius:18px;width:360px;max-width:92vw;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.login h2{margin:6px 0 2px}.login .sub{color:#5b6f6a;margin-bottom:18px}
@media(max-width:820px){.side{width:64px}.side .brand,.who{display:none}.side nav a{justify-content:center;padding:14px 0;font-size:0}.side nav a i{font-size:16px}.navgroup{padding:12px 0 4px;text-align:center;font-size:9px}.row{grid-template-columns:1fr}.bar{padding:14px 16px}.body{padding:18px 14px}.panel{overflow-x:auto}}
CSS;
}

require_once __DIR__ . '/i18n.php';
