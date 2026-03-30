<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function argast_parse_uri(): array
{
    global $config;
    $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $bp = rtrim((string) ($config['base_path'] ?? ''), '/');
    if ($bp !== '' && str_starts_with($rawPath, $bp)) {
        $rawPath = substr($rawPath, strlen($bp)) ?: '/';
    }
    $path = trim($rawPath, '/');
    if ($path === '') {
        return [];
    }
    $parts = explode('/', $path);
    $norm = [];
    foreach ($parts as $p) {
        if ($p === '') {
            continue;
        }
        if (str_ends_with(strtolower($p), '.php')) {
            $p = substr($p, 0, -4);
        }
        $norm[] = $p;
    }

    return $norm;
}

function argast_dispatch(array $segments): void
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $first = $segments[0] ?? '';
    $second = $segments[1] ?? '';

    try {
        if ($method === 'POST' && $first === 'register' && ($segments[1] ?? '') === '') {
            handle_register_post();
            exit;
        }
        if ($method === 'POST' && $first === 'login' && ($segments[1] ?? '') === '') {
            handle_login_post();
            exit;
        }
        if ($method === 'POST' && $first === 'forgot-password') {
            handle_forgot_password_post();
            exit;
        }
    } catch (\Throwable) {
        http_response_code(500);
        echo current_lang() === 'en' ? 'Server error' : 'Ошибка сервера';
        exit;
    }
    if ($method === 'POST' && $first === 'powerpuffsiteadmin') {
        require dirname(__DIR__) . '/pages/admin_post.php';
        exit;
    }

    if ($first === '' || $first === 'index') {
        require dirname(__DIR__) . '/pages/home.php';
        exit;
    }
    if ($first === 'register') {
        require dirname(__DIR__) . '/pages/register.php';
        exit;
    }
    if ($first === 'login') {
        require dirname(__DIR__) . '/pages/login.php';
        exit;
    }
    if ($first === 'forgot-password') {
        require dirname(__DIR__) . '/pages/forgot_password.php';
        exit;
    }
    if ($first === 'logout') {
        auth_logout();
        redirect('/');
    }
    if ($first === 'cabinet') {
        require dirname(__DIR__) . '/pages/cabinet.php';
        exit;
    }
    if ($first === 'news' && $second === '') {
        require dirname(__DIR__) . '/pages/news_list.php';
        exit;
    }
    if ($first === 'news' && $second !== '') {
        $_GET['slug'] = $second;
        require dirname(__DIR__) . '/pages/news_view.php';
        exit;
    }
    if ($first === 'privacy') {
        $GLOBALS['argast_static_slug'] = 'privacy';
        require dirname(__DIR__) . '/pages/page_static.php';
        exit;
    }
    if ($first === 'terms') {
        $GLOBALS['argast_static_slug'] = 'terms';
        require dirname(__DIR__) . '/pages/page_static.php';
        exit;
    }
    if ($first === 'refund') {
        $GLOBALS['argast_static_slug'] = 'refund';
        require dirname(__DIR__) . '/pages/page_static.php';
        exit;
    }
    if ($first === 'powerpuffsiteadmin') {
        $GLOBALS['admin_route'] = $segments;
        require dirname(__DIR__) . '/pages/admin.php';
        exit;
    }
    if ($first === 'status' && $second !== '' && ctype_digit($second)) {
        require_once dirname(__DIR__) . '/inc/realm_stats.php';
        $rid = (int) $second;
        if ($rid < 1 || argast_realm_by_id($rid) === null) {
            http_response_code(404);
            echo current_lang() === 'en' ? 'Not found' : 'Страница не найдена';
            exit;
        }
        $GLOBALS['argast_status_realm_id'] = $rid;
        require dirname(__DIR__) . '/pages/status_realm.php';
        exit;
    }

    http_response_code(404);
    echo current_lang() === 'en' ? 'Not found' : 'Страница не найдена';
    exit;
}
