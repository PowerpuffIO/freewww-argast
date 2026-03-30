<?php

declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function argast_captcha_type(): int
{
    global $config;

    return (int) ($config['captcha_type'] ?? 0);
}

function argast_captcha_keys_ok(): bool
{
    global $config;
    $sk = trim((string) ($config['captcha_site_key'] ?? ''));
    $sec = trim((string) ($config['captcha_secret_key'] ?? ''));

    return $sk !== '' && $sec !== '';
}

/** @return ?string null = OK, otherwise i18n-ready error message */
function argast_captcha_verify_post(): ?string
{
    $type = argast_captcha_type();
    if ($type < 1) {
        return null;
    }
    if (!argast_captcha_keys_ok()) {
        return current_lang() === 'ru'
            ? 'Капча включена в конфиге, но не заданы ключи (site + secret).'
            : 'Captcha is enabled but site/secret keys are missing in config.';
    }

    global $config;
    $secret = trim((string) ($config['captcha_secret_key'] ?? ''));

    if ($type === 1) {
        $token = trim((string) ($_POST['g-recaptcha-response'] ?? ''));
        if ($token === '') {
            return t('captcha_required');
        }
        if (!argast_recaptcha_siteverify($secret, $token)) {
            return t('captcha_recaptcha_failed');
        }

        return null;
    }

    if ($type === 2) {
        $token = trim((string) ($_POST['cf-turnstile-response'] ?? ''));
        if ($token === '') {
            return t('captcha_required');
        }
        if (!argast_turnstile_siteverify($secret, $token)) {
            return t('captcha_turnstile_failed');
        }

        return null;
    }

    return null;
}

function argast_http_post_form(string $url, array $fields): ?array
{
    $body = http_build_query($fields, '', '&');
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 12,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
        if (!is_string($raw)) {
            return null;
        }
        $dec = json_decode($raw, true);

        return is_array($dec) ? $dec : null;
    }
    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body,
            'timeout' => 12,
        ],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    if (!is_string($raw)) {
        return null;
    }
    $dec = json_decode($raw, true);

    return is_array($dec) ? $dec : null;
}

function argast_recaptcha_siteverify(string $secret, string $response): bool
{
    if ($secret === '' || $response === '') {
        return false;
    }
    $data = argast_http_post_form('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => $secret,
        'response' => $response,
    ]);

    return $data !== null && !empty($data['success']);
}

function argast_client_ip_for_captcha(): string
{
    $ip = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
}

function argast_turnstile_siteverify(string $secret, string $response): bool
{
    if ($secret === '' || $response === '') {
        return false;
    }
    $fields = [
        'secret' => $secret,
        'response' => $response,
    ];
    $rip = argast_client_ip_for_captcha();
    if ($rip !== '') {
        $fields['remoteip'] = $rip;
    }
    $data = argast_http_post_form('https://challenges.cloudflare.com/turnstile/v0/siteverify', $fields);

    return $data !== null && !empty($data['success']);
}

function argast_captcha_head_markup(): string
{
    global $config;
    $type = argast_captcha_type();
    if ($type < 1 || !argast_captcha_keys_ok()) {
        return '';
    }
    $hl = preg_replace('/[^a-zA-Z_-]/', '', (string) ($config['captcha_language'] ?? 'en'));
    if ($hl === '') {
        $hl = 'en';
    }
    if ($type === 1) {
        return '<script src="https://www.google.com/recaptcha/api.js?hl=' . h($hl) . '" async defer></script>' . "\n";
    }
    if ($type === 2) {
        return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>' . "\n";
    }

    return '';
}
