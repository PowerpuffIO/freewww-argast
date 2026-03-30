<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_once __DIR__ . '/mmorating_api.php';

function vote_claim_reward_day_from_api(?string $checkedAt): string
{
    if (is_string($checkedAt) && preg_match('/^(\d{4}-\d{2}-\d{2})/', $checkedAt, $m)) {
        return $m[1];
    }

    return gmdate('Y-m-d');
}

function vote_claim_handle_post(): void
{
    $u = require_login();
    if (!csrf_verify($_POST['csrf'] ?? null)) {
        redirect_to('/cabinet', ['section' => 'vote', 'err' => 'csrf']);
    }
    $apiKey = trim(setting('mmorating_api_key', ''));
    $bonus = (int) trim(setting('vote_bonus_amount', '0'));
    if ($apiKey === '' || !str_starts_with($apiKey, 'mmr_') || $bonus < 1) {
        redirect_to('/cabinet', ['section' => 'vote', 'err' => 'config']);
    }
    $email = (string) ($u['email'] ?? '');
    $charRaw = isset($_POST['character_name']) ? (string) $_POST['character_name'] : '';
    $char = trim($charRaw);
    if (mb_strlen($char) > 64) {
        $char = mb_substr($char, 0, 64);
    }
    $charParam = ($char !== '') ? $char : null;

    $check = mmorating_vote_check_flexible($apiKey, $email, $charParam);
    if (!$check['ok']) {
        redirect_to('/cabinet', ['section' => 'vote', 'err' => 'api']);
    }
    if (empty($check['has_voted'])) {
        redirect_to('/cabinet', ['section' => 'vote', 'err' => 'novote']);
    }
    $rewardDay = vote_claim_reward_day_from_api($check['checked_at'] ?? null);
    $checkedAt = (string) ($check['checked_at'] ?? '');
    $uid = (int) $u['id'];
    $aid = (int) $u['game_account_id'];
    $site = db_site();
    $auth = db_auth();

    try {
        $site->beginTransaction();
        $ins = $site->prepare('INSERT INTO votes_history (user_id, game_account_id, reward_day, character_name, bonus_amount, mmorating_checked_at, created_at) VALUES (?,?,?,?,?,?,?)');
        $ins->execute([
            $uid,
            $aid,
            $rewardDay,
            $charParam,
            $bonus,
            $checkedAt !== '' ? $checkedAt : null,
            date('Y-m-d H:i:s'),
        ]);
        $site->commit();
    } catch (\PDOException $e) {
        if ($site->inTransaction()) {
            $site->rollBack();
        }
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            redirect_to('/cabinet', ['section' => 'vote', 'err' => 'duplicate']);
        }
        redirect_to('/cabinet', ['section' => 'vote', 'err' => 'api']);
    }

    try {
        $up = $auth->prepare('UPDATE account_donate SET bonuses = bonuses + ? WHERE id = ?');
        $up->execute([$bonus, $aid]);
        if ($up->rowCount() === 0) {
            $auth->prepare('INSERT INTO account_donate (id, bonuses, votes, total_votes, total_bonuses, banned) VALUES (?, ?, 0, 0, 0, 0)')->execute([$aid, $bonus]);
        }
    } catch (\Throwable) {
        try {
            $site->prepare('DELETE FROM votes_history WHERE user_id = ? AND reward_day = ?')->execute([$uid, $rewardDay]);
        } catch (\Throwable) {
        }
        redirect_to('/cabinet', ['section' => 'vote', 'err' => 'donate']);
    }

    redirect_to('/cabinet', ['section' => 'vote', 'ok' => '1']);
}
