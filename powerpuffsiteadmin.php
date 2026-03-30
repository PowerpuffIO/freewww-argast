<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_once __DIR__ . '/inc/kernel.php';
$sec = (string) ($_GET['section'] ?? 'main');
$ok = ['main', 'news', 'videos', 'community', 'pages', 'vote'];
if (!in_array($sec, $ok, true)) {
    $sec = 'main';
}
argast_dispatch(['powerpuffsiteadmin', $sec]);
