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
$excerptKey = $lang === 'en' ? 'excerpt_en' : 'excerpt_ru';
$news = [];
try {
    $st = db_site()->query('SELECT id, slug, title_ru, title_en, excerpt_ru, excerpt_en, image_path, published_at FROM news WHERE published_at IS NOT NULL ORDER BY published_at DESC LIMIT 5');
    $news = $st->fetchAll() ?: [];
} catch (\Throwable) {
}
$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$bg = $pref . '/themes/argast/Images/Backgrounds/wotlk_news_background.webp';
$featured = $news[0] ?? null;
$side = array_slice($news, 1, 4);
?>
<section id="news" class="py-20 relative overflow-hidden bg-cover bg-center bg-no-repeat" style="background-image: url('<?= h($bg) ?>')">
    <div class="absolute top-0 left-0 right-0 h-16 fade-blur" :style="getNewsTopGradientStyle()"></div>
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between mb-16">
            <div>
                <h2 class="text-5xl font-beaufort mb-4" style="color: #F5E6D3;"><?= h(setting('news_section_title')) ?></h2>
                <p class="text-xl text-gray-300"><?= h(setting('news_section_sub')) ?></p>
            </div>
            <a href="<?= h(url_path('/news')) ?>" class="hidden md:block text-orange-400 hover:text-white font-beaufort transition-all duration-300 px-6 py-3 rounded-md border border-orange-400/50 hover:border-orange-300 bg-transparent hover:bg-orange-500/20 backdrop-blur-sm shadow-lg hover:shadow-orange-500/20 transform hover:scale-105"><?= h(setting('news_all_btn')) ?></a>
        </div>
        <?php if (!$featured): ?>
        <p class="text-gray-400 text-center py-12"><?= h(t('news_empty')) ?></p>
        <?php else: ?>
        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 relative">
                <?php
                $pd = $featured['published_at'] ? strtotime((string) $featured['published_at']) : time();
                $day = date('d', $pd);
                $mon = $lang === 'en' ? date('M', $pd) : date('m', $pd);
                $img = $featured['image_path'] !== '' ? ($pref . '/' . ltrim((string) $featured['image_path'], '/')) : ($pref . '/themes/argast/Images/argast_mini.png');
                $ft = (string) ($featured[$titleKey] ?? $featured['title_ru']);
                $ex = (string) ($featured[$excerptKey] ?? $featured['excerpt_ru'] ?? '');
                ?>
                <div class="absolute -top-2 left-6 z-30 group">
                    <div class="rounded-lg px-4 py-4 text-center text-gray-700 shadow-lg border border-gray-300/20 group-hover:shadow-xl group-hover:scale-105" style="background-color: #E8C5A0; transform: scale(1.2);">
                        <div class="text-lg font-bold"><?= h($day) ?></div>
                        <div class="text-xs uppercase font-medium"><?= h($mon) ?></div>
                    </div>
                </div>
                <article class="rounded-2xl shadow-2xl news-featured h-full relative overflow-hidden group cursor-pointer hover:shadow-orange-500/20 hover:shadow-2xl transition-all duration-500">
                    <div class="absolute inset-0 w-full h-full">
                        <img src="<?= h($img) ?>" alt="<?= h($ft) ?>" class="w-full h-full object-cover transition-all duration-500 group-hover:scale-110" loading="lazy">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/60 to-transparent"></div>
                    <div class="relative z-10 h-full flex flex-col justify-end p-8">
                        <h3 class="text-3xl font-bold drop-shadow-lg mb-4 leading-tight" style="color: #E8C5A0;"><?= h($ft) ?></h3>
                        <p class="mb-6 leading-relaxed text-lg drop-shadow-md line-clamp-3" style="color: #E8C5A0;"><?= h(mb_substr(strip_tags($ex), 0, 220)) ?><?= mb_strlen(strip_tags($ex)) > 220 ? '…' : '' ?></p>
                        <div class="flex items-center justify-between">
                            <a href="<?= h(url_path('/news/' . rawurlencode((string) $featured['slug']))) ?>" class="border border-gray-600 hover:text-black hover:border-gray-400 font-beaufort px-6 py-3 rounded-xl transition-all duration-300 inline-block" style="color: #E8C5A0;"><?= h(t('read_more')) ?></a>
                            <span class="text-sm" style="color: #E8C5A0;"><?= h(t('news_label')) ?></span>
                        </div>
                    </div>
                </article>
            </div>
            <div class="space-y-6">
                <?php foreach ($side as $n): ?>
                <?php
                $pd2 = $n['published_at'] ? strtotime((string) $n['published_at']) : time();
                $day2 = date('d', $pd2);
                $mon2 = $lang === 'en' ? date('M', $pd2) : date('m', $pd2);
                $im2 = $n['image_path'] !== '' ? ($pref . '/' . ltrim((string) $n['image_path'], '/')) : ($pref . '/themes/argast/Images/argast_mini.png');
                $t2 = (string) ($n[$titleKey] ?? $n['title_ru']);
                $e2 = (string) ($n[$excerptKey] ?? $n['excerpt_ru'] ?? '');
                ?>
                <a href="<?= h(url_path('/news/' . rawurlencode((string) $n['slug']))) ?>" class="block">
                    <article class="bg-black/20 backdrop-blur-sm border border-gray-700/30 rounded-2xl shadow-xl news-card group hover:shadow-2xl hover:bg-black/30 transition-all duration-300">
                        <div class="flex overflow-hidden rounded-2xl h-28">
                            <div class="w-28 h-28 flex-shrink-0 relative overflow-hidden rounded-l-2xl">
                                <img src="<?= h($im2) ?>" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500" loading="lazy">
                            </div>
                            <div class="flex-1 p-3 flex flex-col justify-between min-h-0">
                                <div>
                                    <div class="text-xs text-gray-400 mb-1"><?= h($day2) ?> <?= h($mon2) ?></div>
                                    <h4 class="font-bold text-sm leading-tight line-clamp-2 mb-1" style="color: #E8C5A0;"><?= h($t2) ?></h4>
                                    <p class="text-xs text-gray-300 line-clamp-2"><?= h(mb_substr(strip_tags($e2), 0, 120)) ?><?= mb_strlen(strip_tags($e2)) > 120 ? '…' : '' ?></p>
                                </div>
                                <span class="text-xs border border-gray-600 font-beaufort px-3 py-1 rounded-lg text-[#E8C5A0] inline-block w-fit"><?= h(t('read_more')) ?></span>
                            </div>
                        </div>
                    </article>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
