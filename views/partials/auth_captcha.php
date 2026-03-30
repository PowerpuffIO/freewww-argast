<?php

declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

global $config;
$ct = (int) ($config['captcha_type'] ?? 0);
$sk = trim((string) ($config['captcha_site_key'] ?? ''));
if ($ct < 1 || $sk === '' || trim((string) ($config['captcha_secret_key'] ?? '')) === '') {
    return;
}
if ($ct === 1): ?>
        <div class="flex justify-center [&_.g-recaptcha]:inline-block">
            <div class="g-recaptcha" data-sitekey="<?= h($sk) ?>"></div>
        </div>
<?php elseif ($ct === 2): ?>
        <div class="flex justify-center">
            <div class="cf-turnstile" data-sitekey="<?= h($sk) ?>" data-theme="dark"></div>
        </div>
<?php endif;
