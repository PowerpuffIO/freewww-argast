<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$showForm = isset($_GET['id']);
$row = null;
if ($id > 0) {
    $st = db_site()->prepare('SELECT * FROM news WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
}
$list = db_site()->query('SELECT id, slug, title_ru, title_en, published_at FROM news ORDER BY id DESC')->fetchAll();
$noneLabel = t('form_image_none');
?>
<h1 class="cabinet-title"><?= h(t('admin_menu_news')) ?></h1>
<p style="margin:-0.5rem 0 1.25rem">
    <a href="<?= h(url_path('/powerpuffsiteadmin/news', ['id' => '0'])) ?>" class="cabinet-btn cabinet-btn--primary cabinet-btn--sm" style="width:auto;display:inline-flex"><?= h(t('admin_news_add')) ?></a>
</p>
<?php if ($showForm): ?>
<form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" enctype="multipart/form-data" class="admin-form-card admin-form-stack admin-form-stack--loose">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save_news">
    <input type="hidden" name="news_id" value="<?= h((string) $id) ?>">
    <div>
        <label class="admin-label" for="news_slug"><?= h(t('form_slug')) ?></label>
        <input id="news_slug" type="text" name="slug" value="<?= h((string) ($row['slug'] ?? '')) ?>" class="admin-input" placeholder="slug">
    </div>
    <div>
        <label class="admin-label" for="news_tr"><?= h(t('form_title_ru')) ?></label>
        <input id="news_tr" type="text" name="title_ru" value="<?= h((string) ($row['title_ru'] ?? '')) ?>" class="admin-input" placeholder="<?= h(t('form_title_ru')) ?>" required>
    </div>
    <div>
        <label class="admin-label" for="news_te"><?= h(t('form_title_en')) ?></label>
        <input id="news_te" type="text" name="title_en" value="<?= h((string) ($row['title_en'] ?? '')) ?>" class="admin-input" placeholder="<?= h(t('form_title_en')) ?>" required>
    </div>
    <div>
        <label class="admin-label" for="news_er"><?= h(t('form_excerpt_ru')) ?></label>
        <textarea id="news_er" name="excerpt_ru" rows="2" class="admin-textarea" style="min-height:4rem"><?= h((string) ($row['excerpt_ru'] ?? '')) ?></textarea>
    </div>
    <div>
        <label class="admin-label" for="news_ee"><?= h(t('form_excerpt_en')) ?></label>
        <textarea id="news_ee" name="excerpt_en" rows="2" class="admin-textarea" style="min-height:4rem"><?= h((string) ($row['excerpt_en'] ?? '')) ?></textarea>
    </div>
    <div>
        <label class="admin-label" for="news_br"><?= h(t('form_body_ru')) ?></label>
        <textarea id="news_br" name="body_ru" rows="8" class="admin-textarea"><?= h((string) ($row['body_ru'] ?? '')) ?></textarea>
    </div>
    <div>
        <label class="admin-label" for="news_be"><?= h(t('form_body_en')) ?></label>
        <textarea id="news_be" name="body_en" rows="8" class="admin-textarea"><?= h((string) ($row['body_en'] ?? '')) ?></textarea>
    </div>
    <div class="admin-upload">
        <p class="admin-upload-title"><?= h(t('form_image')) ?></p>
        <p class="admin-upload-desc"><?= h(t('form_image_help')) ?></p>
        <div class="admin-upload-inner">
            <input type="file" id="news_image_inp" name="image" class="admin-upload-input" accept="image/jpeg,image/png,image/webp,image/gif">
            <label for="news_image_inp" class="cabinet-btn cabinet-btn--outline cabinet-btn--sm admin-upload-pick"><?= h(t('form_image_choose')) ?></label>
            <span class="admin-upload-filename" id="news_image_fn"><?= h($noneLabel) ?></span>
        </div>
        <?php if (!empty($row['image_path'])): ?>
        <p class="admin-upload-current"><?= h(t('form_image_current')) ?>: <?= h((string) $row['image_path']) ?></p>
        <?php endif; ?>
    </div>
    <label class="admin-check-row"><input type="checkbox" name="publish" value="1" <?= !empty($row['published_at']) ? 'checked' : '' ?>> <?= h(t('form_publish')) ?></label>
    <button type="submit" class="cabinet-btn cabinet-btn--primary cabinet-btn--sm" style="width:auto"><?= h(t('admin_save')) ?></button>
</form>
<script>
(function(){
  var inp = document.getElementById('news_image_inp');
  var fn = document.getElementById('news_image_fn');
  var none = <?= json_encode($noneLabel, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) ?>;
  if (inp && fn) {
    inp.addEventListener('change', function() {
      fn.textContent = (inp.files && inp.files[0]) ? inp.files[0].name : none;
    });
  }
})();
</script>
<?php endif; ?>
<div class="admin-table-wrap">
<table class="admin-table">
    <thead><tr><th>ID</th><th><?= h(t('form_title_ru')) ?></th><th></th></tr></thead>
    <tbody>
    <?php foreach ($list as $n): ?>
    <tr>
        <td><?= h((string) $n['id']) ?></td>
        <td><?= h((string) $n['title_ru']) ?></td>
        <td>
            <div class="admin-table-actions">
                <a href="<?= h(url_path('/powerpuffsiteadmin/news', ['id' => (string) $n['id']])) ?>" class="cabinet-btn cabinet-btn--outline cabinet-btn--sm"><?= h(t('admin_edit')) ?></a>
                <form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="inline" onsubmit="return confirm('OK?');">
                    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="delete_news">
                    <input type="hidden" name="news_id" value="<?= h((string) $n['id']) ?>">
                    <button type="submit" class="admin-btn-danger"><?= h(t('admin_delete')) ?></button>
                </form>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
