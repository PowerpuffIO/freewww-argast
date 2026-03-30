<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

function gv(string $k, string $lang): string
{
    try {
        $st = db_site()->prepare('SELECT value_ru, value_en FROM site_settings WHERE skey = ?');
        $st->execute([$k]);
        $r = $st->fetch();
        if (!$r) {
            return '';
        }

        return (string) ($lang === 'en' ? ($r['value_en'] ?? '') : ($r['value_ru'] ?? ''));
    } catch (\Throwable) {
        return '';
    }
}
function taglines_lines(string $lang): string
{
    $j = gv('hero_taglines', $lang);
    $a = json_decode($j, true);
    if (is_array($a)) {
        return implode("\n", $a);
    }

    return $j;
}
function gvEither(string $k): string
{
    $a = gv($k, 'ru');
    if ($a !== '') {
        return $a;
    }

    return gv($k, 'en');
}
?>
<h1 class="cabinet-title"><?= h(t('admin_menu_main')) ?></h1>
<form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="admin-form-stack admin-form-stack--loose" style="max-width:48rem">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save_main">
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">meta_title</legend>
        <input type="text" name="meta_title_ru" value="<?= h(gv('meta_title', 'ru')) ?>" class="admin-input" placeholder="RU">
        <input type="text" name="meta_title_en" value="<?= h(gv('meta_title', 'en')) ?>" class="admin-input" placeholder="EN">
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">Hero H1</legend>
        <input type="text" name="hero_h1_line1_ru" value="<?= h(gv('hero_h1_line1', 'ru')) ?>" class="admin-input">
        <input type="text" name="hero_h1_line1_en" value="<?= h(gv('hero_h1_line1', 'en')) ?>" class="admin-input">
        <input type="text" name="hero_h1_line2_ru" value="<?= h(gv('hero_h1_line2', 'ru')) ?>" class="admin-input">
        <input type="text" name="hero_h1_line2_en" value="<?= h(gv('hero_h1_line2', 'en')) ?>" class="admin-input">
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">hero_subtitle</legend>
        <input type="text" name="hero_subtitle_ru" value="<?= h(gv('hero_subtitle', 'ru')) ?>" class="admin-input">
        <input type="text" name="hero_subtitle_en" value="<?= h(gv('hero_subtitle', 'en')) ?>" class="admin-input">
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">hero_taglines (one per line)</legend>
        <textarea name="hero_taglines_ru" rows="6" class="admin-textarea admin-textarea--mono"><?= h(taglines_lines('ru')) ?></textarea>
        <textarea name="hero_taglines_en" rows="6" class="admin-textarea admin-textarea--mono"><?= h(taglines_lines('en')) ?></textarea>
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">News block</legend>
        <input type="text" name="news_section_title_ru" value="<?= h(gv('news_section_title', 'ru')) ?>" class="admin-input">
        <input type="text" name="news_section_title_en" value="<?= h(gv('news_section_title', 'en')) ?>" class="admin-input">
        <input type="text" name="news_section_sub_ru" value="<?= h(gv('news_section_sub', 'ru')) ?>" class="admin-input">
        <input type="text" name="news_section_sub_en" value="<?= h(gv('news_section_sub', 'en')) ?>" class="admin-input">
        <input type="text" name="news_all_btn_ru" value="<?= h(gv('news_all_btn', 'ru')) ?>" class="admin-input">
        <input type="text" name="news_all_btn_en" value="<?= h(gv('news_all_btn', 'en')) ?>" class="admin-input">
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend"><?= h(t('admin_instruction_youtube')) ?></legend>
        <p class="text-sm" style="color:rgb(156,163,175);margin:0 0 0.5rem"><?= h(t('admin_instruction_youtube_hint')) ?></p>
        <input type="text" name="instruction_youtube" value="<?= h(setting('start_instruction_youtube')) ?>" class="admin-input" placeholder="https://www.youtube.com/watch?v=…">
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend"><?= h(t('admin_start_play_section')) ?></legend>
        <p class="text-sm" style="color:rgb(156,163,175);margin:0 0 0.5rem"><?= h(t('admin_start_play_section_hint')) ?></p>
        <label class="text-xs text-gray-400 block mb-1"><?= h(t('admin_start_launcher_url')) ?></label>
        <input type="url" name="start_launcher_url" value="<?= h(gvEither('start_launcher_url')) ?>" class="admin-input" placeholder="https://…/launcher.exe">
        <label class="text-xs text-gray-400 block mb-1 mt-3"><?= h(t('admin_start_requirements_url')) ?></label>
        <input type="url" name="start_requirements_url" value="<?= h(gvEither('start_requirements_url')) ?>" class="admin-input" placeholder="https://…/requirements">
        <label class="text-xs text-gray-400 block mb-1 mt-3"><?= h(t('admin_start_realmlist_vanilla')) ?></label>
        <input type="text" name="start_realmlist_vanilla" value="<?= h(gvEither('start_realmlist_vanilla')) ?>" class="admin-input admin-textarea--mono" autocomplete="off">
        <label class="text-xs text-gray-400 block mb-1 mt-3"><?= h(t('admin_start_realmlist_wotlk')) ?></label>
        <input type="text" name="start_realmlist_wotlk" value="<?= h(gvEither('start_realmlist_wotlk')) ?>" class="admin-input admin-textarea--mono" autocomplete="off">
        <label class="text-xs text-gray-400 block mb-1 mt-3"><?= h(t('admin_start_realmlist_legion')) ?></label>
        <input type="text" name="start_realmlist_legion" value="<?= h(gvEither('start_realmlist_legion')) ?>" class="admin-input admin-textarea--mono" autocomplete="off">
    </fieldset>
    <fieldset class="admin-fieldset admin-form-stack">
        <legend class="admin-legend">Video + community</legend>
        <input type="text" name="video_section_title_ru" value="<?= h(gv('video_section_title', 'ru')) ?>" class="admin-input">
        <input type="text" name="video_section_title_en" value="<?= h(gv('video_section_title', 'en')) ?>" class="admin-input">
        <input type="text" name="video_section_sub_ru" value="<?= h(gv('video_section_sub', 'ru')) ?>" class="admin-input">
        <input type="text" name="video_section_sub_en" value="<?= h(gv('video_section_sub', 'en')) ?>" class="admin-input">
        <input type="text" name="community_title_ru" value="<?= h(gv('community_title', 'ru')) ?>" class="admin-input">
        <input type="text" name="community_title_en" value="<?= h(gv('community_title', 'en')) ?>" class="admin-input">
        <textarea name="community_sub_ru" rows="2" class="admin-textarea" style="min-height:4rem"><?= h(gv('community_sub', 'ru')) ?></textarea>
        <textarea name="community_sub_en" rows="2" class="admin-textarea" style="min-height:4rem"><?= h(gv('community_sub', 'en')) ?></textarea>
    </fieldset>
    <button type="submit" class="cabinet-btn cabinet-btn--primary cabinet-btn--sm" style="width:auto"><?= h(t('admin_save')) ?></button>
</form>
