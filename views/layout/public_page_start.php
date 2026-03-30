<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$cssB = $pref . '/build/assets/app-CoYDE0Sa.css';
$jsB = $pref . '/build/assets/app-BnhhhGtr.js';
$favicon = $pref . '/themes/argast/favicon.ico';
$tl = $taglinesForJs ?? [];
$vd = $videosForJs ?? [];
$lang = current_lang();
$instrYt = youtube_id_from_string(setting('start_instruction_youtube', ''));
$instrVideoPayload = $instrYt !== '' ? ['id' => $instrYt, 'title' => t('start_instruction_video_label')] : null;
$startSectionPayload = argast_start_section_config();
?><!DOCTYPE html>
<html lang="<?= h($lang) ?>" prefix="og: http://ogp.me/ns#" x-data="argastApp()" x-init="init()">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle ?? setting('meta_title')) ?></title>
    <meta name="description" content="<?= h($pageDescription ?? '') ?>">
    <link rel="preload" as="style" href="<?= h($cssB) ?>">
    <link rel="modulepreload" as="script" href="<?= h($jsB) ?>">
    <link rel="stylesheet" href="<?= h($cssB) ?>">
    <script type="module" src="<?= h($jsB) ?>"></script>
    <link rel="stylesheet" href="<?= h($pref) ?>/themes/argast/css/fonts.css">
    <link rel="stylesheet" href="<?= h($pref) ?>/themes/argast/css/local-fallback.css">
    <link rel="icon" href="<?= h($favicon) ?>">
    <script>
    window.__HERO_TAGLINES__ = <?= json_encode($tl, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) ?>;
    window.__ARGAST_VIDEOS__ = <?= json_encode($vd, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) ?>;
    window.__ARGAST_INSTRUCTION_VIDEO__ = <?= json_encode($instrVideoPayload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
    window.__ARGAST_START__ = <?= json_encode($startSectionPayload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
    </script>
</head>
<body class="bg-[#1a1a1a] theme-wotlk text-white min-h-screen flex flex-col" x-data x-init="Alpine.store('worlds', { list: [], selectedId: null, serverMenuOpen: false, currentExpansion: 'wotlk' });">
<?php require dirname(__DIR__) . '/partials/navbar.php'; ?>
