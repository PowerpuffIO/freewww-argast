<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

require_admin();
if (!csrf_verify($_POST['csrf'] ?? null)) {
    redirect('/powerpuffsiteadmin');
}
$action = (string) ($_POST['action'] ?? '');
$pdo = db_site();

function upsert_setting(\PDO $pdo, string $key, string $ru, string $en): void
{
    $st = $pdo->prepare('INSERT INTO site_settings (skey, value_ru, value_en) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value_ru = VALUES(value_ru), value_en = VALUES(value_en)');
    $st->execute([$key, $ru, $en]);
}

if ($action === 'save_main') {
    $linesToJson = static function (string $raw): string {
        $a = array_values(array_filter(array_map('trim', explode("\n", $raw))));

        return json_encode($a, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    };
    upsert_setting($pdo, 'meta_title', trim((string) ($_POST['meta_title_ru'] ?? '')), trim((string) ($_POST['meta_title_en'] ?? '')));
    upsert_setting($pdo, 'hero_h1_line1', trim((string) ($_POST['hero_h1_line1_ru'] ?? '')), trim((string) ($_POST['hero_h1_line1_en'] ?? '')));
    upsert_setting($pdo, 'hero_h1_line2', trim((string) ($_POST['hero_h1_line2_ru'] ?? '')), trim((string) ($_POST['hero_h1_line2_en'] ?? '')));
    upsert_setting($pdo, 'hero_subtitle', trim((string) ($_POST['hero_subtitle_ru'] ?? '')), trim((string) ($_POST['hero_subtitle_en'] ?? '')));
    upsert_setting($pdo, 'hero_taglines', $linesToJson((string) ($_POST['hero_taglines_ru'] ?? '')), $linesToJson((string) ($_POST['hero_taglines_en'] ?? '')));
    upsert_setting($pdo, 'news_section_title', trim((string) ($_POST['news_section_title_ru'] ?? '')), trim((string) ($_POST['news_section_title_en'] ?? '')));
    upsert_setting($pdo, 'news_section_sub', trim((string) ($_POST['news_section_sub_ru'] ?? '')), trim((string) ($_POST['news_section_sub_en'] ?? '')));
    upsert_setting($pdo, 'news_all_btn', trim((string) ($_POST['news_all_btn_ru'] ?? '')), trim((string) ($_POST['news_all_btn_en'] ?? '')));
    upsert_setting($pdo, 'video_section_title', trim((string) ($_POST['video_section_title_ru'] ?? '')), trim((string) ($_POST['video_section_title_en'] ?? '')));
    upsert_setting($pdo, 'video_section_sub', trim((string) ($_POST['video_section_sub_ru'] ?? '')), trim((string) ($_POST['video_section_sub_en'] ?? '')));
    $instrId = youtube_id_from_string(trim((string) ($_POST['instruction_youtube'] ?? '')));
    upsert_setting($pdo, 'start_instruction_youtube', $instrId, $instrId);
    $lu = trim((string) ($_POST['start_launcher_url'] ?? ''));
    upsert_setting($pdo, 'start_launcher_url', $lu, $lu);
    $lrq = trim((string) ($_POST['start_requirements_url'] ?? ''));
    upsert_setting($pdo, 'start_requirements_url', $lrq, $lrq);
    $lv = trim((string) ($_POST['start_realmlist_vanilla'] ?? ''));
    upsert_setting($pdo, 'start_realmlist_vanilla', $lv, $lv);
    $lw = trim((string) ($_POST['start_realmlist_wotlk'] ?? ''));
    upsert_setting($pdo, 'start_realmlist_wotlk', $lw, $lw);
    $ll = trim((string) ($_POST['start_realmlist_legion'] ?? ''));
    upsert_setting($pdo, 'start_realmlist_legion', $ll, $ll);
    upsert_setting($pdo, 'community_title', trim((string) ($_POST['community_title_ru'] ?? '')), trim((string) ($_POST['community_title_en'] ?? '')));
    upsert_setting($pdo, 'community_sub', trim((string) ($_POST['community_sub_ru'] ?? '')), trim((string) ($_POST['community_sub_en'] ?? '')));
    invalidate_settings_cache();
    redirect('/powerpuffsiteadmin');
}

if ($action === 'save_news') {
    global $config;
    $nid = (int) ($_POST['news_id'] ?? 0);
    $titleRu = trim((string) ($_POST['title_ru'] ?? ''));
    $titleEn = trim((string) ($_POST['title_en'] ?? ''));
    $slug = trim((string) ($_POST['slug'] ?? ''));
    if ($slug === '') {
        $slug = slugify($titleRu !== '' ? $titleRu : $titleEn);
    }
    $pub = isset($_POST['publish']) ? date('Y-m-d H:i:s') : null;
    $imgPath = '';
    if ($nid > 0) {
        $st = $pdo->prepare('SELECT image_path FROM news WHERE id = ?');
        $st->execute([$nid]);
        $ex = $st->fetch();
        $imgPath = (string) ($ex['image_path'] ?? '');
    }
    if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file((string) $_FILES['image']['tmp_name'])) {
        $f = $_FILES['image'];
        $ext = strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'jpg';
        }
        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dir = rtrim((string) $config['upload_dir'], '/') . '/news';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $dest = $dir . '/' . $name;
        if (move_uploaded_file((string) $f['tmp_name'], $dest)) {
            $imgPath = 'storage/uploads/news/' . $name;
        }
    }
    $now = date('Y-m-d H:i:s');
    if ($nid > 0) {
        $st = $pdo->prepare('UPDATE news SET slug=?, title_ru=?, title_en=?, excerpt_ru=?, excerpt_en=?, body_ru=?, body_en=?, image_path=?, published_at=? WHERE id=?');
        $st->execute([
            $slug, $titleRu, $titleEn,
            (string) ($_POST['excerpt_ru'] ?? ''), (string) ($_POST['excerpt_en'] ?? ''),
            (string) ($_POST['body_ru'] ?? ''), (string) ($_POST['body_en'] ?? ''),
            $imgPath, $pub, $nid,
        ]);
    } else {
        $st = $pdo->prepare('INSERT INTO news (slug, title_ru, title_en, excerpt_ru, excerpt_en, body_ru, body_en, image_path, published_at, created_at) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $st->execute([
            $slug, $titleRu, $titleEn,
            (string) ($_POST['excerpt_ru'] ?? ''), (string) ($_POST['excerpt_en'] ?? ''),
            (string) ($_POST['body_ru'] ?? ''), (string) ($_POST['body_en'] ?? ''),
            $imgPath, $pub, $now,
        ]);
    }
    redirect('/powerpuffsiteadmin/news');
}

if ($action === 'delete_news') {
    $nid = (int) ($_POST['news_id'] ?? 0);
    if ($nid > 0) {
        $pdo->prepare('DELETE FROM news WHERE id = ?')->execute([$nid]);
    }
    redirect('/powerpuffsiteadmin/news');
}

if ($action === 'save_video') {
    $vid = (int) ($_POST['video_id'] ?? 0);
    $yid = (string) preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($_POST['youtube_id'] ?? ''));
    $tr = trim((string) ($_POST['title_ru'] ?? ''));
    $te = trim((string) ($_POST['title_en'] ?? ''));
    $so = (int) ($_POST['sort_order'] ?? 0);
    if ($yid !== '' && $tr !== '' && $te !== '') {
        if ($vid > 0) {
            $pdo->prepare('UPDATE videos SET youtube_id=?, title_ru=?, title_en=?, sort_order=? WHERE id=?')->execute([$yid, $tr, $te, $so, $vid]);
        } else {
            try {
                $pdo->prepare('INSERT INTO videos (youtube_id, title_ru, title_en, sort_order) VALUES (?,?,?,?)')->execute([$yid, $tr, $te, $so]);
            } catch (\Throwable) {
            }
        }
    }
    redirect('/powerpuffsiteadmin/videos');
}

if ($action === 'delete_video') {
    $vid = (int) ($_POST['video_id'] ?? 0);
    if ($vid > 0) {
        $pdo->prepare('DELETE FROM videos WHERE id = ?')->execute([$vid]);
    }
    redirect('/powerpuffsiteadmin/videos');
}

if ($action === 'save_community') {
    $ru = $_POST['url_ru'] ?? [];
    $en = $_POST['url_en'] ?? [];
    if (is_array($ru) && is_array($en)) {
        $st = $pdo->prepare('INSERT INTO community_links (link_key, url_ru, url_en) VALUES (?,?,?) ON DUPLICATE KEY UPDATE url_ru=VALUES(url_ru), url_en=VALUES(url_en)');
        foreach ($ru as $k => $v) {
            $k = (string) $k;
            if (!preg_match('/^[a-z0-9_]+$/', $k)) {
                continue;
            }
            $st->execute([$k, (string) $v, (string) ($en[$k] ?? '')]);
        }
    }
    invalidate_settings_cache();
    redirect('/powerpuffsiteadmin/community');
}

if ($action === 'save_vote') {
    $newKey = trim((string) ($_POST['mmorating_api_key'] ?? ''));
    if ($newKey !== '') {
        upsert_setting($pdo, 'mmorating_api_key', $newKey, $newKey);
    }
    $bonus = max(1, (int) ($_POST['vote_bonus_amount'] ?? 1));
    $bs = (string) $bonus;
    upsert_setting($pdo, 'vote_bonus_amount', $bs, $bs);
    invalidate_settings_cache();
    redirect('/powerpuffsiteadmin/vote');
}

if ($action === 'save_pages') {
    $now = date('Y-m-d H:i:s');
    $up = $pdo->prepare('INSERT INTO static_pages (slug, body_ru, body_en, updated_at) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE body_ru=VALUES(body_ru), body_en=VALUES(body_en), updated_at=VALUES(updated_at)');
    $up->execute(['privacy', (string) ($_POST['privacy_ru'] ?? ''), (string) ($_POST['privacy_en'] ?? ''), $now]);
    $up->execute(['terms', (string) ($_POST['terms_ru'] ?? ''), (string) ($_POST['terms_en'] ?? ''), $now]);
    $up->execute(['refund', (string) ($_POST['refund_ru'] ?? ''), (string) ($_POST['refund_en'] ?? ''), $now]);
    redirect('/powerpuffsiteadmin/pages');
}

redirect('/powerpuffsiteadmin');
