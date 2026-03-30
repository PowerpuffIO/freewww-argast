<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function auth_user(): ?array
{
    static $cached = false;
    static $user = null;
    if ($cached) {
        return $user;
    }
    $cached = true;
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    try {
        $st = db_site()->prepare('SELECT id, username, email, game_account_id, is_admin, created_at FROM users WHERE id = ? LIMIT 1');
        $st->execute([(int) $_SESSION['user_id']]);
        $row = $st->fetch();
        if (!$row) {
            unset($_SESSION['user_id']);

            return null;
        }
        $user = $row;
    } catch (\Throwable) {
        $user = null;
    }

    return $user;
}

function auth_login(int $userId): void
{
    $_SESSION['user_id'] = $userId;
    session_regenerate_id(true);
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
    }
    session_destroy();
}

function require_login(): array
{
    $u = auth_user();
    if ($u === null) {
        redirect('/login');
    }

    return $u;
}

function require_admin(): array
{
    $u = require_login();
    if ((int) ($u['is_admin'] ?? 0) !== 1) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }

    return $u;
}
