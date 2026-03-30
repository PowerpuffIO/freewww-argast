<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function argast_is_alliance_race(int $race): bool
{
    return in_array($race, [1, 3, 4, 7, 11], true);
}

function argast_char_column_exists(\PDO $pdo, string $table, string $column): bool
{
    try {
        $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
        if (!$db) {
            return false;
        }
        $st = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $st->execute([(string) $db, $table, $column]);

        return (int) $st->fetchColumn() > 0;
    } catch (\Throwable) {
        return false;
    }
}

function argast_stat_total_characters(\PDO $pdo): int
{
    try {
        $st = $pdo->query('SELECT COUNT(*) AS c FROM characters');

        return (int) ($st->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0);
    } catch (\Throwable) {
        return 0;
    }
}

function argast_stat_unique_accounts_24h(\PDO $pdo): int
{
    try {
        if (argast_char_column_exists($pdo, 'characters', 'logout_time')) {
            $st = $pdo->query('SELECT COUNT(DISTINCT account) AS c FROM characters WHERE logout_time > UNIX_TIMESTAMP() - 86400');

            return (int) ($st->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0);
        }
    } catch (\Throwable) {
    }

    return 0;
}

function argast_stat_faction_percent(\PDO $pdo): array
{
    $ally = 0;
    $horde = 0;
    try {
        $st = $pdo->query('SELECT race FROM characters');
        foreach ($st->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $r = (int) ($row['race'] ?? 0);
            if (argast_is_alliance_race($r)) {
                ++$ally;
            } else {
                ++$horde;
            }
        }
    } catch (\Throwable) {
        return ['alliance' => 50, 'horde' => 50, 'ally_n' => 0, 'horde_n' => 0];
    }
    $t = $ally + $horde;
    if ($t < 1) {
        return ['alliance' => 50, 'horde' => 50, 'ally_n' => 0, 'horde_n' => 0];
    }

    return [
        'alliance' => (int) round(100 * $ally / $t),
        'horde' => (int) round(100 * $horde / $t),
        'ally_n' => $ally,
        'horde_n' => $horde,
    ];
}

function argast_stat_uptime_row(\PDO $pdo, int $realmId): ?array
{
    $candidates = [
        'SELECT starttime, uptime, maxplayers FROM uptime WHERE realmid = ? ORDER BY starttime DESC LIMIT 1',
        'SELECT starttime, uptime, maxplayers FROM uptime ORDER BY starttime DESC LIMIT 1',
    ];
    foreach ($candidates as $i => $sql) {
        try {
            if ($i === 0) {
                $st = $pdo->prepare($sql);
                $st->execute([$realmId]);
            } else {
                $st = $pdo->query($sql);
            }
            $r = $st->fetch(\PDO::FETCH_ASSOC);

            return $r ?: null;
        } catch (\Throwable) {
        }
    }

    return null;
}

function argast_stat_maxplayers_from_uptime(\PDO $pdo, int $realmId): int
{
    $r = argast_stat_uptime_row($pdo, $realmId);
    if ($r && isset($r['maxplayers'])) {
        return (int) $r['maxplayers'];
    }

    return 0;
}

function argast_format_gold(int $copper): string
{
    $g = intdiv($copper, 10000);

    return number_format($g, 0, '.', ' ') . ' g';
}

function argast_format_uptime(int $seconds): string
{
    if ($seconds < 1) {
        return '—';
    }
    $d = intdiv($seconds, 86400);
    $seconds %= 86400;
    $h = intdiv($seconds, 3600);
    $seconds %= 3600;
    $m = intdiv($seconds, 60);

    return $d . ' д ' . $h . ' ч ' . $m . ' м';
}

function argast_stat_top_kills(\PDO $pdo, int $limit): array
{
    $orderCol = null;
    foreach (['totalKills', 'honorable_kills', 'totalHonorPoints'] as $col) {
        if (argast_char_column_exists($pdo, 'characters', $col)) {
            $orderCol = $col;
            break;
        }
    }
    if ($orderCol === null) {
        return [];
    }
    try {
        $qc = '`' . str_replace('`', '``', $orderCol) . '`';
        $lim = max(1, min(50, $limit));
        $sql = "SELECT name, race, {$qc} AS k FROM characters ORDER BY {$qc} DESC LIMIT {$lim}";
        $st = $pdo->query($sql);

        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    } catch (\Throwable) {
        return [];
    }
}

function argast_stat_top_playtime(\PDO $pdo, int $limit): array
{
    if (!argast_char_column_exists($pdo, 'characters', 'totaltime')) {
        return [];
    }
    try {
        $st = $pdo->query('SELECT name, race, totaltime FROM characters ORDER BY totaltime DESC LIMIT ' . (int) $limit);

        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    } catch (\Throwable) {
        return [];
    }
}

function argast_stat_top_money(\PDO $pdo, int $limit): array
{
    if (!argast_char_column_exists($pdo, 'characters', 'money')) {
        return [];
    }
    try {
        $st = $pdo->query('SELECT name, race, money FROM characters ORDER BY money DESC LIMIT ' . (int) $limit);

        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    } catch (\Throwable) {
        return [];
    }
}
