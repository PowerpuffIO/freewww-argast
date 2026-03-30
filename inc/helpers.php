<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_brand_name(): string
{
    $t = trim(setting('meta_title', ''));
    if ($t === '') {
        return trim(setting('hero_h1_line1', 'ARGAST') . setting('hero_h1_line2', '.SU'));
    }
    $parts = preg_split('/\s+[—–-]\s+/u', $t, 2);

    return trim($parts[0] !== '' ? $parts[0] : $t);
}

function youtube_id_from_string(string $raw): string
{
    $s = trim($raw);
    if ($s === '') {
        return '';
    }
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $s)) {
        return $s;
    }
    if (preg_match('/(?:youtube\.com\/(?:watch\?(?:[^&]*&)*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $s, $m)) {
        return (string) $m[1];
    }

    return '';
}

function url_path(string $path, array $query = []): string
{
    global $config;
    $bp = rtrim((string) ($config['base_path'] ?? ''), '/');
    $base = $bp;

    $fragment = '';
    if (($i = strpos($path, '#')) !== false) {
        $fragment = substr($path, $i);
        $path = substr($path, 0, $i);
    }
    $path = trim($path, '/');

    if ($path === '') {
        $u = $base . '/';
        if ($query !== []) {
            $u .= '?' . http_build_query($query);
        }

        return $u . $fragment;
    }

    $parts = explode('/', $path);
    $a = $parts[0] ?? '';
    $b = $parts[1] ?? '';

    if ($a === 'register') {
        $u = $base . '/register.php';
    } elseif ($a === 'login') {
        $u = $base . '/login.php';
    } elseif ($a === 'logout') {
        $u = $base . '/logout.php';
    } elseif ($a === 'cabinet') {
        $u = $base . '/cabinet.php';
        $q = $query;
        if ($b === 'vote') {
            $q['section'] = 'vote';
        }
        if ($q !== []) {
            return $u . '?' . http_build_query($q) . $fragment;
        }

        return $u . $fragment;
    } elseif ($a === 'forgot-password') {
        return $base . '/forgot-password.php' . $fragment;
    } elseif ($a === 'news') {
        if ($b !== '') {
            $q = array_merge(['slug' => $b], $query);

            return $base . '/news.php?' . http_build_query($q) . $fragment;
        }
        $u = $base . '/news.php';
    } elseif ($a === 'privacy') {
        $u = $base . '/privacy.php';
    } elseif ($a === 'terms') {
        $u = $base . '/terms.php';
    } elseif ($a === 'refund') {
        $u = $base . '/refund.php';
    } elseif ($a === 'powerpuffsiteadmin') {
        $allowed = ['main', 'news', 'videos', 'community', 'pages', 'vote'];
        $sec = ($b !== '' && in_array($b, $allowed, true)) ? $b : 'main';
        $q = array_merge(['section' => $sec], $query);

        return $base . '/powerpuffsiteadmin.php?' . http_build_query($q) . $fragment;
    } elseif ($a === 'status' && $b !== '' && ctype_digit($b)) {
        return $base . '/status.php?realm=' . $b . $fragment;
    } else {
        $u = $base . '/' . $path;
        if ($query !== []) {
            $u .= '?' . http_build_query($query);
        }

        return $u . $fragment;
    }

    if ($query !== []) {
        $u .= '?' . http_build_query($query);
    }

    return $u . $fragment;
}

function asset_url(string $path): string
{
    global $config;
    $prefix = rtrim((string) ($config['url_prefix'] ?? ''), '/');
    $p = '/' . ltrim($path, '/');
    if ($prefix === '') {
        return $p;
    }

    return $prefix . $p;
}

function redirect(string $path, int $code = 302): never
{
    header('Location: ' . url_path($path), true, $code);
    exit;
}

function redirect_to(string $path, array $query = [], int $code = 302): never
{
    header('Location: ' . url_path($path, $query), true, $code);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_verify(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('~[^\pL\pN]+~u', '-', $text) ?? '';
    $text = trim((string) $text, '-');
    if ($text === '') {
        $text = 'news-' . bin2hex(random_bytes(4));
    }

    return $text;
}

function current_lang(): string
{
    $l = $_SESSION['lang'] ?? null;
    if ($l === 'en' || $l === 'ru') {
        return $l;
    }
    global $config;

    return ($config['default_lang'] ?? 'ru') === 'en' ? 'en' : 'ru';
}

function lang_switch_url(string $lang): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $parts = parse_url($uri);
    $path = $parts['path'] ?? '/';
    $q = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $q);
    }
    $q['lang'] = $lang;
    $qs = http_build_query($q);

    return $path . ($qs !== '' ? '?' . $qs : '');
}

function t(string $key): string
{
    global $i18n;
    if (isset($i18n[$key])) {
        return $i18n[$key];
    }

    return $key;
}

function setting(string $key, ?string $fallback = null): string
{
    global $site_settings_cache, $config;
    if ($site_settings_cache === null) {
        $site_settings_cache = [];
        try {
            $st = db_site()->query('SELECT skey, value_ru, value_en FROM site_settings');
            foreach ($st->fetchAll() as $row) {
                $site_settings_cache[$row['skey']] = [
                    'ru' => (string) ($row['value_ru'] ?? ''),
                    'en' => (string) ($row['value_en'] ?? ''),
                ];
            }
        } catch (\Throwable) {
            $site_settings_cache = [];
        }
    }
    $lang = current_lang();
    if (isset($site_settings_cache[$key][$lang]) && $site_settings_cache[$key][$lang] !== '') {
        return $site_settings_cache[$key][$lang];
    }
    if (isset($site_settings_cache[$key]['ru']) && $site_settings_cache[$key]['ru'] !== '') {
        return $site_settings_cache[$key]['ru'];
    }

    return $fallback ?? '';
}

function community_url(string $key): string
{
    global $community_cache;
    if ($community_cache === null) {
        $community_cache = [];
        try {
            $st = db_site()->query('SELECT link_key, url_ru, url_en FROM community_links');
            foreach ($st->fetchAll() as $row) {
                $community_cache[$row['link_key']] = [
                    'ru' => (string) ($row['url_ru'] ?? ''),
                    'en' => (string) ($row['url_en'] ?? ''),
                ];
            }
        } catch (\Throwable) {
            $community_cache = [];
        }
    }
    $lang = current_lang();
    if (isset($community_cache[$key][$lang]) && $community_cache[$key][$lang] !== '') {
        return $community_cache[$key][$lang];
    }
    if (isset($community_cache[$key]['ru'])) {
        return $community_cache[$key]['ru'];
    }

    return '#';
}

function argast_start_section_config(): array
{
    $launcher = trim(setting('start_launcher_url', ''));
    if ($launcher === '') {
        $launcher = 'https://argast.su/ArgastInstaller_x64_v1.0.0.exe';
    }
    $req = trim(setting('start_requirements_url', ''));
    if ($req === '') {
        $req = 'https://argast.su/start/wotlk';
    }
    $rv = trim(setting('start_realmlist_vanilla', ''));
    if ($rv === '') {
        $rv = 'set realmlist vanilla.argast.su';
    }
    $rw = trim(setting('start_realmlist_wotlk', ''));
    if ($rw === '') {
        $rw = 'SET realmList logon.argast.su';
    }
    $rl = trim(setting('start_realmlist_legion', ''));
    if ($rl === '') {
        $rl = 'SET portal "logon.argast.su"';
    }

    return [
        'launcherUrl' => $launcher,
        'requirementsUrl' => $req,
        'realmlistVanilla' => $rv,
        'realmlistWotlk' => $rw,
        'realmlistLegion' => $rl,
    ];
}

function invalidate_settings_cache(): void
{
    global $site_settings_cache, $community_cache;
    $site_settings_cache = null;
    $community_cache = null;
}

