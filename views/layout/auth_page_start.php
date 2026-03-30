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
?><!DOCTYPE html>
<html lang="<?= h(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle ?? '') ?></title>
    <link rel="stylesheet" href="<?= h($cssB) ?>">
    <script type="module" src="<?= h($jsB) ?>"></script>
    <link rel="stylesheet" href="<?= h($pref) ?>/themes/argast/css/fonts.css">
    <link rel="stylesheet" href="<?= h($pref) ?>/themes/argast/css/local-fallback.css">
    <link rel="icon" href="<?= h($pref) ?>/themes/argast/favicon.ico">
    <?= argast_captcha_head_markup() ?>
</head>
<body class="bg-[#1a1a1a] theme-wotlk text-white min-h-screen flex flex-col">
<?php require dirname(__DIR__) . '/partials/navbar.php'; ?>
<?php
$auth_main_variant = !empty($auth_align_top) ? 'auth-page-main--top' : 'auth-page-main--centered';
?>
<div class="auth-page-main <?= h($auth_main_variant) ?> flex-grow px-4 flex w-full min-w-0">
