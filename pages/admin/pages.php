<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function pg(string $slug): array
{
    $st = db_site()->prepare('SELECT body_ru, body_en FROM static_pages WHERE slug = ?');
    $st->execute([$slug]);
    $r = $st->fetch();

    return $r ? [(string) ($r['body_ru'] ?? ''), (string) ($r['body_en'] ?? '')] : ['', ''];
}
[$pr, $pe] = pg('privacy');
[$tr, $te] = pg('terms');
[$rr, $re] = pg('refund');
?>
<h1 class="cabinet-title"><?= h(t('admin_menu_pages')) ?></h1>
<form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="admin-form-stack admin-form-stack--loose" style="max-width:56rem">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save_pages">
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">privacy</legend>
        <textarea name="privacy_ru" rows="6" class="admin-textarea admin-textarea--mono"><?= h($pr) ?></textarea>
        <textarea name="privacy_en" rows="6" class="admin-textarea admin-textarea--mono"><?= h($pe) ?></textarea>
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">terms</legend>
        <textarea name="terms_ru" rows="6" class="admin-textarea admin-textarea--mono"><?= h($tr) ?></textarea>
        <textarea name="terms_en" rows="6" class="admin-textarea admin-textarea--mono"><?= h($te) ?></textarea>
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">refund</legend>
        <textarea name="refund_ru" rows="6" class="admin-textarea admin-textarea--mono"><?= h($rr) ?></textarea>
        <textarea name="refund_en" rows="6" class="admin-textarea admin-textarea--mono"><?= h($re) ?></textarea>
    </fieldset>
    <button type="submit" class="cabinet-btn cabinet-btn--primary cabinet-btn--sm" style="width:auto"><?= h(t('admin_save')) ?></button>
</form>
