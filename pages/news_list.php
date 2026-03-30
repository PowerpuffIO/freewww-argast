<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$lang = current_lang();
$titleKey = $lang === 'en' ? 'title_en' : 'title_ru';
$exKey = $lang === 'en' ? 'excerpt_en' : 'excerpt_ru';
$list = [];
try {
    $st = db_site()->query('SELECT slug, title_ru, title_en, excerpt_ru, excerpt_en, image_path, published_at FROM news WHERE published_at IS NOT NULL ORDER BY published_at DESC');
    $list = $st->fetchAll() ?: [];
} catch (\Throwable) {
}
$pageTitle = t('news_page_title') . ' — ' . setting('meta_title');
$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="w-full max-w-5xl">
    <h1 class="text-4xl font-beaufort mb-10" style="color: #F5E6D3;"><?= h(t('news_page_title')) ?></h1>
    <?php if (!$list): ?>
    <p class="text-gray-400"><?= h(t('news_empty')) ?></p>
    <?php else: ?>
    <div class="grid gap-6 md:grid-cols-2">
        <?php foreach ($list as $n): ?>
        <?php
        $t = (string) ($n[$titleKey] ?? $n['title_ru']);
        $ex = (string) ($n[$exKey] ?? $n['excerpt_ru'] ?? '');
        $im = $n['image_path'] !== '' ? ($pref . '/' . ltrim((string) $n['image_path'], '/')) : ($pref . '/themes/argast/Images/argast_mini.png');
        ?>
        <article class="rounded-2xl overflow-hidden border border-white/10 bg-black/30 flex flex-col">
            <a href="<?= h(url_path('/news/' . rawurlencode((string) $n['slug']))) ?>" class="block aspect-video overflow-hidden">
                <img src="<?= h($im) ?>" alt="" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
            </a>
            <div class="p-5 flex flex-col flex-grow">
                <h2 class="text-xl font-beaufort mb-2" style="color: #E8C5A0;"><?= h($t) ?></h2>
                <p class="text-gray-400 text-sm flex-grow mb-4"><?= h(mb_substr(strip_tags($ex), 0, 200)) ?><?= mb_strlen(strip_tags($ex)) > 200 ? '…' : '' ?></p>
                <a href="<?= h(url_path('/news/' . rawurlencode((string) $n['slug']))) ?>" class="inline-block text-blue-400 hover:underline text-sm"><?= h(t('read_more')) ?></a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
