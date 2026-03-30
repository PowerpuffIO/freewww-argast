<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_once __DIR__ . '/inc/kernel.php';
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
if ($slug !== '') {
    $_GET['slug'] = $slug;
    argast_dispatch(['news', $slug]);
} else {
    argast_dispatch(['news']);
}
