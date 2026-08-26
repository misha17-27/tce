<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
boot_session();

$section = preg_replace('/[^a-z_]/', '', $_GET['section'] ?? 'overview') ?? 'overview';

/* ---- язык интерфейса админки (?lang=az|ru|en, хранится в cookie) ---- */
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ru', 'az', 'en'], true)) {
    setcookie('alang', $_GET['lang'], ['expires' => time() + 86400 * 365, 'path' => '/', 'samesite' => 'Lax']);
    $_COOKIE['alang'] = $_GET['lang'];
    redirect('index.php?section=' . $section);
}

/* ---- logout ---- */
if ($section === 'logout') {
    logout();
    redirect('index.php?section=login');
}

/* ---- login ---- */
if ($section === 'login') {
    if (current_admin()) {
        redirect('index.php');
    }
    $err = '';
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        csrf_check();
        $__ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if (login_locked($__ip)) {
            $err = 'Слишком много попыток входа. Попробуйте через 15 минут.';
        } elseif (!turnstile_verify((string)($_POST['cf-turnstile-response'] ?? ''))) {
            $err = 'Подтвердите, что вы не робот.';
        } elseif (attempt_login((string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''))) {
            login_ok($__ip);
            redirect('index.php');
        } else {
            login_fail($__ip);
            $err = 'Неверный e-mail или пароль.';
        }
    }
    ob_start();
    echo '<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Вход — TCE admin</title><style>' . admin_css() . '</style></head><body>';
    echo '<div class="login"><form class="box" method="post">';
    echo '<div style="display:flex;justify-content:flex-end;margin-bottom:6px">' . i18n_switcher() . '</div>';
    echo '<div style="margin-bottom:14px"><img src="' . e(img_src('assets/img/logo.svg')) . '" alt="TCE" style="max-height:46px;max-width:220px"></div>';
    echo '<h2>Вход в панель</h2><div class="sub">Управление контентом сайта</div>';
    if ($err) {
        echo '<div style="background:#b91c1c;color:#fff;padding:10px;border-radius:8px;margin-bottom:12px">' . e($err) . '</div>';
    }
    echo csrf_field();
    echo '<label>E-mail</label><input type="email" name="email" required autofocus autocomplete="username">';
    echo '<label>Пароль</label><input type="password" name="password" required autocomplete="current-password">';
    echo '<div style="margin-top:14px;display:flex;justify-content:center">';
    turnstile_widget();
    echo '</div>';
    echo '<button class="btn" style="width:100%;margin-top:20px" type="submit">Войти</button>';
    echo '</form></div></body></html>';
    echo i18n_apply((string)ob_get_clean());
    exit;
}

/* ---- всё ниже требует авторизации ---- */
require_login();
if (!isset(SECTIONS[$section])) {
    $section = 'overview';
}
if (in_array($section, ADMIN_ONLY, true) && !is_admin()) {
    flash('Недостаточно прав (нужна роль Администратор).', 'err');
    redirect('index.php?section=overview');
}
csrf_check();

/* ---- заявки: чтение/запись storage/messages.json ---- */
function submissions_all(): array
{
    $file = storage_path('messages.json');
    if (!is_file($file)) {
        return [];
    }
    $list = json_decode((string)file_get_contents($file), true);
    return is_array($list) ? $list : [];
}
function submissions_save(array $list): void
{
    $payload = json_encode(array_values($list), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($payload !== false) {
        file_put_contents(storage_path('messages.json'), $payload . "\n", LOCK_EX);
    }
}
function lines_to_array(string $value): array
{
    $lines = preg_split('/\R+/', trim($value)) ?: [];
    return array_values(array_filter(array_map('trim', $lines), static fn(string $l): bool => $l !== ''));
}
function array_to_lines(array $items): string
{
    return implode("\n", array_map('strval', $items));
}

/* ================= POST actions ================= */
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $act = $_POST['action'] ?? '';

    if ($section === 'texts' && $act === 'save') {
        foreach (['site_short' => 'name', 'site_full' => 'full_name', 'site_slogan' => 'tagline'] as $f => $cfg) {
            if (isset($_POST[$f])) {
                $site[$cfg] = trim((string)$_POST[$f]);
            }
        }
        foreach ((array)($_POST['t'] ?? []) as $k => $v) {
            kv_set('texts', (string)$k, (string)$v);
        }
        save_site();
        flash('Тексты сохранены.');
        redirect('index.php?section=texts');
    }

    if ($section === 'contacts' && $act === 'save') {
        foreach (['phone', 'phone_href', 'email', 'address', 'hours', 'map_embed', 'whatsapp'] as $k) {
            kv_set('contacts', $k, trim((string)($_POST[$k] ?? '')));
        }
        $socials = [];
        foreach ([['soc_facebook', 'Facebook'], ['soc_instagram', 'Instagram'], ['soc_linkedin', 'LinkedIn'],
                  ['soc_youtube', 'YouTube'], ['soc_x', 'X (Twitter)']] as [$k, $label]) {
            $u = trim((string)($_POST[$k] ?? ''));
            if ($u !== '' && $u !== '#') {
                $socials[] = ['label' => $label, 'url' => $u];
            }
        }
        $site['contacts']['socials'] = $socials;
        save_site();
        flash('Контакты сохранены.');
        redirect('index.php?section=contacts');
    }

    if ($section === 'services' && $act === 'save') {
        $services = [];
        foreach ((array)($_POST['svc'] ?? []) as $row) {
            $title = trim((string)($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $services[] = [
                'slug'  => slugify((string)($row['slug'] ?? '') !== '' ? (string)$row['slug'] : $title),
                'code'  => trim((string)($row['code'] ?? '')),
                'title' => $title,
                'lead'  => trim((string)($row['short'] ?? '')),
                'text'  => trim((string)($row['descr'] ?? '')),
                'items' => lines_to_array((string)($row['points'] ?? '')),
            ];
        }
        $site['services'] = $services;
        save_site();
        flash('Услуги сохранены.');
        redirect('index.php?section=services');
    }
    if ($section === 'services' && $act === 'save_groups') {
        $groups = [];
        foreach ((array)($_POST['grp'] ?? []) as $row) {
            $title = trim((string)($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $groups[] = [
                'slug'  => slugify((string)($row['slug'] ?? '') !== '' ? (string)$row['slug'] : $title),
                'title' => $title,
                'items' => lines_to_array((string)($row['points'] ?? '')),
            ];
        }
        $site['service_groups'] = $groups;
        save_site();
        flash('Услуги сохранены.');
        redirect('index.php?section=services');
    }

    if ($section === 'projects') {
        if ($act === 'save') {
            $key = (string)($_POST['key'] ?? '');           // исходный slug ('' = новый проект)
            $cover = trim((string)($_POST['cover'] ?? ''));
            if ($p = admin_upload('cover_file')) {
                $cover = $p;
            }
            $video = trim((string)($_POST['video'] ?? ''));
            if ($p = admin_upload('video_file')) {
                $video = $p;
            }
            $gallery = array_values(array_filter(array_map('trim', (array)($_POST['gallery'] ?? [])), static fn($x) => $x !== ''));
            foreach (admin_upload_multi('gallery_files') as $p) {
                $gallery[] = $p;
            }
            $gallery = array_values(array_unique($gallery));
            if ($gallery === [] && $cover !== '') {
                $gallery = [$cover];
            }

            // slug: авто из названия, всегда нормализован, уникален
            $slug = slugify(trim((string)($_POST['slug'] ?? '')) !== '' ? (string)$_POST['slug'] : (string)($_POST['title'] ?? ''));
            if ($slug === '') {
                $slug = 'layihe-' . substr(bin2hex(random_bytes(3)), 0, 6);
            }
            $others = [];
            foreach ($site['projects'] as $pr) {
                if ((string)$pr['slug'] !== $key) {
                    $others[(string)$pr['slug']] = true;
                }
            }
            $base = $slug;
            $n = 2;
            while (isset($others[$slug])) {
                $slug = $base . '-' . $n++;
            }

            $proj = [
                'slug' => $slug,
                'title' => trim((string)($_POST['title'] ?? '')),
                'category' => trim((string)($_POST['category'] ?? '')),
                'year' => trim((string)($_POST['year'] ?? '')),
                'location' => trim((string)($_POST['location'] ?? '')),
                'area' => trim((string)($_POST['area'] ?? '')),
                'client' => trim((string)($_POST['client'] ?? '')),
                'cover' => $cover,
                'video' => $video,
                'gallery' => $gallery,
                'summary' => trim((string)($_POST['descr'] ?? '')),
                'body' => clean_rich_text((string)($_POST['content'] ?? '')),
                'scope' => lines_to_array((string)($_POST['scope'] ?? '')),
                'sort' => (int)($_POST['sort'] ?? 0),
                'visible' => isset($_POST['visible']) ? 1 : 0,
                'status' => ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published',
            ];

            $found = false;
            foreach ($site['projects'] as $i => $pr) {
                if ((string)$pr['slug'] === $key && $key !== '') {
                    $site['projects'][$i] = $proj;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $site['projects'][] = $proj;
            }

            // SEO проекта
            $oldSeoKey = 'layihe/' . $key;
            $newSeoKey = 'layihe/' . $slug;
            $site['seo']['pages'][$newSeoKey] = [
                'title' => trim((string)($_POST['seo_title'] ?? '')),
                'description' => trim((string)($_POST['seo_desc'] ?? '')),
                'robots' => trim((string)($_POST['robots'] ?? 'index,follow')),
                'canonical' => trim((string)($_POST['canonical'] ?? '')),
            ];
            if ($key !== '' && $oldSeoKey !== $newSeoKey) {
                unset($site['seo']['pages'][$oldSeoKey]);
            }
            save_site();
            flash('Проект сохранён.');
            redirect('index.php?section=projects');
        } elseif ($act === 'del') {
            $key = (string)($_POST['key'] ?? '');
            $site['projects'] = array_values(array_filter($site['projects'], static fn(array $p): bool => (string)$p['slug'] !== $key));
            unset($site['seo']['pages']['layihe/' . $key]);
            save_site();
            flash('Удалено.');
            redirect('index.php?section=projects');
        }
    }

    if ($section === 'partners') {
        if (!empty($_POST['delete_id'])) {
            $idx = (int)$_POST['delete_id'] - 1;
            if (isset($site['partners'][$idx])) {
                array_splice($site['partners'], $idx, 1);
                save_site();
            }
            flash('Партнёр удалён.');
            redirect('index.php?section=partners');
        }
        if ($act === 'add') {
            $img = trim((string)($_POST['image'] ?? ''));
            if ($p = admin_upload('logo_file')) {
                $img = $p;
            }
            if ($img === '') {
                flash('Добавь файл логотипа или путь к нему.', 'err');
            } else {
                $site['partners'][] = [
                    'name' => trim((string)($_POST['name'] ?? '')),
                    'logo' => $img,
                    'url' => trim((string)($_POST['url'] ?? '')),
                    'sort' => (int)($_POST['sort'] ?? 0),
                    'visible' => 1,
                ];
                save_site();
                flash('Партнёр добавлен.');
            }
            redirect('index.php?section=partners');
        }
        if ($act === 'save') {
            foreach ((array)($_POST['p'] ?? []) as $id => $row) {
                $idx = (int)$id - 1;
                if (!isset($site['partners'][$idx])) {
                    continue;
                }
                $site['partners'][$idx]['name'] = trim((string)($row['name'] ?? ''));
                $site['partners'][$idx]['url'] = trim((string)($row['url'] ?? ''));
                $site['partners'][$idx]['sort'] = (int)($row['sort'] ?? 0);
                $site['partners'][$idx]['visible'] = isset($row['visible']) ? 1 : 0;
            }
            save_site();
            flash('Изменения сохранены.');
            redirect('index.php?section=partners');
        }
    }

    if ($section === 'pages' && $act === 'save') {
        $slug = (string)($_POST['slug'] ?? '');
        $pageKey = $slug === '' ? 'home' : $slug;
        if (array_key_exists($slug, (array)$site['nav'])) {
            $label = trim((string)($_POST['title'] ?? ''));
            if ($label !== '') {
                $site['nav'][$slug] = $label;
            }
            foreach (PAGE_FIELDS[$slug] ?? [] as [$fkey, , $type]) {
                $val = (string)($_POST['f'][$fkey] ?? '');
                if ($type === 'img' && !empty($_FILES['fimg']['name'][$fkey])) {
                    if ($p = admin_move((string)$_FILES['fimg']['tmp_name'][$fkey], (string)$_FILES['fimg']['name'][$fkey])) {
                        $val = $p;
                    }
                }
                if ($type === 'rich') {
                    $val = clean_rich_text($val);
                }
                page_field_set($fkey, $val);
            }
            $site['seo']['pages'][$pageKey] = [
                'title' => trim((string)($_POST['seo_title'] ?? '')),
                'description' => trim((string)($_POST['seo_desc'] ?? '')),
                'robots' => trim((string)($_POST['robots'] ?? 'index,follow')),
                'canonical' => trim((string)($_POST['canonical'] ?? '')),
            ];
            save_site();
            flash('Страница сохранена.');
        }
        redirect('index.php?section=pages');
    }

    if ($section === 'seo') {
        if ($act === 'global') {
            $og = trim((string)($_POST['og_image'] ?? ''));
            if ($p = admin_upload('og_file')) {
                $og = $p;
            }
            kv_set('settings', 'og_image', $og);
            kv_set('settings', 'search_visible', ($_POST['search_visible'] ?? '1') === '0' ? '0' : '1');
            $ne = trim((string)($_POST['notify_email'] ?? ''));
            if ($ne === '' || filter_var($ne, FILTER_VALIDATE_EMAIL)) {
                kv_set('settings', 'notify_email', $ne);
            } else {
                flash('Почта для заявок: некорректный адрес.', 'err');
            }
            $site['seo']['description'] = trim((string)($_POST['global_desc'] ?? ($site['seo']['description'] ?? '')));
            save_site();
            flash('Настройки SEO сохранены.');
            redirect('index.php?section=seo');
        }
        if ($act === 'save') {
            foreach ((array)($_POST['title'] ?? []) as $slug => $title) {
                $slug = (string)$slug;
                $row = $site['seo']['pages'][$slug] ?? [];
                $row['title'] = trim((string)$title);
                $row['description'] = trim((string)($_POST['descr'][$slug] ?? ''));
                $site['seo']['pages'][$slug] = $row;
            }
            save_site();
            flash('SEO сохранён.');
            redirect('index.php?section=seo');
        }
    }

    if ($section === 'submissions') {
        $list = submissions_all();
        if ($act === 'read') {
            foreach ($list as $i => $m) {
                if ((string)($m['id'] ?? '') === (string)($_POST['id'] ?? '')) {
                    $list[$i]['status'] = 'read';
                }
            }
            submissions_save($list);
        } elseif ($act === 'read_all') {
            foreach ($list as $i => $m) {
                $list[$i]['status'] = 'read';
            }
            submissions_save($list);
            flash('Все заявки отмечены прочитанными.');
        } elseif ($act === 'del') {
            $list = array_values(array_filter($list, static fn(array $m): bool => (string)($m['id'] ?? '') !== (string)($_POST['id'] ?? '')));
            submissions_save($list);
            flash('Заявка удалена.');
        }
        redirect('index.php?section=submissions');
    }

    if ($section === 'profile') {
        $a = current_admin();
        $data = admin_users();
        if ($act === 'save') {
            foreach ($data['users'] as $i => $u) {
                if ((int)$u['id'] === (int)$a['id']) {
                    $em = trim((string)($_POST['email'] ?? $u['email']));
                    if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
                        $data['users'][$i]['email'] = $em;
                        $_SESSION['admin']['email'] = $em;
                    }
                    $data['users'][$i]['name'] = trim((string)($_POST['name'] ?? ''));
                    $_SESSION['admin']['name'] = $data['users'][$i]['name'];
                }
            }
            save_admin_users($data);
            flash('Профиль сохранён.');
        } elseif ($act === 'passwd') {
            $me = null;
            foreach ($data['users'] as $i => $u) {
                if ((int)$u['id'] === (int)$a['id']) {
                    $me = $i;
                }
            }
            if ($me === null || !password_verify((string)($_POST['old'] ?? ''), (string)$data['users'][$me]['pass_hash'])) {
                flash('Текущий пароль неверный.', 'err');
            } elseif (strlen((string)($_POST['new'] ?? '')) < 8) {
                flash('Пароль минимум 8 символов.', 'err');
            } elseif (($_POST['new'] ?? '') !== ($_POST['new2'] ?? '')) {
                flash('Пароли не совпадают.', 'err');
            } else {
                $data['users'][$me]['pass_hash'] = password_hash((string)$_POST['new'], PASSWORD_DEFAULT);
                save_admin_users($data);
                @unlink(storage_path('admin-password.txt'));
                flash('Пароль изменён.');
            }
        }
        redirect('index.php?section=profile');
    }

    if ($section === 'security' && $act === 'save') {
        kv_set('settings', 'turnstile_site', trim((string)($_POST['turnstile_site'] ?? '')));
        if (($_POST['turnstile_secret'] ?? '') !== '') {
            kv_set('settings', 'turnstile_secret', trim((string)$_POST['turnstile_secret']));
        }
        save_site();
        flash('Настройки безопасности сохранены.');
        redirect('index.php?section=security');
    }

    if ($section === 'smtp') {
        if ($act === 'save') {
            foreach (['smtp_host', 'smtp_port', 'smtp_user', 'smtp_from', 'smtp_from_name', 'smtp_secure'] as $k) {
                kv_set('settings', $k, trim((string)($_POST[$k] ?? '')));
            }
            if (($_POST['smtp_pass'] ?? '') !== '') {
                kv_set('settings', 'smtp_pass', (string)$_POST['smtp_pass']);
            }
            save_site();
            flash('Настройки SMTP сохранены.');
            redirect('index.php?section=smtp');
        } elseif ($act === 'test') {
            $err = '';
            $to = trim((string)($_POST['test_to'] ?? ''));
            if (filter_var($to, FILTER_VALIDATE_EMAIL)
                && smtp_send($to, 'Тест SMTP — tce.az', "Это тестовое письмо из админ-панели.\nЕсли вы его получили — SMTP настроен верно.", $err)) {
                flash('Тестовое письмо отправлено на ' . $to);
            } else {
                flash('Ошибка отправки: ' . ($err !== '' ? $err : 'некорректный адрес'), 'err');
            }
            redirect('index.php?section=smtp');
        }
    }

    if ($section === 'users') {
        $data = admin_users();
        if (!empty($_POST['delete_id'])) {
            $id = (int)$_POST['delete_id'];
            if ($id !== (int)current_admin()['id']) {
                $data['users'] = array_values(array_filter($data['users'], static fn(array $u): bool => (int)$u['id'] !== $id));
                save_admin_users($data);
                flash('Пользователь удалён.');
            } else {
                flash('Нельзя удалить самого себя.', 'err');
            }
            redirect('index.php?section=users');
        }
        if ($act === 'add') {
            $em = trim((string)($_POST['email'] ?? ''));
            $exists = false;
            foreach ($data['users'] as $u) {
                if (strcasecmp((string)$u['email'], $em) === 0) {
                    $exists = true;
                }
            }
            if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
                flash('Некорректный e-mail.', 'err');
            } elseif (strlen((string)($_POST['password'] ?? '')) < 8) {
                flash('Пароль минимум 8 символов.', 'err');
            } elseif ($exists) {
                flash('Пользователь с таким e-mail уже есть.', 'err');
            } else {
                $data['users'][] = [
                    'id' => (int)($data['next_id'] ?? count($data['users']) + 1),
                    'name' => trim((string)($_POST['name'] ?? '')),
                    'email' => $em,
                    'pass_hash' => password_hash((string)$_POST['password'], PASSWORD_DEFAULT),
                    'role' => ($_POST['role'] ?? 'editor') === 'admin' ? 'admin' : 'editor',
                    'active' => 1,
                    'last_login' => null,
                ];
                $data['next_id'] = (int)($data['next_id'] ?? count($data['users'])) + 1;
                save_admin_users($data);
                flash('Пользователь добавлен.');
            }
            redirect('index.php?section=users');
        } elseif ($act === 'save') {
            $me = (int)current_admin()['id'];
            foreach ((array)($_POST['u'] ?? []) as $id => $row) {
                $id = (int)$id;
                foreach ($data['users'] as $i => $u) {
                    if ((int)$u['id'] !== $id) {
                        continue;
                    }
                    $role = ($row['role'] ?? 'editor') === 'admin' ? 'admin' : 'editor';
                    $active = isset($row['active']) ? 1 : 0;
                    if ($id === $me) {  // не запираем себя
                        $active = 1;
                        $role = 'admin';
                    }
                    $data['users'][$i]['name'] = trim((string)($row['name'] ?? ''));
                    $data['users'][$i]['role'] = $role;
                    $data['users'][$i]['active'] = $active;
                    if (($row['password'] ?? '') !== '' && strlen((string)$row['password']) >= 8) {
                        $data['users'][$i]['pass_hash'] = password_hash((string)$row['password'], PASSWORD_DEFAULT);
                    }
                }
            }
            save_admin_users($data);
            flash('Изменения сохранены.');
            redirect('index.php?section=users');
        }
    }

    if ($section === 'images' && $act === 'upload') {
        if ($p = admin_upload('file')) {
            flash('Загружено: ' . $p);
        } else {
            flash('Не удалось загрузить (jpg/png/webp/gif/svg/mp4/webm/ogg/mov).', 'err');
        }
        redirect('index.php?section=images');
    }
}

/* ================= render ================= */
$titles = [];
foreach (SECTIONS as $k => [$label]) {
    $titles[$k] = $label;
}
layout_top($section, $titles[$section] ?? 'Панель');

if ($section === 'overview') {
    $subs = submissions_all();
    $nunread = count(array_filter($subs, static fn(array $m): bool => ($m['status'] ?? 'new') !== 'read'));
    echo '<div class="cards">';
    foreach ([[count((array)($site['content'] ?? [])), 'текстовых блоков'],
              [count((array)$site['services']), 'услуг'],
              [count((array)$site['projects']), 'проектов'],
              [count($subs), 'заявок']] as [$n, $l]) {
        echo '<div class="card"><div class="n">' . $n . '</div><div class="l">' . $l . '</div></div>';
    }
    echo '</div>';
    echo '<div class="panel"><h3>Быстрые действия</h3><p class="muted">Чаще всего меняют тексты, проекты, контакты и заявки.</p>';
    foreach ([['texts', 'Редактировать тексты'], ['projects', 'Проекты'], ['contacts', 'Контакты'], ['submissions', 'Заявки с сайта']] as [$s, $l]) {
        echo '<a class="btn" style="margin:6px 8px 0 0" href="index.php?section=' . $s . '">' . $l . '</a>';
    }
    if ($nunread) {
        echo '<p style="margin-top:16px">🔔 Новых заявок: <b>' . $nunread . '</b></p>';
    }
    echo '</div>';
}

elseif ($section === 'texts') {
    $t = static fn(string $k): string => e(kv_get('texts', $k));
    echo '<form method="post" class="panel"><h3>Основное</h3>' . csrf_field() . '<input type="hidden" name="action" value="save">';
    echo '<label>Короткое имя</label><input type="text" name="site_short" value="' . e($site['name']) . '">';
    echo '<label>Полное имя</label><input type="text" name="site_full" value="' . e($site['full_name']) . '">';
    echo '<label>Слоган</label><input type="text" name="site_slogan" value="' . e($site['tagline']) . '">';
    echo '<div style="margin-top:16px"><button class="btn">Сохранить основное</button></div></form>';

    echo '<form method="post" class="panel"><h3>Редактируемые тексты</h3>' . csrf_field() . '<input type="hidden" name="action" value="save">';
    echo '<label>Заголовок первого экрана</label><textarea name="t[home.hero_title]">' . $t('home.hero_title') . '</textarea>';
    echo '<label>Синий блок: короткий заголовок</label><textarea name="t[home.intro_label]">' . $t('home.intro_label') . '</textarea>';
    echo '<label>Синий блок: текст</label><textarea name="t[home.intro_text]" style="min-height:120px">' . $t('home.intro_text') . '</textarea>';
    echo '<label>CTA-блок: заголовок</label><input type="text" name="t[cta.title]" value="' . $t('cta.title') . '">';
    echo '<label>CTA-блок: текст</label><textarea name="t[cta.text]">' . $t('cta.text') . '</textarea>';
    echo '<div style="margin-top:16px"><button class="btn">Сохранить тексты</button></div></form>';
    echo '<p class="muted">Тексты конкретных страниц редактируются в разделе «Страницы» → выбери страницу.</p>';
}

elseif ($section === 'contacts') {
    $g = static fn(string $k): string => e(kv_get('contacts', $k));
    $soc = [];
    foreach ((array)($site['contacts']['socials'] ?? []) as $s) {
        $soc[mb_strtolower((string)($s['label'] ?? ''))] = (string)($s['url'] ?? '');
    }
    $socGet = static fn(string $label): string => e($soc[mb_strtolower($label)] ?? '');
    echo '<form method="post" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="save">';
    echo '<label>Телефон</label><input type="text" name="phone" value="' . $g('phone') . '">';
    echo '<label>Телефон для ссылки tel:</label><input type="text" name="phone_href" value="' . $g('phone_href') . '">';
    echo '<label>E-mail</label><input type="text" name="email" value="' . $g('email') . '">';
    echo '<label>Адрес</label><input type="text" name="address" value="' . $g('address') . '">';
    echo '<label>Часы работы</label><input type="text" name="hours" value="' . $g('hours') . '">';
    echo '<label>Google Maps embed URL</label><input type="text" name="map_embed" value="' . $g('map_embed') . '">';
    $wa = kv_get('contacts', 'whatsapp');
    $waDigits = preg_replace('/[^0-9]/', '', $wa !== '' ? $wa : kv_get('contacts', 'phone')) ?? '';
    echo '<fieldset style="border:1px solid #e6ebea;border-radius:10px;padding:12px 16px;margin-top:14px"><legend style="color:#011640;font-weight:700">WhatsApp</legend>';
    echo '<p class="muted">Номер для круглой кнопки в правом нижнем углу сайта. В ссылку уйдут только цифры. Пусто — кнопка не показывается.</p>';
    echo '<div class="row"><div><label>Номер WhatsApp</label><input type="text" name="whatsapp" value="' . e($wa) . '" placeholder="+994 ..."></div>';
    echo '<div><label>Ссылка получится такой</label><input type="text" readonly value="' . ($wa !== '' && $waDigits !== '' ? 'https://wa.me/' . e($waDigits) : '—') . '"></div></div></fieldset>';
    echo '<fieldset style="border:1px solid #e6ebea;border-radius:10px;padding:12px 16px;margin-top:14px"><legend style="color:#011640;font-weight:700">Социальные сети</legend>';
    echo '<p class="muted">Показываются в подвале сайта. Заполняй только те, что есть.</p>';
    foreach ([['soc_linkedin', 'LinkedIn', 'https://www.linkedin.com/company/...'],
              ['soc_instagram', 'Instagram', 'https://www.instagram.com/...'],
              ['soc_facebook', 'Facebook', 'https://www.facebook.com/...'],
              ['soc_youtube', 'YouTube', 'https://www.youtube.com/@...'],
              ['soc_x', 'X (Twitter)', 'https://x.com/...']] as [$k, $l, $ph]) {
        echo '<label>' . $l . '</label><input type="text" name="' . $k . '" value="' . $socGet($l) . '" placeholder="' . $ph . '">';
    }
    echo '</fieldset>';
    echo '<div style="margin-top:18px"><button class="btn">Сохранить контакты</button></div></form>';
}

elseif ($section === 'services') {
    $slots = $site['services'];
    for ($i = 0; $i < 2; $i++) {
        $slots[] = ['slug' => '', 'code' => '', 'title' => '', 'lead' => '', 'text' => '', 'items' => []];
    }
    echo '<form method="post" class="panel"><h3>Xidmət kartları (ana səhifə)</h3><p class="muted">Показываются карточками на главной и в подвале сайта.</p>' . csrf_field() . '<input type="hidden" name="action" value="save">';
    foreach ($slots as $i => $s) {
        echo '<div style="border-bottom:1px solid #eef2f1;padding-bottom:16px;margin-bottom:16px"><b style="color:#011640">Услуга ' . ($i + 1) . '</b>';
        echo '<label>Slug</label><input type="text" name="svc[' . $i . '][slug]" value="' . e($s['slug'] ?? '') . '">';
        echo '<label>Код</label><input type="text" name="svc[' . $i . '][code]" value="' . e($s['code'] ?? '') . '">';
        echo '<label>Название</label><input type="text" name="svc[' . $i . '][title]" value="' . e($s['title'] ?? '') . '">';
        echo '<label>Кратко</label><textarea name="svc[' . $i . '][short]">' . e($s['lead'] ?? '') . '</textarea>';
        echo '<label>Описание</label><textarea name="svc[' . $i . '][descr]">' . e($s['text'] ?? '') . '</textarea>';
        echo '<label>Пункты, каждый с новой строки</label><textarea name="svc[' . $i . '][points]">' . e(array_to_lines((array)($s['items'] ?? []))) . '</textarea>';
        echo '</div>';
    }
    echo '<button class="btn" style="width:100%">Сохранить услуги</button></form>';

    $gslots = (array)($site['service_groups'] ?? []);
    $gslots[] = ['slug' => '', 'title' => '', 'items' => []];
    echo '<form method="post" class="panel"><h3>Xidmət qrupları (səhifə «Xidmətlərimiz»)</h3><p class="muted">Подробный список направлений на странице услуг.</p>' . csrf_field() . '<input type="hidden" name="action" value="save_groups">';
    foreach ($gslots as $i => $s) {
        echo '<div style="border-bottom:1px solid #eef2f1;padding-bottom:16px;margin-bottom:16px"><b style="color:#011640">Услуга ' . ($i + 1) . '</b>';
        echo '<label>Slug</label><input type="text" name="grp[' . $i . '][slug]" value="' . e($s['slug'] ?? '') . '">';
        echo '<label>Название</label><input type="text" name="grp[' . $i . '][title]" value="' . e($s['title'] ?? '') . '">';
        echo '<label>Пункты, каждый с новой строки</label><textarea name="grp[' . $i . '][points]">' . e(array_to_lines((array)($s['items'] ?? []))) . '</textarea>';
        echo '</div>';
    }
    echo '<button class="btn" style="width:100%">Сохранить услуги</button></form>';
    echo '<p class="muted">Чтобы удалить услугу — очисти её «Название» и сохрани.</p>';
}

elseif ($section === 'projects') { render_projects(); }

elseif ($section === 'partners') {
    $maxs = 0;
    foreach ((array)$site['partners'] as $p) {
        $maxs = max($maxs, (int)($p['sort'] ?? 0) + 1);
    }
    echo '<form method="post" enctype="multipart/form-data" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="add">';
    echo '<h3>Добавить партнёра</h3><p class="muted">Лучше всего — логотип на прозрачном фоне (PNG/SVG/WebP).</p>';
    echo '<div class="row3"><div><label>Название</label><input type="text" name="name" placeholder="Например: Siemens"></div>';
    echo '<div><label>Ссылка на сайт (необязательно)</label><input type="text" name="url" placeholder="https://..."></div>';
    echo '<div><label>Порядок</label><input type="number" name="sort" value="' . $maxs . '"></div></div>';
    echo '<label>Файл логотипа</label><input type="file" name="logo_file" accept="image/*">';
    echo '<label>или путь к картинке</label><input type="text" name="image" placeholder="assets/img/partners/...">';
    echo '<div style="margin-top:16px"><button class="btn">Добавить партнёра</button></div></form>';

    echo '<form method="post" class="panel"><h3>Список партнёров</h3><p class="muted">Логотипы показываются на главной странице. Порядок — по числу.</p>'
        . csrf_field() . '<input type="hidden" name="action" value="save">';
    echo '<table><tr><th>Логотип</th><th>Название</th><th>Ссылка</th><th>Порядок</th><th>Показывать</th><th></th></tr>';
    if (!$site['partners']) {
        echo '<tr><td colspan="6" class="muted">Пока нет партнёров.</td></tr>';
    }
    foreach ((array)$site['partners'] as $i => $r) {
        $id = $i + 1;
        echo '<tr><td><img src="' . e(img_src((string)($r['logo'] ?? ''))) . '" style="width:86px;height:46px;object-fit:contain;background:#fff;border:1px solid #eef2f1;border-radius:8px;padding:4px" alt=""></td>';
        echo '<td><input type="text" name="p[' . $id . '][name]" value="' . e($r['name'] ?? '') . '" style="width:150px"></td>';
        echo '<td><input type="text" name="p[' . $id . '][url]" value="' . e($r['url'] ?? '') . '" style="width:170px"></td>';
        echo '<td><input type="number" name="p[' . $id . '][sort]" value="' . e((string)($r['sort'] ?? $i)) . '" style="width:70px"></td>';
        echo '<td style="text-align:center"><input type="checkbox" name="p[' . $id . '][visible]" ' . ((int)($r['visible'] ?? 1) ? 'checked' : '') . '></td>';
        echo '<td class="right"><button class="btn sm red" name="delete_id" value="' . $id . '" formnovalidate onclick="return confirm(\'Удалить партнёра?\')">Удалить</button></td></tr>';
    }
    echo '</table><div style="margin-top:16px"><button class="btn">Сохранить изменения</button></div></form>';
}

elseif ($section === 'pages') {
    $mkurl = static fn(string $slug): string => $slug === '' ? url('') . '/' : url($slug);
    if (isset($_GET['edit'])) {
        $slug = (string)$_GET['edit'] === 'home' ? '' : (string)$_GET['edit'];
        $pageKey = $slug === '' ? 'home' : $slug;
        if (!array_key_exists($slug, (array)$site['nav'])) {
            echo '<div class="panel">Страница не найдена. <a href="index.php?section=pages">Назад</a></div>';
        } else {
            $label = (string)$site['nav'][$slug];
            $seo = $site['seo']['pages'][$pageKey] ?? ['title' => '', 'description' => '', 'robots' => 'index,follow', 'canonical' => ''];
            $url = $mkurl($slug);
            echo '<form method="post" enctype="multipart/form-data" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="save"><input type="hidden" name="slug" value="' . e($slug) . '">';
            echo '<h3>Редактировать: ' . e($label) . '</h3><p class="muted">Permalink: <a href="' . e($url) . '" target="_blank" style="color:#1b4b8f">' . e(absolute_url($slug)) . '</a></p>';
            echo '<label>Заголовок / пункт меню</label><input type="text" name="title" value="' . e($label) . '">';
            $fields = PAGE_FIELDS[$slug] ?? [];
            if ($fields) {
                echo '<fieldset style="border:1px solid #e6ebea;border-radius:10px;padding:14px 16px;margin-top:16px"><legend style="color:#011640;font-weight:700">Контент страницы</legend>';
                $hasRich = false;
                $imgs = array_filter($fields, static fn(array $f): bool => $f[2] === 'img');
                foreach ($fields as [$fkey, $flabel, $ftype]) {
                    if ($ftype === 'img') {
                        continue; // рендерится в блоке изображений ниже
                    }
                    $val = page_field_get($fkey);
                    if ($ftype === 'rich') {
                        rich_field('f[' . $fkey . ']', $val, $flabel);
                        $hasRich = true;
                    } elseif ($ftype === 'text') {
                        echo '<label>' . e($flabel) . '</label><input type="text" name="f[' . e($fkey) . ']" value="' . e($val) . '">';
                    } else {
                        echo '<label>' . e($flabel) . '</label><textarea name="f[' . e($fkey) . ']" style="min-height:90px">' . e($val) . '</textarea>';
                    }
                }
                if ($imgs) {
                    echo '<div style="margin-top:20px;font-weight:700;color:#011640">Изображения страницы</div>';
                    echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;margin-top:10px">';
                    foreach ($imgs as [$fkey, $flabel]) {
                        $val = page_field_get($fkey);
                        echo '<div style="border:1px solid #e6ebea;border-radius:10px;padding:12px">';
                        echo '<div style="font-weight:600;font-size:13px;margin-bottom:8px">' . e($flabel) . '</div>';
                        if ($val) {
                            echo '<img src="' . e(img_src($val)) . '" style="width:100%;height:110px;object-fit:cover;border-radius:8px;background:#f4f6f6" alt="">';
                        }
                        echo '<input type="text" name="f[' . e($fkey) . ']" value="' . e($val) . '" placeholder="assets/img/..." style="font-size:11px;margin-top:8px">';
                        echo '<div class="muted" style="font-size:11px;margin:6px 0 2px">Заменить файлом:</div><input type="file" name="fimg[' . e($fkey) . ']" accept="image/*" style="font-size:12px">';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                echo '</fieldset>';
                if ($hasRich) {
                    echo rich_editor_assets();
                }
            } else {
                echo '<div style="background:#fff8e6;border:1px solid #f0e0a0;border-radius:10px;padding:12px 16px;margin-top:16px" class="muted">Контент этой страницы ещё не подключён к редактированию — подключаем по мере готовности. SEO ниже уже работает.</div>';
            }
            echo '<fieldset style="border:1px solid #e6ebea;border-radius:10px;padding:14px 16px;margin-top:16px"><legend style="color:#011640;font-weight:700">SEO этой страницы</legend>';
            echo '<label>SEO Title</label><input type="text" name="seo_title" value="' . e($seo['title'] ?? '') . '">';
            echo '<label>Meta Description</label><textarea name="seo_desc">' . e($seo['description'] ?? '') . '</textarea>';
            echo '<div class="row"><div><label>Robots</label><input type="text" name="robots" value="' . e($seo['robots'] ?? 'index,follow') . '"></div>';
            echo '<div><label>Canonical</label><input type="text" name="canonical" value="' . e($seo['canonical'] ?? '') . '"></div></div></fieldset>';
            echo '<div style="margin-top:18px"><button class="btn">Сохранить страницу</button> <a class="btn ghost" href="index.php?section=pages">Назад к списку</a> <a class="btn ghost" href="' . e($url) . '" target="_blank">Открыть на сайте</a></div></form>';
        }
    } else {
        $rows = [];
        foreach ((array)$site['nav'] as $slug => $label) {
            $rows[] = ['slug' => (string)$slug, 'title' => (string)$label];
        }
        echo '<div class="panel"><div class="muted" style="margin-bottom:12px">Все (' . count($rows) . ') &nbsp;|&nbsp; Опубликованные (' . count($rows) . ')</div>';
        echo '<table><tr><th>Заголовок</th><th>URL</th><th>SEO</th><th></th></tr>';
        foreach ($rows as $r) {
            $pageKey = $r['slug'] === '' ? 'home' : $r['slug'];
            $url = $mkurl($r['slug']);
            $dot = trim((string)($site['seo']['pages'][$pageKey]['title'] ?? '')) !== '' ? '#011640' : '#cbd3d1';
            echo '<tr><td><b>' . e($r['title']) . '</b></td><td><span class="muted" style="background:#f0f3f2;padding:2px 8px;border-radius:6px">' . e($r['slug'] === '' ? '/' : '/' . $r['slug']) . '</span></td>';
            echo '<td><span style="display:inline-block;width:11px;height:11px;border-radius:50%;background:' . $dot . '"></span></td>';
            echo '<td class="right" style="white-space:nowrap"><a class="btn sm ghost" href="index.php?section=pages&edit=' . rawurlencode($pageKey) . '">Редактировать</a> <a class="btn sm ghost" href="' . e($url) . '" target="_blank">Открыть</a></td></tr>';
        }
        echo '</table></div>';
    }
}

elseif ($section === 'seo') {
    $og = kv_get('settings', 'og_image');
    $sv = kv_get('settings', 'search_visible', '1');
    $ne = kv_get('settings', 'notify_email', (string)($site['mail']['to'] ?? 'info@tce.az'));
    echo '<form method="post" enctype="multipart/form-data" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="global">';
    echo '<h3>Поисковая оптимизация</h3><p class="muted">Эти данные видят Google и соцсети при отправке ссылки.</p>';
    echo '<label>Картинка для соцсетей (OG)</label>';
    echo '<div style="display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap">';
    if ($og) {
        echo '<img src="' . e(img_src($og)) . '" style="width:160px;height:84px;object-fit:contain;background:#f4f6f6;border-radius:8px" alt="">';
    }
    echo '<div style="flex:1;min-width:240px"><input type="text" name="og_image" value="' . e($og) . '" placeholder="assets/img/...">';
    echo '<div class="muted" style="margin:6px 0 2px">Загрузить новый файл (заменит ссылку):</div><input type="file" name="og_file" accept="image/*">';
    echo '<p class="muted" style="margin-top:6px">Показывается, когда ссылку на сайт отправляют в мессенджере или соцсети. Лучше 1200×630 px.</p></div></div>';
    echo '<label>Глобальный meta description</label><textarea name="global_desc">' . e((string)($site['seo']['description'] ?? '')) . '</textarea>';
    echo '<label>Видимость в поиске</label><select name="search_visible">';
    echo '<option value="1"' . ($sv !== '0' ? ' selected' : '') . '>Открыт для поисковиков</option>';
    echo '<option value="0"' . ($sv === '0' ? ' selected' : '') . '>Закрыт (noindex)</option></select>';
    echo '<p class="muted">Закрывайте только на время работ — закрытый сайт выпадает из выдачи.</p>';
    echo '<label>Почта для заявок с формы</label><input type="text" name="notify_email" value="' . e($ne) . '">';
    echo '<p class="muted">Куда приходят сообщения из формы обратной связи.</p>';
    echo '<div style="margin-top:14px"><button class="btn">Сохранить</button></div></form>';

    $routes = ['home' => 'Ana səhifə'];
    foreach ((array)$site['nav'] as $slug => $label) {
        $routes[$slug === '' ? 'home' : (string)$slug] = (string)$label;
    }
    foreach ((array)$site['projects'] as $p) {
        $routes['layihe/' . $p['slug']] = 'Layihə: ' . (string)$p['title'];
    }
    echo '<form method="post" class="panel"><h3>SEO по страницам</h3>' . csrf_field() . '<input type="hidden" name="action" value="save">';
    foreach ($routes as $slug => $label) {
        $r = $site['seo']['pages'][$slug] ?? [];
        echo '<div style="border-bottom:1px solid #eef2f1;padding-bottom:14px;margin-bottom:14px"><b>' . e($label) . '</b> <span class="muted">/' . e($slug === 'home' ? '' : $slug) . '</span>';
        echo '<label>Title</label><input type="text" name="title[' . e($slug) . ']" value="' . e($r['title'] ?? '') . '">';
        echo '<label>Meta description</label><textarea name="descr[' . e($slug) . ']">' . e($r['description'] ?? '') . '</textarea></div>';
    }
    echo '<button class="btn">Сохранить</button></form>';
}

elseif ($section === 'submissions') {
    $rows = submissions_all();
    $ne = kv_get('settings', 'notify_email', (string)($site['mail']['to'] ?? ''));
    echo '<div class="panel"><h3>Сообщения из формы обратной связи</h3>';
    echo '<p class="muted">Всего: ' . count($rows) . ($ne !== '' ? '. Заявки также дублируются на почту <b>' . e($ne) . '</b>.' : '') . '</p>';
    echo '<form method="post" style="display:inline">' . csrf_field() . '<input type="hidden" name="action" value="read_all"><button class="btn ghost">Отметить все прочитанными</button></form>';
    echo '</div>';
    if (!$rows) {
        echo '<div class="panel"><p class="muted">Пока заявок нет.</p></div>';
    }
    foreach ($rows as $r) {
        $new = ($r['status'] ?? 'new') !== 'read';
        $created = (string)($r['created_at'] ?? '');
        $ts = strtotime($created);
        echo '<div class="panel" style="' . ($new ? 'border-left:4px solid #011640;' : '') . 'display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap">';
        echo '<div style="flex:1;min-width:260px">';
        echo '<div style="font-weight:800;font-size:16px">' . e(($r['name'] ?? '') !== '' ? (string)$r['name'] : '(без имени)')
            . ($new ? ' <span style="font-size:11px;background:#dbe6f5;color:#011640;padding:2px 10px;border-radius:20px;vertical-align:middle">новая</span>' : '') . '</div>';
        echo '<div class="muted" style="margin:4px 0 10px">' . e($ts ? date('d.m.Y H:i', $ts) : $created) . (($r['ip'] ?? '') !== '' && ($r['ip'] ?? '') !== '-' ? ' · IP ' . e((string)$r['ip']) : '') . '</div>';
        if (($r['phone'] ?? '') !== '') {
            echo '<div><b>Телефон:</b> ' . e((string)$r['phone']) . '</div>';
        }
        if (($r['email'] ?? '') !== '') {
            echo '<div><b>E-mail:</b> ' . e((string)$r['email']) . '</div>';
        }
        if (trim((string)($r['message'] ?? '')) !== '') {
            echo '<div style="background:#f4f6f6;border-radius:8px;padding:10px 14px;margin-top:10px">' . nl2br(e((string)$r['message'])) . '</div>';
        }
        echo '</div><div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end">';
        if ($new) {
            echo '<form method="post">' . csrf_field() . '<input type="hidden" name="action" value="read"><input type="hidden" name="id" value="' . e((string)($r['id'] ?? '')) . '"><button class="btn sm ghost">Прочитано</button></form>';
        }
        echo '<form method="post" onsubmit="return confirm(\'Удалить заявку?\')">' . csrf_field() . '<input type="hidden" name="action" value="del"><input type="hidden" name="id" value="' . e((string)($r['id'] ?? '')) . '"><button class="btn sm red">Удалить</button></form>';
        echo '</div></div>';
    }
}

elseif ($section === 'images') {
    $root = dirname(__DIR__);
    $updir = $root . '/assets/img';
    echo '<form method="post" enctype="multipart/form-data" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="upload">';
    echo '<label>Загрузить изображение или видео</label><input type="file" name="file" accept="image/*,video/*" required>';
    echo '<div style="margin-top:14px"><button class="btn">Загрузить</button></div><p class="muted">jpg/png/webp/gif/svg/mp4/webm. Файлы кладутся в assets/img/uploads/. Клик по пути — выделить и скопировать.</p></form>';

    $vidext = ['mp4', 'webm', 'ogg', 'mov'];
    $files = [];
    if (is_dir($updir)) {
        try {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($updir, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if (!$f->isFile()) {
                    continue;
                }
                $ext = strtolower($f->getExtension());
                if (!in_array($ext, array_merge(['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], $vidext), true)) {
                    continue;
                }
                $files[] = ['path' => $f->getPathname(), 'mtime' => $f->getMTime(), 'video' => in_array($ext, $vidext, true)];
            }
        } catch (Throwable $e) {
        }
    }
    usort($files, static fn(array $a, array $b): int => $b['mtime'] <=> $a['mtime']);
    $files = array_slice($files, 0, 200);

    echo '<div class="panel"><h3>Медиа сайта (' . count($files) . ')</h3>';
    echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px">';
    foreach ($files as $m) {
        $rel = ltrim(str_replace('\\', '/', substr((string)$m['path'], strlen($root))), '/');
        $src = url(implode('/', array_map('rawurlencode', explode('/', $rel))));
        echo '<div style="border:1px solid #eef2f1;border-radius:8px;padding:8px;text-align:center">';
        if ($m['video']) {
            echo '<video src="' . e($src) . '#t=0.1" preload="metadata" style="width:100%;height:92px;object-fit:cover;border-radius:6px;background:#000"></video>';
        } else {
            echo '<img src="' . e($src) . '" loading="lazy" style="width:100%;height:92px;object-fit:contain;background:#f4f6f6;border-radius:6px" alt="">';
        }
        echo '<input type="text" readonly value="' . e($rel) . '" onclick="this.select();document.execCommand(\'copy\')" title="Кликни, чтобы скопировать" style="width:100%;font-size:10px;margin-top:6px;border:1px solid #cfd8d6;border-radius:5px;padding:4px;cursor:pointer">';
        echo '</div>';
    }
    if (!$files) {
        echo '<p class="muted">Медиа не найдено.</p>';
    }
    echo '</div></div>';
}

elseif ($section === 'security') {
    $siteKey = kv_get('settings', 'turnstile_site');
    $hasSecret = kv_get('settings', 'turnstile_secret') !== '';
    $on = ($siteKey !== '' && $hasSecret);
    echo '<form method="post" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="save">';
    echo '<h3>Cloudflare Turnstile (капча) <span style="font-size:12px;background:' . ($on ? '#011640' : '#c9ced0') . ';color:#fff;padding:3px 10px;border-radius:20px;vertical-align:middle">' . ($on ? 'включена' : 'выключена') . '</span></h3>';
    echo '<p class="muted">Защищает форму на сайте и вход в панель от ботов.</p>';
    echo '<label>Site Key (публичный ключ)</label><input type="text" name="turnstile_site" value="' . e($siteKey) . '">';
    echo '<label>Secret Key (секретный ключ)</label><input type="password" name="turnstile_secret" placeholder="' . ($hasSecret ? '•••••••• (оставь пустым — не менять)' : '') . '">';
    echo '<div style="margin-top:16px"><button class="btn">Сохранить</button></div></form>';
    echo '<div class="panel"><h3>Где взять ключи</h3><ol class="muted"><li>Зайдите в <b>dash.cloudflare.com</b> → раздел <b>Turnstile</b>.</li><li>Нажмите <b>Add widget</b>.</li><li>Domain — укажите <b>tce.az</b>.</li><li>Widget Mode — <b>Managed</b>.</li><li>Скопируйте Site Key и Secret Key в поля выше.</li></ol></div>';
    echo '<div class="panel"><h3>Что уже защищено</h3><ul class="muted"><li>Пароли хранятся в виде необратимого хэша.</li><li>Все формы защищены от CSRF.</li><li>Проверка типа файлов при загрузке.</li><li>Защита от подстановки заголовков в письмах.</li><li>Блокировка входа после 10 неудачных попыток.</li></ul></div>';
}

elseif ($section === 'profile') {
    $a = current_admin();
    echo '<form method="post" class="panel" style="max-width:520px">' . csrf_field() . '<input type="hidden" name="action" value="save"><h3>Мой профиль</h3>';
    echo '<label>Имя</label><input type="text" name="name" value="' . e($a['name'] ?? '') . '">';
    echo '<label>E-mail</label><input type="email" name="email" value="' . e($a['email'] ?? '') . '">';
    echo '<div class="muted" style="margin-top:8px">Роль: <b>' . (($a['role'] ?? '') === 'admin' ? 'Администратор' : 'Редактор') . '</b></div>';
    echo '<div style="margin-top:16px"><button class="btn">Сохранить</button></div></form>';
    echo '<form method="post" class="panel" style="max-width:520px">' . csrf_field() . '<input type="hidden" name="action" value="passwd"><h3>Сменить пароль</h3>';
    echo '<label>Текущий пароль</label><input type="password" name="old" required>';
    echo '<label>Новый пароль</label><input type="password" name="new" required minlength="8">';
    echo '<label>Повторите новый</label><input type="password" name="new2" required minlength="8">';
    echo '<div style="margin-top:16px"><button class="btn">Изменить пароль</button></div></form>';
}

elseif ($section === 'smtp') {
    $g = static fn(string $k): string => e(kv_get('settings', $k));
    $mode = kv_get('settings', 'smtp_host') !== '' ? 'SMTP' : 'стандартная функция mail()';
    echo '<form method="post" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="save">';
    echo '<h3>Настройки SMTP <span class="muted" style="font-size:13px;font-weight:400">— способ отправки сейчас: <b>' . e($mode) . '</b></span></h3>';
    echo '<div class="row3"><div><label>SMTP-сервер</label><input type="text" name="smtp_host" value="' . $g('smtp_host') . '" placeholder="mail.tce.az"></div>';
    echo '<div><label>Порт</label><input type="text" name="smtp_port" value="' . $g('smtp_port') . '"></div>';
    echo '<div><label>Шифрование</label><select name="smtp_secure">';
    foreach (['tls' => 'STARTTLS (587)', 'ssl' => 'SSL/TLS (465)', 'none' => 'Без шифрования'] as $v => $l) {
        echo '<option value="' . $v . '"' . (kv_get('settings', 'smtp_secure') === $v ? ' selected' : '') . '>' . $l . '</option>';
    }
    echo '</select></div></div>';
    echo '<div class="row"><div><label>Пользователь (обычно полный адрес почты)</label><input type="text" name="smtp_user" value="' . $g('smtp_user') . '"></div>';
    echo '<div><label>Пароль</label><input type="password" name="smtp_pass" placeholder="' . (kv_get('settings', 'smtp_pass') !== '' ? '•••••• (оставь пустым — не менять)' : '') . '"></div></div>';
    echo '<div class="row"><div><label>Адрес отправителя</label><input type="text" name="smtp_from" value="' . $g('smtp_from') . '"></div>';
    echo '<div><label>Имя отправителя</label><input type="text" name="smtp_from_name" value="' . $g('smtp_from_name') . '"></div></div>';
    echo '<div style="margin-top:16px"><button class="btn">Сохранить</button></div></form>';
    echo '<form method="post" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="test"><h3>Проверка отправки</h3><p class="muted">Отправим тестовое письмо, чтобы убедиться, что настройки верные.</p>';
    echo '<div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap"><div style="flex:1;min-width:220px"><label>Адрес получателя</label><input type="email" name="test_to" value="' . e(current_admin()['email'] ?? '') . '" required></div><button class="btn ghost">Отправить тест</button></div></form>';
    echo '<div class="panel"><h3>Где взять данные</h3><p class="muted">В cPanel → <b>Email Accounts</b> → у нужного ящика нажмите <b>Connect Devices</b>. Там сервер, порт и способ шифрования. Пользователь — полный адрес почты, пароль — от этого ящика.</p></div>';
}

elseif ($section === 'users') {
    $me = (int)current_admin()['id'];
    echo '<form method="post" class="panel">' . csrf_field() . '<input type="hidden" name="action" value="add"><h3>Добавить пользователя</h3>';
    echo '<p class="muted">Роль «Администратор» даёт доступ к управлению пользователями. «Редактор» — только контент.</p>';
    echo '<div class="row"><div><label>Имя</label><input type="text" name="name"></div><div><label>E-mail</label><input type="email" name="email" required></div></div>';
    echo '<div class="row"><div><label>Пароль (мин. 8 символов)</label><input type="text" name="password" required minlength="8"></div>';
    echo '<div><label>Роль</label><select name="role"><option value="editor">Редактор</option><option value="admin">Администратор</option></select></div></div>';
    echo '<div style="margin-top:16px"><button class="btn">Добавить</button></div></form>';

    $rows = admin_users()['users'];
    echo '<form method="post" class="panel"><h3>Все пользователи</h3><p class="muted">Поле пароля оставьте пустым, если менять его не нужно.</p>' . csrf_field() . '<input type="hidden" name="action" value="save">';
    echo '<table><tr><th>Имя</th><th>E-mail</th><th>Роль</th><th>Новый пароль</th><th>Активен</th><th>Вход</th><th></th></tr>';
    foreach ($rows as $r) {
        $self = (int)$r['id'] === $me;
        echo '<tr><td><input type="text" name="u[' . $r['id'] . '][name]" value="' . e($r['name'] ?? '') . '" style="width:130px"></td>';
        echo '<td>' . e($r['email']) . ($self ? ' <span style="font-size:11px;background:#dbe6f5;color:#0a2a5c;padding:2px 8px;border-radius:20px">это вы</span>' : '') . '</td>';
        echo '<td><select name="u[' . $r['id'] . '][role]"' . ($self ? ' disabled' : '') . '><option value="admin"' . (($r['role'] ?? 'admin') === 'admin' ? ' selected' : '') . '>Администратор</option><option value="editor"' . (($r['role'] ?? '') === 'editor' ? ' selected' : '') . '>Редактор</option></select></td>';
        echo '<td><input type="text" name="u[' . $r['id'] . '][password]" placeholder="—" style="width:120px"></td>';
        echo '<td style="text-align:center"><input type="checkbox" name="u[' . $r['id'] . '][active]" ' . ((int)($r['active'] ?? 1) ? 'checked' : '') . ($self ? ' disabled' : '') . '></td>';
        echo '<td class="muted">' . e($r['last_login'] ?? '—') . '</td>';
        echo '<td class="right">' . ($self ? '' : '<button class="btn sm red" name="delete_id" value="' . $r['id'] . '" formnovalidate onclick="return confirm(\'Удалить пользователя?\')">Удалить</button>') . '</td></tr>';
    }
    echo '</table><div style="margin-top:16px"><button class="btn">Сохранить изменения</button></div></form>';
}

layout_bottom();

/* ---- раздел «Проекты»: список + редактор ---- */
function render_projects(): void
{
    global $site;
    $mode = isset($_GET['edit']) ? 'edit' : (isset($_GET['new']) ? 'new' : 'list');

    if ($mode === 'list') {
        $rows = (array)$site['projects'];
        $pub = count(array_filter($rows, static fn(array $r): bool => ($r['status'] ?? 'published') === 'published'));
        echo '<div class="panel"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;gap:10px;flex-wrap:wrap">';
        echo '<div class="muted">Все (' . count($rows) . ') &nbsp;|&nbsp; Опубликованные (' . $pub . ')</div>';
        echo '<a class="btn" href="index.php?section=projects&new=1">+ Добавить проект</a></div>';
        echo '<table><tr><th>Заголовок</th><th>Фото</th><th>SEO</th><th></th></tr>';
        if (!$rows) {
            echo '<tr><td colspan="4" class="muted">Пока нет проектов.</td></tr>';
        }
        foreach ($rows as $r) {
            $cover = (string)($r['cover'] ?? '');
            $purl = url('layihe/' . (string)$r['slug']);
            $seoOk = trim((string)($site['seo']['pages']['layihe/' . $r['slug']]['title'] ?? '')) !== '' ? '#011640' : '#cbd3d1';
            $draft = ($r['status'] ?? 'published') !== 'published' || !(int)($r['visible'] ?? 1);
            echo '<tr><td><a href="index.php?section=projects&edit=' . rawurlencode((string)$r['slug']) . '" style="color:#011640;text-decoration:none"><b>' . e($r['title'])
                . '</b></a>' . ($draft ? ' <span style="font-size:11px;background:#f3e8c8;color:#7a5b00;padding:2px 8px;border-radius:20px">черновик</span>' : '') . '<br>'
                . '<a class="muted" href="' . e($purl) . '" target="_blank" style="font-size:12px;text-decoration:none">/layihe/' . e($r['slug']) . '</a></td>';
            echo '<td>' . ($cover ? '<a href="' . e($purl) . '" target="_blank"><img src="' . e(img_src($cover)) . '" style="width:74px;height:48px;object-fit:cover;border-radius:6px" alt=""></a>' : '—') . '</td>';
            echo '<td><span style="display:inline-block;width:11px;height:11px;border-radius:50%;background:' . $seoOk . '"></span></td>';
            echo '<td class="right" style="white-space:nowrap">';
            echo '<a class="btn sm ghost" href="index.php?section=projects&edit=' . rawurlencode((string)$r['slug']) . '">Редактировать</a> ';
            echo '<a class="btn sm ghost" href="' . e($purl) . '" target="_blank">Открыть</a> ';
            echo '<form method="post" style="display:inline" onsubmit="return confirm(\'Удалить проект?\')">' . csrf_field() . '<input type="hidden" name="action" value="del"><input type="hidden" name="key" value="' . e($r['slug']) . '"><button class="btn sm red">✕</button></form></td></tr>';
        }
        echo '</table></div>';
        return;
    }

    $p = ['slug' => '', 'title' => '', 'category' => '', 'year' => '', 'location' => '', 'area' => '', 'client' => '',
          'cover' => '', 'video' => '', 'gallery' => [], 'summary' => '', 'body' => '', 'scope' => [],
          'sort' => 0, 'visible' => 1, 'status' => 'published'];
    $key = '';
    if ($mode === 'edit') {
        foreach ((array)$site['projects'] as $pr) {
            if ((string)$pr['slug'] === (string)$_GET['edit']) {
                $p = array_merge($p, $pr);
                $key = (string)$pr['slug'];
                break;
            }
        }
        if ($key === '') {
            echo '<div class="panel">Проект не найден. <a href="index.php?section=projects">Назад</a></div>';
            return;
        }
    }
    $seo = $site['seo']['pages']['layihe/' . $key] ?? ['title' => '', 'description' => '', 'robots' => 'index,follow', 'canonical' => ''];
    $gallery = (array)$p['gallery'];
    $cover = (string)$p['cover'];

    echo '<form method="post" enctype="multipart/form-data" class="panel">' . csrf_field();
    echo '<input type="hidden" name="action" value="save"><input type="hidden" name="key" value="' . e($key) . '">';
    echo '<h3>' . ($mode === 'edit' ? 'Редактировать: ' . e($p['title']) : 'Новый проект') . '</h3>';
    if ($mode === 'edit' && $p['slug'] !== '') {
        echo '<p class="muted" style="margin:2px 0 12px">Постоянная ссылка: <a href="' . e(url('layihe/' . $p['slug'])) . '" target="_blank" style="color:#1b4b8f;font-weight:600">' . e(absolute_url('layihe/' . $p['slug'])) . '</a></p>';
    }
    echo '<div class="row"><div><label>Название</label><input type="text" id="projTitle" name="title" value="' . e($p['title']) . '" required></div>';
    echo '<div><label>Slug (URL)</label><input type="text" id="projSlug" name="slug" value="' . e($p['slug']) . '" placeholder="авто из названия"></div></div>';

    echo '<div class="row3"><div><label>Категория</label><input type="text" name="category" value="' . e($p['category']) . '"></div>';
    echo '<div><label>Год</label><input type="text" name="year" value="' . e($p['year']) . '"></div>';
    echo '<div><label>Локация</label><input type="text" name="location" value="' . e($p['location']) . '"></div></div>';
    echo '<div class="row"><div><label>Площадь</label><input type="text" name="area" value="' . e($p['area']) . '"></div>';
    echo '<div><label>Клиент</label><input type="text" name="client" value="' . e($p['client']) . '"></div></div>';

    echo '<label>Обложка, путь</label><input type="text" name="cover" value="' . e($cover) . '">';
    if ($cover) {
        echo '<div style="margin-top:8px"><img src="' . e(img_src($cover)) . '" style="max-width:200px;border-radius:8px" alt=""></div>';
    }
    echo '<label>Загрузить новую обложку</label><input type="file" name="cover_file" accept="image/*">';

    echo '<label style="margin-top:20px;color:#011640">Галерея проекта</label>';
    echo '<div id="gal" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px">';
    foreach ($gallery as $gp) {
        echo gal_item((string)$gp);
    }
    echo '</div>';
    echo '<button type="button" class="btn ghost" style="margin-top:10px" onclick="addGal()">Добавить ещё</button>';
    echo '<label>Загрузить новые изображения</label><input type="file" name="gallery_files[]" accept="image/*" multiple>';

    echo '<label style="margin-top:20px;color:#011640">Видео проекта (URL или загрузка)</label>';
    echo '<input type="text" name="video" value="' . e($p['video']) . '" placeholder="assets/img/uploads/... или https://...">';
    if (!empty($p['video'])) {
        echo '<div style="margin-top:8px"><video src="' . e(str_starts_with((string)$p['video'], 'http') ? $p['video'] : url((string)$p['video'])) . '" controls style="max-width:300px;border-radius:8px;background:#000"></video></div>';
    }
    echo '<label>Загрузить видео (mp4/webm)</label><input type="file" name="video_file" accept="video/*">';

    echo '<label style="margin-top:20px">Краткое описание</label><textarea name="descr">' . e($p['summary']) . '</textarea>';

    echo '<label>Контент проекта</label>';
    echo '<div class="rtbar"><button type="button" onmousedown="rt(event,\'bold\')"><b>B</b></button>';
    echo '<button type="button" onmousedown="rt(event,\'italic\')"><i>I</i></button>';
    echo '<button type="button" onmousedown="rt(event,\'underline\')"><u>U</u></button>';
    echo '<button type="button" onmousedown="rt(event,\'insertUnorderedList\')">• список</button>';
    echo '<button type="button" onmousedown="rt(event,\'insertOrderedList\')">1. список</button>';
    echo '<button type="button" onmousedown="rtLink(event)">Ссылка</button></div>';
    echo '<div id="rt" contenteditable="true" class="rtarea">' . clean_rich_text((string)$p['body']) . '</div>';
    echo '<textarea name="content" id="rtsrc" style="display:none">' . e($p['body']) . '</textarea>';

    echo '<label>Объём работ, каждый пункт с новой строки</label><textarea name="scope" style="min-height:110px">' . e(array_to_lines((array)$p['scope'])) . '</textarea>';

    echo '<h4 style="color:#011640;margin-top:22px">SEO этого проекта</h4>';
    echo '<label>SEO Title</label><input type="text" name="seo_title" value="' . e($seo['title'] ?? '') . '">';
    echo '<label>Meta Description</label><textarea name="seo_desc">' . e($seo['description'] ?? '') . '</textarea>';
    echo '<div class="row"><div><label>Robots</label><input type="text" name="robots" value="' . e($seo['robots'] ?? 'index,follow') . '"></div>';
    echo '<div><label>Canonical</label><input type="text" name="canonical" value="' . e($seo['canonical'] ?? '') . '"></div></div>';

    echo '<div class="row3" style="margin-top:8px"><div><label>Порядок</label><input type="number" name="sort" value="' . e((string)$p['sort']) . '"></div>';
    echo '<div><label>Статус</label><select name="status"><option value="published"' . ($p['status'] === 'published' ? ' selected' : '') . '>Опубликовано</option><option value="draft"' . ($p['status'] === 'draft' ? ' selected' : '') . '>Черновик</option></select></div>';
    echo '<div><label>&nbsp;</label><label style="font-weight:400"><input type="checkbox" name="visible" ' . (!empty($p['visible']) ? 'checked' : '') . '> Показывать</label></div></div>';

    echo '<div style="margin-top:20px"><button class="btn">Сохранить проект</button> ';
    echo '<a class="btn ghost" href="index.php?section=projects">Назад к списку</a></div>';
    echo '</form>';
    echo projects_js();
}

function gal_item(string $path): string
{
    return '<div class="gitem" style="border:1px solid #e6ebea;border-radius:10px;padding:8px;text-align:center">'
        . '<img src="' . e(img_src($path)) . '" style="width:100%;height:90px;object-fit:cover;border-radius:6px" alt="">'
        . '<input type="text" name="gallery[]" value="' . e($path) . '" style="font-size:12px;margin-top:6px">'
        . '<button type="button" class="btn sm red" style="margin-top:6px" onclick="this.parentNode.remove()">Удалить</button></div>';
}

function projects_js(): string
{
    return <<<'HTML'
<style>.row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}@media(max-width:700px){.row3{grid-template-columns:1fr}}
.rtbar{display:flex;gap:6px;flex-wrap:wrap;border:1px solid #cfd8d6;border-bottom:0;border-radius:9px 9px 0 0;padding:8px;background:#f7faf9}
.rtbar button{background:#fff;border:1px solid #d7dedc;border-radius:6px;padding:4px 10px;cursor:pointer}
.rtarea{border:1px solid #cfd8d6;border-radius:0 0 9px 9px;min-height:140px;padding:12px;background:#fff}
.gitem input{width:100%;border:1px solid #cfd8d6;border-radius:6px;padding:5px}</style>
<script>
var slugTouched = (document.getElementById('projSlug')||{value:''}).value !== '';
function jsSlug(s){var m={'ə':'e','Ə':'e','ı':'i','İ':'i','ö':'o','Ö':'o','ü':'u','Ü':'u','ç':'c','Ç':'c','ş':'s','Ş':'s','ğ':'g','Ğ':'g'};
  s=s.replace(/./g,function(ch){return m[ch]!==undefined?m[ch]:ch;});
  return s.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');}
var tEl=document.getElementById('projTitle'), sEl=document.getElementById('projSlug');
if(tEl&&sEl){ sEl.addEventListener('input',function(){slugTouched=this.value!=='';});
  tEl.addEventListener('input',function(){ if(!slugTouched) sEl.value=jsSlug(this.value); }); }
function rt(e,cmd){e.preventDefault();document.execCommand(cmd,false,null);document.getElementById('rt').focus();}
function rtLink(e){e.preventDefault();var u=prompt('URL ссылки:','https://');if(u)document.execCommand('createLink',false,u);}
function syncRT(){document.getElementById('rtsrc').value=document.getElementById('rt').innerHTML;}
document.querySelector('form[enctype]').addEventListener('submit',syncRT);
function addGal(){var d=document.createElement('div');d.className='gitem';d.style.cssText='border:1px solid #e6ebea;border-radius:10px;padding:8px;text-align:center';
d.innerHTML='<input type="text" name="gallery[]" placeholder="assets/img/..." style="width:100%;border:1px solid #cfd8d6;border-radius:6px;padding:6px"><button type="button" class="btn sm red" style="margin-top:6px" onclick="this.parentNode.remove()">Удалить</button>';
document.getElementById('gal').appendChild(d);}
</script>
HTML;
}

/* ---- переиспользуемый rich-text редактор (contenteditable + тулбар) ---- */
function rich_field(string $name, string $value, string $label): void
{
    $btns = [['bold', '<b>B</b>'], ['italic', '<i>I</i>'], ['underline', '<u>U</u>'],
             ['insertUnorderedList', '&bull; список'], ['insertOrderedList', '1. список'],
             ['formatBlock:blockquote', '&laquo;&nbsp;&raquo;'], ['createLink', 'Ссылка'], ['removeFormat', 'T&times;']];
    $bar = '';
    foreach ($btns as $b) {
        $bar .= '<button type="button" data-cmd="' . $b[0] . '">' . $b[1] . '</button>';
    }
    $l = e($label);
    $n = e($name);
    $clean = clean_rich_text($value);
    $tv = e($value);
    echo "<label>$l</label><div class=\"rte-wrap\"><div class=\"rte-bar\">$bar</div>"
       . "<div class=\"rte\" contenteditable=\"true\">$clean</div>"
       . "<textarea name=\"$n\" style=\"display:none\">$tv</textarea></div>";
}

function rich_editor_assets(): string
{
    return <<<'HTML'
<style>
.rte-wrap{margin-bottom:14px}
.rte-bar{display:flex;gap:6px;flex-wrap:wrap;border:1px solid #cfd8d6;border-bottom:0;border-radius:9px 9px 0 0;padding:8px;background:#f7faf9}
.rte-bar button{background:#fff;border:1px solid #d7dedc;border-radius:6px;padding:4px 10px;cursor:pointer;font-size:14px}
.rte{border:1px solid #cfd8d6;border-radius:0 0 9px 9px;min-height:130px;padding:12px;background:#fff;line-height:1.5}
.rte:focus{outline:2px solid #011640;outline-offset:-2px}
.rte p{margin:0 0 10px}
</style>
<script>
document.addEventListener('mousedown', function (e) {
  var b = e.target.closest('.rte-bar button'); if (!b) return;
  e.preventDefault();
  var cmd = b.getAttribute('data-cmd');
  if (cmd === 'createLink') { var u = prompt('URL ссылки:', 'https://'); if (u) document.execCommand('createLink', false, u); }
  else if (cmd.indexOf('formatBlock:') === 0) { document.execCommand('formatBlock', false, cmd.split(':')[1]); }
  else document.execCommand(cmd, false, null);
});
document.addEventListener('submit', function (e) {
  if (!e.target.querySelectorAll) return;
  e.target.querySelectorAll('.rte').forEach(function (ed) {
    var ta = ed.parentNode.querySelector('textarea'); if (ta) ta.value = ed.innerHTML;
  });
}, true);
</script>
HTML;
}
