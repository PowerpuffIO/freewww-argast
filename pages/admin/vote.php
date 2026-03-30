<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$curKey = trim(setting('mmorating_api_key', ''));
$masked = '';
if ($curKey !== '' && str_starts_with($curKey, 'mmr_')) {
    $masked = 'mmr_' . str_repeat('•', 12) . substr($curKey, -4);
}
$bonus = (int) trim(setting('vote_bonus_amount', '0'));
?>
<h1 class="cabinet-title"><?= h(t('admin_vote_title')) ?></h1>
<p class="text-gray-400 text-sm mb-6 max-w-2xl"><?= h(t('admin_vote_intro')) ?></p>
<form method="post" action="<?= h(url_path('/powerpuffsiteadmin')) ?>" class="space-y-6 max-w-xl">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save_vote">
    <div>
        <label class="block text-sm text-gray-300 mb-1"><?= h(t('admin_vote_api_key')) ?></label>
        <input type="password" name="mmorating_api_key" class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white" placeholder="<?= $curKey !== '' ? h($masked) : 'mmr_...' ?>" autocomplete="off">
        <p class="text-gray-500 text-xs mt-1"><?= h(t('admin_vote_api_key_hint')) ?></p>
    </div>
    <div>
        <label class="block text-sm text-gray-300 mb-1"><?= h(t('admin_vote_bonus')) ?></label>
        <input type="number" name="vote_bonus_amount" min="1" max="999999" value="<?= h((string) max(1, $bonus)) ?>" required class="w-full rounded-lg bg-black/40 border border-white/20 px-4 py-2 text-white">
        <p class="text-gray-500 text-xs mt-1"><?= h(t('admin_vote_bonus_hint')) ?></p>
    </div>
    <button type="submit" class="font-beaufort py-3 px-6 rounded-lg bg-[#3b82f6] hover:bg-[#2563eb] text-white font-semibold"><?= h(t('admin_save')) ?></button>
</form>
