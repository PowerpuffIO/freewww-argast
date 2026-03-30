<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$config = require dirname(__DIR__) . '/config.php';

$site_settings_cache = null;
$community_cache = null;

session_name($config['session_name']);
session_start();

if (isset($_GET['lang']) && ($_GET['lang'] === 'ru' || $_GET['lang'] === 'en')) {
    $_SESSION['lang'] = $_GET['lang'];
}
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = ($config['default_lang'] ?? 'ru') === 'en' ? 'en' : 'ru';
}
if ($_SESSION['lang'] !== 'ru' && $_SESSION['lang'] !== 'en') {
    $_SESSION['lang'] = 'ru';
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/realm_data.php';
require_once __DIR__ . '/auth.php';

$langFile = dirname(__DIR__) . '/lang/' . current_lang() . '.php';
$i18n = is_file($langFile) ? (require $langFile) : [];

require_once __DIR__ . '/captcha.php';

$uploadDir = $config['upload_dir'];
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
    @mkdir($uploadDir . '/news', 0755, true);
}
