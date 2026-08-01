<?php
/**
 * Импорт контента из WordPress-экспорта (WXR) в структуру этого сайта.
 *
 * Запуск:
 *   php tools/import-wp.php export.xml
 *   php tools/import-wp.php export.xml --media          (ещё и скачать картинки)
 *   php tools/import-wp.php export.xml --media --limit=50
 *
 * Что делает:
 *   1. читает XML-файл из «Инструменты → Экспорт → Всё содержимое»;
 *   2. достаёт текст из post_content И из _elementor_data (JSON Elementor);
 *   3. собирает список всех картинок;
 *   4. пишет config/content.imported.php и storage/import-report.txt;
 *   5. по флагу --media скачивает картинки в assets/img/imported/.
 *
 * Ничего не перезаписывает: config/site.php остаётся нетронутым,
 * результат кладётся в отдельный файл — сравните и перенесите нужное.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("Скрипт запускается только из командной строки.\n");
}

$root = dirname(__DIR__);

// ── Аргументы ───────────────────────────────────────────────────────────────
$file       = null;
$withMedia  = false;
$mediaLimit = 0;

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--media') {
        $withMedia = true;
    } elseif (str_starts_with($arg, '--limit=')) {
        $mediaLimit = (int)substr($arg, 8);
    } elseif ($file === null) {
        $file = $arg;
    }
}

if ($file === null || !is_file($file)) {
    exit("Укажите путь к XML-экспорту.\nПример: php tools/import-wp.php export.xml --media\n");
}

echo "Читаю: {$file}\n";

// ── Разбор WXR ──────────────────────────────────────────────────────────────
libxml_use_internal_errors(true);
$xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE);

if ($xml === false) {
    $errors = array_map(static fn($e) => trim($e->message), libxml_get_errors());
    exit("Не удалось разобрать XML:\n  " . implode("\n  ", array_slice($errors, 0, 5)) . "\n");
}

$ns = $xml->getNamespaces(true);
$wp      = $ns['wp']      ?? 'http://wordpress.org/export/1.2/';
$content = $ns['content'] ?? 'http://purl.org/rss/1.0/modules/content/';
$excerpt = $ns['excerpt'] ?? 'http://wordpress.org/export/1.2/excerpt/';

$siteUrl = (string)($xml->channel->children($wp)->base_site_url ?? '');
if ($siteUrl === '') {
    $siteUrl = (string)($xml->channel->link ?? '');
}
echo "Сайт-источник: " . ($siteUrl ?: '(не определён)') . "\n\n";

// ── Ключи Elementor, в которых лежит видимый текст ──────────────────────────
const TEXT_KEYS = [
    'title', 'editor', 'text', 'description', 'caption', 'heading',
    'title_text', 'description_text', 'button_text', 'subtitle',
    'testimonial_content', 'testimonial_name', 'inner_text',
    'tab_title', 'tab_content', 'item_title', 'item_description',
    'html', 'address', 'phone', 'email', 'counter_title', 'prefix', 'suffix',
];

const SKIP_KEY_PATTERNS = [
    'color', 'size', 'width', 'height', 'align', 'margin', 'padding',
    'typography', 'font', 'border', 'shadow', 'background_c', 'animation',
    'gap', 'space', 'radius', 'opacity', 'transform', 'filter', 'position',
    'css', 'class', 'id_', '_id', 'motion', 'sticky', 'zindex', 'z_index',
];

function key_is_skippable(string $key): bool
{
    $key = strtolower($key);
    foreach (SKIP_KEY_PATTERNS as $pattern) {
        if (str_contains($key, $pattern)) {
            return true;
        }
    }
    return false;
}

/** Рекурсивный обход JSON Elementor: собирает тексты и картинки. */
function walk_elementor(mixed $node, array &$texts, array &$images, string $key = ''): void
{
    if (is_string($node)) {
        if ($key === '' || key_is_skippable($key)) {
            return;
        }
        $isTextKey = in_array(strtolower($key), TEXT_KEYS, true)
                  || str_ends_with($key, '_text')
                  || str_ends_with($key, '_title');

        if (!$isTextKey) {
            return;
        }

        $clean = trim(html_entity_decode(strip_tags($node), ENT_QUOTES, 'UTF-8'));
        $clean = preg_replace('/\s+/u', ' ', $clean) ?? $clean;

        if ($clean !== '' && !preg_match('~^(https?://|#|\d+(px|%|em)?$)~i', $clean)) {
            $texts[] = $clean;
        }
        return;
    }

    if (!is_array($node)) {
        return;
    }

    // Картинка Elementor: ['id' => 123, 'url' => '...']
    if (isset($node['url']) && is_string($node['url'])
        && preg_match('~\.(jpe?g|png|webp|svg|gif|avif)(\?|$)~i', $node['url'])) {
        $images[] = $node['url'];
    }

    foreach ($node as $childKey => $child) {
        walk_elementor($child, $texts, $images, is_string($childKey) ? $childKey : $key);
    }
}

/** Вытаскивает src всех <img> из HTML. */
function images_from_html(string $html): array
{
    preg_match_all('~<img[^>]+src=["\']([^"\']+)["\']~i', $html, $m);
    $found = $m[1] ?? [];

    // srcset тоже
    preg_match_all('~srcset=["\']([^"\']+)["\']~i', $html, $m2);
    foreach ($m2[1] ?? [] as $set) {
        foreach (explode(',', $set) as $candidate) {
            $url = trim(explode(' ', trim($candidate))[0]);
            if ($url !== '') {
                $found[] = $url;
            }
        }
    }
    return $found;
}

/** HTML → читаемый текст с сохранением абзацев. */
function html_to_text(string $html): string
{
    $html = preg_replace('~<(script|style)[^>]*>.*?</\1>~is', '', $html) ?? $html;
    $html = preg_replace('~</(p|div|h[1-6]|li|br)\s*>~i', "\n", $html) ?? $html;
    $html = preg_replace('~<br\s*/?>~i', "\n", $html) ?? $html;
    $text = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');
    $text = preg_replace('~[ \t]+~', ' ', $text) ?? $text;
    $text = preg_replace('~\n{3,}~', "\n\n", $text) ?? $text;
    return trim($text);
}

// ── Обход записей ───────────────────────────────────────────────────────────
$pages       = [];
$posts       = [];
$attachments = [];
$allImages   = [];
$typeCount   = [];

foreach ($xml->channel->item as $item) {
    $w = $item->children($wp);
    $c = $item->children($content);
    $x = $item->children($excerpt);

    $type   = (string)$w->post_type;
    $status = (string)$w->status;

    $typeCount[$type] = ($typeCount[$type] ?? 0) + 1;

    if ($type === 'attachment') {
        $url = (string)$w->attachment_url;
        if ($url !== '') {
            $attachments[] = [
                'title' => (string)$item->title,
                'url'   => $url,
            ];
            $allImages[] = $url;
        }
        continue;
    }

    if ($status === 'trash' || $status === 'auto-draft') {
        continue;
    }
    if (!in_array($type, ['page', 'post', 'project', 'portfolio', 'service'], true)) {
        continue;
    }

    $rawHtml = (string)$c->encoded;

    // Мета-поля, в том числе _elementor_data
    $elementorTexts  = [];
    $elementorImages = [];

    foreach ($w->postmeta as $meta) {
        $metaKey = (string)$meta->meta_key;
        if ($metaKey !== '_elementor_data') {
            continue;
        }
        $json = json_decode((string)$meta->meta_value, true);
        if (is_array($json)) {
            walk_elementor($json, $elementorTexts, $elementorImages);
        }
    }

    $bodyText = html_to_text($rawHtml);

    // Если страница собрана в Elementor, post_content обычно пуст
    if ($bodyText === '' && $elementorTexts) {
        $bodyText = implode("\n\n", array_values(array_unique($elementorTexts)));
    }

    $images = array_values(array_unique(array_merge(
        images_from_html($rawHtml),
        $elementorImages
    )));
    $allImages = array_merge($allImages, $images);

    $entry = [
        'title'   => trim((string)$item->title),
        'slug'    => (string)$w->post_name,
        'date'    => substr((string)$w->post_date, 0, 10),
        'status'  => $status,
        'excerpt' => html_to_text((string)$x->encoded),
        'text'    => $bodyText,
        'images'  => $images,
        'source'  => (string)$item->link,
    ];

    if ($type === 'page') {
        $pages[] = $entry;
    } else {
        $posts[] = $entry;
    }
}

$allImages = array_values(array_unique(array_filter($allImages)));

echo "Найдено:\n";
foreach ($typeCount as $type => $n) {
    printf("  %-14s %d\n", $type, $n);
}
printf("\n  страниц:   %d\n  записей:   %d\n  картинок:  %d\n\n",
    count($pages), count($posts), count($allImages));

// ── Запись результата ───────────────────────────────────────────────────────
$export = [
    'source'      => $siteUrl,
    'imported_at' => date('c'),
    'pages'       => $pages,
    'posts'       => $posts,
    'images'      => $allImages,
];

$out = "<?php\n"
     . "/**\n * Импортировано из WordPress " . date('d.m.Y H:i') . "\n"
     . " * Перенесите нужные куски в config/site.php вручную.\n */\n\n"
     . "return " . var_export($export, true) . ";\n";

$targetDir = $root . '/config';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0775, true);
}
file_put_contents($targetDir . '/content.imported.php', $out);
echo "Записано: config/content.imported.php\n";

// Человекочитаемый отчёт
$report = "Импорт из {$siteUrl}\n" . str_repeat('=', 60) . "\n\n";
foreach (array_merge($pages, $posts) as $entry) {
    $report .= "### {$entry['title']}  [/{$entry['slug']}]\n";
    $report .= "Источник: {$entry['source']}\n";
    $report .= "Картинок: " . count($entry['images']) . "\n\n";
    $report .= $entry['text'] . "\n\n" . str_repeat('-', 60) . "\n\n";
}

$storage = $root . '/storage';
if (!is_dir($storage)) {
    mkdir($storage, 0775, true);
}
file_put_contents($storage . '/import-report.txt', $report);
echo "Записано: storage/import-report.txt  (весь текст в читаемом виде)\n";

// ── Скачивание картинок ─────────────────────────────────────────────────────
if (!$withMedia) {
    echo "\nЧтобы скачать картинки, добавьте флаг --media\n";
    exit(0);
}

$mediaDir = $root . '/assets/img/imported';
if (!is_dir($mediaDir)) {
    mkdir($mediaDir, 0775, true);
}

$queue = $mediaLimit > 0 ? array_slice($allImages, 0, $mediaLimit) : $allImages;
echo "\nСкачиваю " . count($queue) . " файлов в assets/img/imported/\n";

$ok = $skip = $fail = 0;
$map = [];

foreach ($queue as $i => $url) {
    // Убираем размерные суффиксы WordPress: image-300x200.jpg → image.jpg
    $path = parse_url($url, PHP_URL_PATH) ?? '';
    $name = basename($path);
    $full = preg_replace('~-\d{2,4}x\d{2,4}(\.[a-z]{3,4})$~i', '$1', $name) ?? $name;

    $dest = $mediaDir . '/' . $full;
    $map[$url] = 'assets/img/imported/' . $full;

    if (is_file($dest) && filesize($dest) > 0) {
        $skip++;
        continue;
    }

    $ctx = stream_context_create([
        'http' => [
            'timeout'    => 20,
            'user_agent' => 'Mozilla/5.0 (migration script)',
            'follow_location' => 1,
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);

    $data = @file_get_contents($url, false, $ctx);

    if ($data === false || strlen($data) < 100) {
        $fail++;
        fwrite(STDERR, "  не скачалось: {$url}\n");
        continue;
    }

    file_put_contents($dest, $data);
    $ok++;

    if (($i + 1) % 20 === 0) {
        echo "  ... {$ok} готово\n";
    }
}

file_put_contents(
    $storage . '/media-map.json',
    json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

echo "\nГотово: скачано {$ok}, пропущено {$skip}, ошибок {$fail}\n";
echo "Соответствие старых и новых путей: storage/media-map.json\n";
