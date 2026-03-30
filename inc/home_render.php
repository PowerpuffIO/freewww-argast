<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function render_home_suffix(): string
{
    global $config;
    $path = dirname(__DIR__) . '/templates/home_suffix.html';
    $raw = is_file($path) ? (string) file_get_contents($path) : '';
    $prefix = rtrim((string) ($config['url_prefix'] ?? ''), '/');
    $raw = str_replace('https://argast.su', $prefix, $raw);
    $bp = h(url_path('/'));
    $raw = str_replace(['href="index.html"', "href='index.html'"], ['href="' . $bp . '"', "href='" . $bp . "'"], $raw);
    $flat = [
        'href="/privacy"' => 'href="' . h(url_path('/privacy')) . '"',
        'href="/terms"' => 'href="' . h(url_path('/terms')) . '"',
        'href="/refund"' => 'href="' . h(url_path('/refund')) . '"',
        'href="/register"' => 'href="' . h(url_path('/register')) . '"',
        'href="/login"' => 'href="' . h(url_path('/login')) . '"',
    ];
    $raw = strtr($raw, $flat);
    $bgVideo = ($prefix === '' ? '' : $prefix) . '/themes/argast/Images/wotlk_videobackground.webp';
    $repl = [
        '{{VIDEO_SECTION_BG_URL}}' => h($bgVideo),
        '{{VIDEO_SECTION_TITLE}}' => h(setting('video_section_title')),
        '{{VIDEO_SECTION_SUB}}' => h(setting('video_section_sub')),
        '{{COMMUNITY_TITLE}}' => h(setting('community_title')),
        '{{COMMUNITY_SUB}}' => h(setting('community_sub')),
        '{{SECTION_DISCORD_URL}}' => h(community_url('section_discord')),
        '{{SECTION_VK_URL}}' => h(community_url('section_vk')),
        '{{SECTION_TELEGRAM_URL}}' => h(community_url('section_telegram')),
        '{{FOOTER_COPYRIGHT_LINE}}' => '© 2018-' . date('Y') . ' ' . h(site_brand_name()) . '. ' . h(t('footer_trademark')),
    ];

    return strtr($raw, $repl);
}
