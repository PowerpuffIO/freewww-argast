<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function handle_register_post(): void
{
    global $config;
    header('Content-Type: application/json; charset=UTF-8');
    if (!csrf_verify($_POST['csrf'] ?? null)) {
        echo json_encode(['ok' => false, 'message' => t('error_generic')]);

        return;
    }
    $cErr = argast_captcha_verify_post();
    if ($cErr !== null) {
        echo json_encode(['ok' => false, 'message' => $cErr]);

        return;
    }
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $password2 = (string) ($_POST['password_confirm'] ?? '');
    $isRu = current_lang() === 'ru';
    if (!preg_match('/^[a-zA-Z0-9]{1,14}$/', $username)) {
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Логин: латиница и цифры, до 14 символов' : 'Login: latin letters and digits, max 14']);

        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Некорректный email' : 'Invalid email']);

        return;
    }
    if (strlen($password) < 8 || $password !== $password2) {
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Пароль от 8 символов и должен совпадать' : 'Password min 8 chars and must match']);

        return;
    }
    $unameUpper = strtoupper($username);
    $site = db_site();
    $st = $site->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    $st->execute([$username, $email]);
    if ($st->fetch()) {
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Логин или email уже заняты' : 'Username or email already taken']);

        return;
    }
    $auth = db_auth();
    $st = $auth->prepare('SELECT id FROM account WHERE username = ? LIMIT 1');
    $st->execute([$unameUpper]);
    if ($st->fetch()) {
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Такой логин уже есть в игровой базе' : 'Game account already exists']);

        return;
    }
    if (!extension_loaded('gmp')) {
        echo json_encode(['ok' => false, 'message' => 'GMP extension required']);

        return;
    }
    $srp = new Srp6();
    [$salt, $verifier] = $srp->getRegistrationData($unameUpper, $password);
    $exp = (int) ($config['wow_expansion'] ?? 2);
    $sk40 = str_repeat("\0", 40);
    $insertAttempts = [
        ['INSERT INTO account (username, salt, verifier, email, reg_mail, expansion) VALUES (?, ?, ?, ?, ?, ?)', [$unameUpper, $salt, $verifier, $email, $email, $exp]],
        ['INSERT INTO account (username, salt, verifier, email, reg_mail, expansion, joindate) VALUES (?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP())', [$unameUpper, $salt, $verifier, $email, $email, $exp]],
        ['INSERT INTO account (username, salt, verifier, email, expansion) VALUES (?, ?, ?, ?, ?)', [$unameUpper, $salt, $verifier, $email, $exp]],
        ['INSERT INTO account (username, salt, verifier, email, expansion, joindate) VALUES (?, ?, ?, ?, ?, UNIX_TIMESTAMP())', [$unameUpper, $salt, $verifier, $email, $exp]],
        ['INSERT INTO account (username, salt, verifier, sessionkey, email, reg_mail, expansion, joindate) VALUES (?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP())', [$unameUpper, $salt, $verifier, $sk40, $email, $email, $exp]],
        ['INSERT INTO account (username, salt, verifier, sessionkey, email, expansion, joindate) VALUES (?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP())', [$unameUpper, $salt, $verifier, $sk40, $email, $exp]],
    ];
    $gameAccountId = 0;
    $lastDbError = null;
    foreach ($insertAttempts as [$sql, $params]) {
        try {
            $auth->prepare($sql)->execute($params);
            $gameAccountId = (int) $auth->lastInsertId();
            if ($gameAccountId < 1) {
                $st = $auth->prepare('SELECT id FROM account WHERE username = ? LIMIT 1');
                $st->execute([$unameUpper]);
                $row = $st->fetch(\PDO::FETCH_ASSOC);
                $gameAccountId = (int) ($row['id'] ?? 0);
            }
            if ($gameAccountId > 0) {
                break;
            }
        } catch (\Throwable $e) {
            $lastDbError = $e;
        }
    }
    if ($gameAccountId < 1) {
        if ($lastDbError !== null) {
            error_log('argast register auth: ' . $lastDbError->getMessage());
        }
        $msg = $isRu ? 'Ошибка создания игрового аккаунта' : 'Game account error';
        if (!empty($config['debug_auth_errors']) && $lastDbError !== null) {
            $msg .= ': ' . $lastDbError->getMessage();
        }
        echo json_encode(['ok' => false, 'message' => $msg]);

        return;
    }
    try {
        $don = $auth->prepare('INSERT INTO account_donate (id, bonuses, votes, total_votes, total_bonuses, banned) VALUES (?, 0, 0, 0, 0, 0)');
        $don->execute([$gameAccountId]);
    } catch (\Throwable) {
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');
    try {
        $ins = $site->prepare('INSERT INTO users (username, email, password_hash, game_account_id, is_admin, created_at, updated_at) VALUES (?, ?, ?, ?, 0, ?, ?)');
        $ins->execute([$username, $email, $hash, $gameAccountId, $now, $now]);
        $uid = (int) $site->lastInsertId();
        auth_login($uid);
        echo json_encode(['ok' => true, 'redirect' => url_path('/cabinet')]);
    } catch (\Throwable) {
        try {
            $auth->prepare('DELETE FROM account_donate WHERE id = ?')->execute([$gameAccountId]);
            $auth->prepare('DELETE FROM account WHERE id = ?')->execute([$gameAccountId]);
        } catch (\Throwable) {
        }
        echo json_encode(['ok' => false, 'message' => $isRu ? 'Ошибка регистрации на сайте' : 'Site registration error']);
    }
}
