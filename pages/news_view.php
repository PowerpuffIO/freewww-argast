<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$slug = (string) ($_GET['slug'] ?? '');
if ($slug === '') {
    redirect('/news');
}
$lang = current_lang();
$titleKey = $lang === 'en' ? 'title_en' : 'title_ru';
$bodyKey = $lang === 'en' ? 'body_en' : 'body_ru';
$row = null;
try {
    $st = db_site()->prepare('SELECT slug, title_ru, title_en, body_ru, body_en, image_path, published_at FROM news WHERE slug = ? AND published_at IS NOT NULL LIMIT 1');
    $st->execute([$slug]);
    $row = $st->fetch();
} catch (\Throwable) {
}
if (!$row) {
    http_response_code(404);
    echo current_lang() === 'en' ? 'Not found' : 'Не найдено';
    exit;
}
$t = (string) ($row[$titleKey] ?? $row['title_ru']);
$b = (string) ($row[$bodyKey] ?? $row['body_ru'] ?? '');
$pageTitle = $t . ' — ' . setting('meta_title');
$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$im = $row['image_path'] !== '' ? ($pref . '/' . ltrim((string) $row['image_path'], '/')) : '';
require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="w-full max-w-4xl">
    <a href="<?= h(url_path('/news')) ?>" class="text-blue-400 text-sm hover:underline mb-6 inline-block">← <?= h(t('news_page_title')) ?></a>
    <h1 class="text-4xl font-beaufort mb-6" style="color: #F5E6D3;"><?= h($t) ?></h1>
    <?php if ($im !== ''): ?>
    <img src="<?= h($im) ?>" alt="" class="w-full rounded-2xl mb-8 max-h-[480px] object-cover" loading="lazy">
    <?php endif; ?>
    <div class="text-gray-200 leading-relaxed space-y-4"><?= $b ?></div>
</div>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
