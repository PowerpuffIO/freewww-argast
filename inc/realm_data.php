<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function argast_realmlist_all(): array
{
    try {
        $st = db_auth()->query('SELECT id, name, gamebuild FROM realmlist ORDER BY id ASC');

        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    } catch (\Throwable) {
        return [];
    }
}

function argast_realm_by_id(int $id): ?array
{
    if ($id < 1) {
        return null;
    }
    try {
        $st = db_auth()->prepare('SELECT id, name, gamebuild FROM realmlist WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);

        return $r ?: null;
    } catch (\Throwable) {
        return null;
    }
}

function argast_realm_online_players(): int
{
    try {
        $st = db_characters()->query('SELECT COUNT(*) AS c FROM characters WHERE online = 1');
        $row = $st->fetch(\PDO::FETCH_ASSOC);

        return (int) ($row['c'] ?? 0);
    } catch (\Throwable) {
        return 0;
    }
}

function argast_realm_characters_has_realmid(\PDO $pdo): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    try {
        $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
        if (!$db) {
            return $cached = false;
        }
        $st = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $st->execute([(string) $db, 'characters', 'realmid']);

        return $cached = ((int) $st->fetchColumn() > 0);
    } catch (\Throwable) {
        return $cached = false;
    }
}

function argast_realm_online_players_for_realm(int $realmId): int
{
    if ($realmId < 1) {
        return 0;
    }
    $pdo = db_characters();
    try {
        if (argast_realm_characters_has_realmid($pdo)) {
            $st = $pdo->prepare('SELECT COUNT(*) AS c FROM characters WHERE online = 1 AND realmid = ?');
            $st->execute([$realmId]);
        } else {
            $st = $pdo->query('SELECT COUNT(*) AS c FROM characters WHERE online = 1');
        }
        $row = $st->fetch(\PDO::FETCH_ASSOC);

        return (int) ($row['c'] ?? 0);
    } catch (\Throwable) {
        return 0;
    }
}
