<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_once dirname(__DIR__) . '/inc/home_render.php';

$pageTitle = setting('meta_title');
$pageDescription = current_lang() === 'en' ? 'World of Warcraft servers' : 'Игровые миры World of Warcraft';
$taglinesForJs = json_decode(setting('hero_taglines', '[]'), true);
if (!is_array($taglinesForJs)) {
    $taglinesForJs = [];
}
if ($taglinesForJs === []) {
    $taglinesForJs = current_lang() === 'en'
        ? ['World of Warcraft server community']
        : ['Комплекс серверов World of Warcraft'];
}
$videosForJs = [];
try {
    $vs = db_site()->query('SELECT youtube_id, title_ru, title_en, sort_order FROM videos ORDER BY sort_order ASC, id ASC');
    foreach ($vs->fetchAll() as $v) {
        $t = current_lang() === 'en' ? (string) ($v['title_en'] ?? '') : (string) ($v['title_ru'] ?? '');
        $videosForJs[] = ['id' => (string) $v['youtube_id'], 'title' => $t];
    }
} catch (\Throwable) {
}

require dirname(__DIR__) . '/views/layout/public_page_start.php';
require dirname(__DIR__) . '/views/home/hero_block.php';
require dirname(__DIR__) . '/views/home/news_block.php';
echo render_home_suffix();
