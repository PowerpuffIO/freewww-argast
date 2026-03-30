<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_once __DIR__ . '/inc/kernel.php';
require_once __DIR__ . '/inc/realm_stats.php';
$rid = (int) ($_GET['realm'] ?? 0);
if ($rid < 1 || argast_realm_by_id($rid) === null) {
    http_response_code(404);
    echo current_lang() === 'en' ? 'Not found' : 'Страница не найдена';
    exit;
}
$GLOBALS['argast_status_realm_id'] = $rid;
require __DIR__ . '/pages/status_realm.php';
