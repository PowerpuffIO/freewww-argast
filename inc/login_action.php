<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function handle_login_post(): void
{
    header('Content-Type: application/json; charset=UTF-8');
    if (!csrf_verify($_POST['csrf'] ?? null)) {
        echo json_encode(['ok' => false, 'message' => t('error_generic')]);

        return;
    }
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $isRu = current_lang() === 'ru';
    if ($username === '' || $password === '') {
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Введите логин и пароль' : 'Enter username and password']);

        return;
    }
    $st = db_site()->prepare('SELECT id, password_hash FROM users WHERE username = ? LIMIT 1');
    $st->execute([$username]);
    $row = $st->fetch();
    if (!$row || !password_verify($password, (string) $row['password_hash'])) {
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Неверный логин или пароль' : 'Invalid credentials']);

        return;
    }
    auth_login((int) $row['id']);
    echo json_encode(['ok' => true, 'redirect' => url_path('/cabinet')]);
}
