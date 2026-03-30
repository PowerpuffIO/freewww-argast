<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$vs = db_site()->query('SELECT * FROM videos ORDER BY sort_order ASC, id ASC')->fetchAll();
?>
<h1 class="cabinet-title"><?= h(t('admin_menu_videos')) ?></h1>
<form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="admin-form-card admin-form-stack" style="max-width:36rem">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save_video">
    <input type="hidden" name="video_id" value="0">
    <input type="text" name="youtube_id" required class="admin-input" placeholder="YouTube video ID">
    <input type="text" name="title_ru" required class="admin-input" placeholder="<?= h(t('form_title_ru')) ?>">
    <input type="text" name="title_en" required class="admin-input" placeholder="<?= h(t('form_title_en')) ?>">
    <input type="number" name="sort_order" value="0" class="admin-input" placeholder="sort">
    <button type="submit" class="cabinet-btn cabinet-btn--primary cabinet-btn--sm" style="width:auto"><?= h(t('admin_save')) ?></button>
</form>
<ul style="list-style:none;padding:0;margin:0">
<?php foreach ($vs as $v): ?>
<li class="admin-list-row">
    <span><?= h((string) $v['youtube_id']) ?> — <?= h((string) $v['title_ru']) ?></span>
    <form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="inline" onsubmit="return confirm('OK?');">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="action" value="delete_video">
        <input type="hidden" name="video_id" value="<?= h((string) $v['id']) ?>">
        <button type="submit" class="admin-btn-danger"><?= h(t('admin_delete')) ?></button>
    </form>
</li>
<?php endforeach; ?>
</ul>
