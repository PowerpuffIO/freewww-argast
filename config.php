<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

return [
    'url_prefix' => '',
    'base_path' => '',
    'site_db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'argast_site',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'auth_db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'auth',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'characters_db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'characters',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'session_name' => 'argastwww',
    'upload_dir' => __DIR__ . '/storage/uploads',
    'upload_url' => '/storage/uploads',
    'default_lang' => 'ru',
    'wow_expansion' => 2,
    'debug_auth_errors' => false,
];
