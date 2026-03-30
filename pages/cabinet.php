<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_once dirname(__DIR__) . '/inc/vote_claim.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && (string) ($_POST['action'] ?? '') === 'claim_vote') {
    vote_claim_handle_post();
}

$u = require_login();
$section = (string) ($_GET['section'] ?? '');
if ($section !== '' && $section !== 'vote') {
    $section = '';
}

$aid = (int) $u['game_account_id'];
$chars = 0;
try {
    $st = db_characters()->prepare('SELECT COUNT(*) AS c FROM characters WHERE account = ?');
    $st->execute([$aid]);
    $chars = (int) ($st->fetch()['c'] ?? 0);
} catch (\Throwable) {
}

$ban = null;
try {
    $st = db_auth()->prepare('SELECT banreason, bandate, unbandate FROM account_banned WHERE id = ? AND active = 1 ORDER BY bandate DESC LIMIT 1');
    $st->execute([$aid]);
    $ban = $st->fetch() ?: null;
} catch (\Throwable) {
    try {
        $st = db_auth()->prepare('SELECT banreason, bandate, unbandate FROM account_banned WHERE id = ? ORDER BY bandate DESC LIMIT 1');
        $st->execute([$aid]);
        $ban = $st->fetch() ?: null;
    } catch (\Throwable) {
    }
}
$now = time();
$isBanned = false;
if ($ban) {
    $ub = (int) ($ban['unbandate'] ?? 0);
    $bd = (int) ($ban['bandate'] ?? 0);
    if ($ub > $now) {
        $isBanned = true;
    } elseif ($ub === $bd && $ub > 0) {
        $isBanned = true;
    }
}

$balance = 0;
try {
    $st = db_auth()->prepare('SELECT bonuses FROM account_donate WHERE id = ? LIMIT 1');
    $st->execute([$aid]);
    $r = $st->fetch();
    if ($r) {
        $balance = (int) ($r['bonuses'] ?? 0);
    }
} catch (\Throwable) {
}

$voteErrRaw = (string) ($_GET['err'] ?? '');
$voteErrAllowed = ['csrf', 'config', 'novote', 'api', 'duplicate', 'donate'];
$voteErr = in_array($voteErrRaw, $voteErrAllowed, true) ? $voteErrRaw : '';
$voteOk = isset($_GET['ok']) && (string) $_GET['ok'] === '1';
$voteBonusCfg = (int) trim(setting('vote_bonus_amount', '0'));
$voteApiOk = str_starts_with(trim(setting('mmorating_api_key', '')), 'mmr_') && $voteBonusCfg >= 1;

$pageTitle = t('nav_cabinet') . ' — ' . setting('meta_title');
$auth_align_top = true;
require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="cabinet-shell">
    <aside class="cabinet-sidebar">
        <a href="<?= h(url_path('/cabinet')) ?>" class="cabinet-btn <?= $section === '' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('cabinet_home')) ?></a>
        <a href="<?= h(url_path('/cabinet/vote')) ?>" class="cabinet-btn <?= $section === 'vote' ? 'cabinet-btn--primary' : 'cabinet-btn--outline' ?>"><?= h(t('cabinet_vote_tab')) ?></a>
        <?php if ((int) ($u['is_admin'] ?? 0) === 1): ?>
        <a href="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="cabinet-btn cabinet-btn--admin"><?= h(t('cabinet_admin_link')) ?></a>
        <?php endif; ?>
        <a href="<?= h(url_path('/logout')) ?>" class="cabinet-btn cabinet-btn--outline"><?= h(t('nav_logout')) ?></a>
    </aside>
    <div class="cabinet-main">
        <?php if ($section === 'vote'): ?>
        <h1 class="cabinet-title"><?= h(t('vote_page_title')) ?></h1>
        <?php if ($voteOk): ?>
        <p class="text-green-400 text-sm mb-4"><?= h(t('vote_success')) ?></p>
        <?php endif; ?>
        <?php if ($voteErr !== ''): ?>
        <?php
        $ek = 'vote_err_' . $voteErr;
        $voteErrMsg = t($ek);
        if ($voteErrMsg === $ek) {
            $voteErrMsg = t('vote_err_generic');
        }
        ?>
        <p class="text-red-400 text-sm mb-4"><?= h($voteErrMsg) ?></p>
        <?php endif; ?>
        <div class="cabinet-card max-w-xl mb-6">
            <p class="text-gray-300 text-sm leading-relaxed mb-4"><?= h(t('vote_intro')) ?></p>
            <p class="text-gray-400 text-sm mb-2"><?= h(t('vote_bonus_label')) ?>: <span class="text-white font-semibold"><?= h((string) $voteBonusCfg) ?></span></p>
            <?php if (!$voteApiOk): ?>
            <p class="text-amber-400 text-sm"><?= h(t('vote_not_configured')) ?></p>
            <?php else: ?>
            <form method="post" action="<?= h(url_path('/cabinet/vote')) ?>" class="space-y-4 mt-4">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="claim_vote">
                <div>
                    <label class="block text-sm text-gray-300 mb-1"><?= h(t('vote_character_optional')) ?></label>
                    <input type="text" name="character_name" maxlength="64" class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" placeholder="<?= h(t('vote_character_placeholder')) ?>" autocomplete="off">
                </div>
                <p class="text-gray-500 text-xs"><?= h(t('vote_email_hint')) ?></p>
                <button type="submit" class="font-beaufort py-3 px-6 rounded-lg bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold"><?= h(t('vote_claim_button')) ?></button>
            </form>
            <?php endif; ?>
        </div>
        <p class="text-gray-500 text-xs max-w-xl"><a href="https://mmorating.top/" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:underline">MMORating.top</a> — <?= h(t('vote_external_hint')) ?></p>
        <?php else: ?>
        <h1 class="cabinet-title"><?= h(t('nav_cabinet')) ?></h1>
        <div class="cabinet-grid">
            <div class="cabinet-card">
                <div class="cabinet-card-label"><?= h(t('cabinet_reg_date')) ?></div>
                <div class="cabinet-card-value cabinet-card-value--mono"><?= h((string) $u['created_at']) ?></div>
            </div>
            <div class="cabinet-card">
                <div class="cabinet-card-label"><?= h(t('cabinet_chars')) ?></div>
                <div class="cabinet-card-value cabinet-card-value--mono"><?= h((string) $chars) ?></div>
            </div>
            <div class="cabinet-card">
                <div class="cabinet-card-label"><?= h(t('cabinet_ban')) ?></div>
                <div class="cabinet-card-value"><?= $isBanned ? h(t('cabinet_banned_yes')) : h(t('cabinet_not_banned')) ?></div>
                <?php if ($isBanned && $ban): ?>
                <p class="cabinet-card-note"><?= h(t('cabinet_ban_reason')) ?>: <?= h((string) ($ban['banreason'] ?? '')) ?></p>
                <p class="cabinet-card-note"><?= h(t('cabinet_ban_until')) ?>:
                <?php
                $ub = (int) ($ban['unbandate'] ?? 0);
                $bd = (int) ($ban['bandate'] ?? 0);
                if ($ub === $bd || $ub > 2000000000) {
                    echo h(t('cabinet_permanent'));
                } else {
                    echo h(date('Y-m-d H:i', $ub));
                }
                ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="cabinet-card">
                <div class="cabinet-card-label"><?= h(t('cabinet_balance')) ?></div>
                <div class="cabinet-card-value cabinet-card-value--mono"><?= h((string) $balance) ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
