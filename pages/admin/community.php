<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function cl(string $k): array
{
    $st = db_site()->prepare('SELECT url_ru, url_en FROM community_links WHERE link_key = ?');
    $st->execute([$k]);
    $r = $st->fetch();

    return $r ? [(string) ($r['url_ru'] ?? ''), (string) ($r['url_en'] ?? '')] : ['', ''];
}
$keys = ['nav_discord', 'nav_vk', 'nav_telegram', 'nav_forum', 'nav_bugtracker', 'section_discord', 'section_vk', 'section_telegram'];
?>
<h1 class="cabinet-title"><?= h(t('admin_menu_community')) ?></h1>
<form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="admin-form-stack admin-form-stack--loose" style="max-width:48rem">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save_community">
    <?php foreach ($keys as $key): ?>
    <?php [$ru, $en] = cl($key); ?>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend"><?= h($key) ?></legend>
        <input type="text" name="url_ru[<?= h($key) ?>]" value="<?= h($ru) ?>" class="admin-input" placeholder="URL RU">
        <input type="text" name="url_en[<?= h($key) ?>]" value="<?= h($en) ?>" class="admin-input" placeholder="URL EN">
    </fieldset>
    <?php endforeach; ?>
    <button type="submit" class="cabinet-btn cabinet-btn--primary cabinet-btn--sm" style="width:auto"><?= h(t('admin_save')) ?></button>
</form>
