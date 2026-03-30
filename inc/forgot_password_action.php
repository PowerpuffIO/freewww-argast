<?php

declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function handle_forgot_password_post(): void
{
    header('Content-Type: application/json; charset=UTF-8');
    if (!csrf_verify($_POST['csrf'] ?? null)) {
        echo json_encode(['ok' => false, 'message' => t('error_generic')]);

        return;
    }
    $err = argast_captcha_verify_post();
    if ($err !== null) {
        echo json_encode(['ok' => false, 'message' => $err]);

        return;
    }
    echo json_encode(['ok' => true, 'message' => t('forgot_password_notice')]);
}
