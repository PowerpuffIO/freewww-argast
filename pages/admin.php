<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_admin();
$seg = $GLOBALS['admin_route'] ?? ['powerpuffsiteadmin', 'main'];
$section = $seg[1] ?? 'main';
$pageTitle = t('admin_title') . ' — ' . setting('meta_title');
$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$cssB = $pref . '/build/assets/app-CoYDE0Sa.css';
$jsB = $pref . '/build/assets/app-BnhhhGtr.js';
?><!DOCTYPE html>
<html lang="<?= h(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= h($cssB) ?>">
    <script type="module" src="<?= h($jsB) ?>"></script>
    <link rel="stylesheet" href="<?= h($pref) ?>/themes/argast/css/fonts.css">
    <link rel="stylesheet" href="<?= h($pref) ?>/themes/argast/css/local-fallback.css">
    <link rel="icon" href="<?= h($pref) ?>/themes/argast/favicon.ico">
</head>
<body class="bg-[#1a1a1a] theme-wotlk text-white min-h-screen flex flex-col">
<?php require dirname(__DIR__) . '/views/partials/navbar.php'; ?>
<div class="auth-page-main auth-page-main--top flex-grow px-4 flex w-full min-w-0">
<div class="cabinet-shell cabinet-shell--admin">
    <aside class="cabinet-sidebar">
        <p class="cabinet-title" style="margin-bottom:0.5rem;font-size:1.25rem;line-height:1.75rem"><?= h(t('admin_title')) ?></p>
        <a href="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="cabinet-btn <?= $section === 'main' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('admin_menu_main')) ?></a>
        <a href="<?= h(url_path('/powerpuffsiteadmin/news')) ?>" class="cabinet-btn <?= $section === 'news' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('admin_menu_news')) ?></a>
        <a href="<?= h(url_path('/powerpuffsiteadmin/videos')) ?>" class="cabinet-btn <?= $section === 'videos' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('admin_menu_videos')) ?></a>
        <a href="<?= h(url_path('/powerpuffsiteadmin/community')) ?>" class="cabinet-btn <?= $section === 'community' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('admin_menu_community')) ?></a>
        <a href="<?= h(url_path('/powerpuffsiteadmin/pages')) ?>" class="cabinet-btn <?= $section === 'pages' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('admin_menu_pages')) ?></a>
        <a href="<?= h(url_path('/powerpuffsiteadmin/vote')) ?>" class="cabinet-btn <?= $section === 'vote' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('admin_menu_vote')) ?></a>
        <div class="admin-sidebar-spacer">
            <a href="<?= h(url_path('/cabinet')) ?>" class="cabinet-btn cabinet-btn--outline"><?= h(t('nav_cabinet')) ?></a>
            <a href="<?= h(url_path('/')) ?>" class="cabinet-btn cabinet-btn--outline"><?= h(t('footer_home')) ?></a>
        </div>
    </aside>
    <main class="cabinet-main" style="padding-bottom:2rem">
        <?php
        if ($section === 'main') {
            require __DIR__ . '/admin/main.php';
        } elseif ($section === 'news') {
            require __DIR__ . '/admin/news.php';
        } elseif ($section === 'videos') {
            require __DIR__ . '/admin/videos.php';
        } elseif ($section === 'community') {
            require __DIR__ . '/admin/community.php';
        } elseif ($section === 'pages') {
            require __DIR__ . '/admin/pages.php';
        } elseif ($section === 'vote') {
            require __DIR__ . '/admin/vote.php';
        } else {
            echo '<p class="cabinet-card-label">—</p>';
        }
        ?>
    </main>
</div>
</div>
</body>
</html>
