<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$slug = $GLOBALS['argast_static_slug'] ?? null;
if (!is_string($slug) || !in_array($slug, ['privacy', 'terms', 'refund'], true)) {
    $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $bp = rtrim((string) ($config['base_path'] ?? ''), '/');
    if ($bp !== '' && str_starts_with($rawPath, $bp)) {
        $rawPath = substr($rawPath, strlen($bp)) ?: '/';
    }
    $segs = array_values(array_filter(explode('/', trim($rawPath, '/'))));
    $s0 = $segs[0] ?? 'privacy';
    if (str_ends_with(strtolower($s0), '.php')) {
        $s0 = substr($s0, 0, -4);
    }
    $slug = in_array($s0, ['privacy', 'terms', 'refund'], true) ? $s0 : 'privacy';
}

$row = null;
try {
    $st = db_site()->prepare('SELECT body_ru, body_en FROM static_pages WHERE slug = ? LIMIT 1');
    $st->execute([$slug]);
    $row = $st->fetch();
} catch (\Throwable) {
}

$lang = current_lang();
$body = $row ? (string) ($lang === 'en' ? ($row['body_en'] ?? '') : ($row['body_ru'] ?? '')) : '';
$pageTitle = ($slug === 'privacy' ? t('footer_privacy') : ($slug === 'terms' ? t('footer_terms') : t('footer_refund'))) . ' — ' . setting('meta_title');

require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="w-full max-w-4xl prose prose-invert prose-headings:font-beaufort">
    <div class="bg-black/30 border border-white/10 rounded-2xl p-8 text-gray-200"><?= $body ?></div>
</div>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
