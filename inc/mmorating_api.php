<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

/**
 * Server-side MMORating.top vote check (do not expose API key to the browser).
 *
 * @return array{ok:bool,has_voted?:bool,error?:string,checked_at?:string,raw?:mixed}
 */
function mmorating_vote_check_flexible(string $apiKey, string $email, ?string $characterName): array
{
    $email = trim($email);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'invalid_email'];
    }
    $body = [
        'api_key' => $apiKey,
        'email' => $email,
    ];
    $cn = $characterName !== null ? trim($characterName) : '';
    if ($cn !== '') {
        $body['character_name'] = $cn;
    }
    $url = 'https://mmorating.top/api/v1/vote/check-flexible';
    $raw = http_post_json($url, $body, 12);
    if ($raw === null) {
        return ['ok' => false, 'error' => 'http'];
    }
    if (empty($raw['success'])) {
        return ['ok' => false, 'error' => (string) ($raw['error'] ?? 'api'), 'raw' => $raw];
    }

    return [
        'ok' => true,
        'has_voted' => (bool) ($raw['has_voted'] ?? false),
        'checked_at' => isset($raw['checked_at']) ? (string) $raw['checked_at'] : '',
        'raw' => $raw,
    ];
}

function http_post_json(string $url, array $body, int $timeoutSec = 10): ?array
{
    $json = json_encode($body, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return null;
    }
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_TIMEOUT => $timeoutSec,
        ]);
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!is_string($resp) || $code !== 200) {
            return null;
        }
        $dec = json_decode($resp, true);

        return is_array($dec) ? $dec : null;
    }
    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
            'content' => $json,
            'timeout' => $timeoutSec,
        ],
    ]);
    $resp = @file_get_contents($url, false, $ctx);
    if (!is_string($resp)) {
        return null;
    }
    $dec = json_decode($resp, true);

    return is_array($dec) ? $dec : null;
}
