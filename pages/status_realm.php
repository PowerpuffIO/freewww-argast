<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$realmId = (int) ($GLOBALS['argast_status_realm_id'] ?? $_GET['realm'] ?? 0);
$realm = argast_realm_by_id($realmId);
if ($realm === null) {
    http_response_code(404);
    echo current_lang() === 'en' ? 'Not found' : 'Страница не найдена';
    exit;
}
require_once dirname(__DIR__) . '/inc/realm_stats.php';

$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$img = fn (string $p) => ($pref === '' ? '' : $pref) . '/themes/argast/' . ltrim($p, '/');
$chars = db_characters();

$totalChars = argast_stat_total_characters($chars);
$uniqueDay = argast_stat_unique_accounts_24h($chars);
$faction = argast_stat_faction_percent($chars);
$maxOn = argast_stat_maxplayers_from_uptime($chars, $realmId);
$upt = argast_stat_uptime_row($chars, $realmId);
$uptimeSeconds = (int) ($upt['uptime'] ?? 0);
$uptimeStr = $uptimeSeconds > 0 ? argast_format_uptime($uptimeSeconds) : '—';
$serverTimeStr = date('d/m/Y H:i:s');
$topKills = argast_stat_top_kills($chars, 10);
$topTime = argast_stat_top_playtime($chars, 10);
$topGold = argast_stat_top_money($chars, 10);

$isWotlkBuild = (int) ($realm['gamebuild'] ?? 0) === 12340;
$realmName = (string) ($realm['name'] ?? '');
$wotlkLabel = $isWotlkBuild ? 'WotLK' : '';
$pageTitle = t('status_page_title') . ' — ' . $realmName . ' — ' . setting('meta_title');
$auth_align_top = true;

$raceBadge = static function (int $race) use ($img): string {
    return argast_is_alliance_race($race) ? $img('Images/alliance_badge.png') : $img('Images/horde_badge.png');
};

require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="status-page w-full max-w-6xl mx-auto">
    <div class="status-hero" style="background-image: url('<?= h($img('Images/wotlk_videobackground.webp')) ?>');">
        <div class="status-hero-overlay"></div>
        <div class="status-hero-inner">
            <h1 class="status-hero-title"><?= h(t('status_page_title')) ?></h1>
            <p class="status-hero-sub"><?= h($wotlkLabel !== '' ? $wotlkLabel . ' · ' . $realmName : $realmName) ?></p>
        </div>
    </div>

    <div class="status-panel status-info-grid">
        <div class="status-info-col">
            <div class="status-info-row">
                <span class="status-info-label"><?= h(t('status_auth_label')) ?></span>
                <span class="status-dot status-dot--on"></span>
                <span class="status-info-value"><?= h(t('status_online')) ?></span>
            </div>
            <div class="status-info-row">
                <span class="status-info-label"><?= h(t('status_world_label')) ?></span>
                <span class="status-dot status-dot--on"></span>
                <span class="status-info-value"><?= h(t('status_online')) ?></span>
            </div>
        </div>
        <div class="status-info-col">
            <div class="status-info-row">
                <span class="status-info-label"><?= h(t('status_server_time')) ?></span>
                <span class="status-info-value status-info-value--mono"><?= h($serverTimeStr) ?></span>
            </div>
            <div class="status-info-row">
                <span class="status-info-label"><?= h(t('status_uptime')) ?></span>
                <span class="status-info-value"><?= h($uptimeStr) ?></span>
            </div>
        </div>
    </div>

    <div class="status-stat-trio">
        <div class="status-stat-card">
            <div class="status-stat-num"><?= h(number_format($uniqueDay, 0, '.', ' ')) ?></div>
            <div class="status-stat-desc"><?= h(t('status_stat_unique')) ?></div>
        </div>
        <div class="status-stat-card">
            <div class="status-stat-num"><?= h($maxOn > 0 ? number_format($maxOn, 0, '.', ' ') : '—') ?></div>
            <div class="status-stat-desc"><?= h(t('status_stat_max_online')) ?></div>
        </div>
        <div class="status-stat-card">
            <div class="status-stat-num"><?= h(number_format($totalChars, 0, '.', ' ')) ?></div>
            <div class="status-stat-desc"><?= h(t('status_stat_chars')) ?></div>
        </div>
    </div>

    <div class="status-faction-bar">
        <div class="status-faction-seg status-faction-seg--ally" style="width:<?= (int) $faction['alliance'] ?>%">
            <img src="<?= h($img('Images/alliance_badge.png')) ?>" alt="" class="status-faction-ico" width="28" height="28">
            <span><?= (int) $faction['alliance'] ?>%</span>
        </div>
        <div class="status-faction-seg status-faction-seg--horde" style="width:<?= (int) $faction['horde'] ?>%">
            <span><?= (int) $faction['horde'] ?>%</span>
            <img src="<?= h($img('Images/horde_badge.png')) ?>" alt="" class="status-faction-ico" width="28" height="28">
        </div>
    </div>

    <div class="status-features">
        <h2 class="status-section-title"><?= h(t('status_features_title')) ?></h2>
        <ul class="status-features-list">
            <?php for ($fi = 1; $fi <= 6; $fi++): ?>
            <?php $fk = 'status_feat_' . $fi;
            $ft = t($fk);
            if ($ft === $fk) {
                continue;
            } ?>
            <li><?= h($ft) ?></li>
            <?php endfor; ?>
        </ul>
    </div>

    <div class="status-tables">
        <div class="status-table-wrap">
            <h3 class="status-table-title"><?= h(t('status_top_pvp')) ?></h3>
            <table class="status-table">
                <thead><tr><th>#</th><th></th><th><?= h(t('status_col_name')) ?></th><th><?= h(t('status_col_kills')) ?></th></tr></thead>
                <tbody>
                <?php if (!$topKills): ?>
                <tr><td colspan="4" class="status-table-empty">—</td></tr>
                <?php else: ?>
                <?php $rk = 1;
                foreach ($topKills as $row): ?>
                <tr>
                    <td><?= (string) $rk++ ?></td>
                    <td class="status-table-ico"><img src="<?= h($raceBadge((int) ($row['race'] ?? 0))) ?>" alt="" width="28" height="28"></td>
                    <td><?= h((string) ($row['name'] ?? '')) ?></td>
                    <td><?= h(number_format((int) ($row['k'] ?? 0), 0, '.', ' ')) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="status-table-wrap">
            <h3 class="status-table-title"><?= h(t('status_top_time')) ?></h3>
            <table class="status-table">
                <thead><tr><th>#</th><th></th><th><?= h(t('status_col_name')) ?></th><th><?= h(t('status_col_hours')) ?></th></tr></thead>
                <tbody>
                <?php if (!$topTime): ?>
                <tr><td colspan="4" class="status-table-empty">—</td></tr>
                <?php else: ?>
                <?php $rt = 1;
                foreach ($topTime as $row): ?>
                <tr>
                    <td><?= (string) $rt++ ?></td>
                    <td class="status-table-ico"><img src="<?= h($raceBadge((int) ($row['race'] ?? 0))) ?>" alt="" width="28" height="28"></td>
                    <td><?= h((string) ($row['name'] ?? '')) ?></td>
                    <td><?= h(number_format((int) floor((int) ($row['totaltime'] ?? 0) / 3600), 0, '.', ' ') . ' ' . t('status_hours_suffix')) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="status-table-wrap">
            <h3 class="status-table-title"><?= h(t('status_top_gold')) ?></h3>
            <table class="status-table">
                <thead><tr><th>#</th><th></th><th><?= h(t('status_col_name')) ?></th><th><?= h(t('status_col_gold')) ?></th></tr></thead>
                <tbody>
                <?php if (!$topGold): ?>
                <tr><td colspan="4" class="status-table-empty">—</td></tr>
                <?php else: ?>
                <?php $rg = 1;
                foreach ($topGold as $row): ?>
                <tr>
                    <td><?= (string) $rg++ ?></td>
                    <td class="status-table-ico"><img src="<?= h($raceBadge((int) ($row['race'] ?? 0))) ?>" alt="" width="28" height="28"></td>
                    <td><?= h((string) ($row['name'] ?? '')) ?></td>
                    <td><?= h(argast_format_gold((int) ($row['money'] ?? 0))) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
