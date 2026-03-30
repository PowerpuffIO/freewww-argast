<?php
declare(strict_types=1);

/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$pref = rtrim((string) ($config['url_prefix'] ?? ''), '/');
$u = auth_user();
?>
<footer class="bg-gray-950 py-8 md:py-16 mt-auto">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
            <div class="sm:col-span-2">
                <a href="<?= h(url_path('/')) ?>" class="flex items-center space-x-3 mb-4">
                    <img src="<?= h($pref) ?>/themes/argast/Images/argast_mini.png" alt="" class="h-10 w-auto" loading="lazy">
                    <span class="text-xl font-beaufort" style="color: #F5E6D3;"><?= h(setting('hero_h1_line1')) ?><span class="text-orange-400"><?= h(setting('hero_h1_line2')) ?></span></span>
                </a>
                <p class="text-gray-400 text-sm mb-4"><?= h('© 2018-' . date('Y') . ' ' . site_brand_name() . '. ' . t('footer_trademark')) ?></p>
                <ul class="space-y-1 text-gray-400 text-sm">
                    <li><a href="<?= h(url_path('/privacy')) ?>" class="hover:text-blue-400"><?= h(t('footer_privacy')) ?></a></li>
                    <li><a href="<?= h(url_path('/terms')) ?>" class="hover:text-blue-400"><?= h(t('footer_terms')) ?></a></li>
                    <li><a href="<?= h(url_path('/refund')) ?>" class="hover:text-blue-400"><?= h(t('footer_refund')) ?></a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-beaufort mb-4" style="color: #F5E6D3;"><?= h(t('footer_nav')) ?></h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="<?= h(url_path('/')) ?>" class="hover:text-blue-400"><?= h(t('footer_home')) ?></a></li>
                    <li><a href="<?= h(url_path('/news')) ?>" class="hover:text-blue-400"><?= h(t('nav_news')) ?></a></li>
                    <?php if (!$u): ?>
                    <li><a href="<?= h(url_path('/register')) ?>" class="hover:text-blue-400"><?= h(t('footer_register')) ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h4 class="font-beaufort mb-4" style="color: #F5E6D3;"><?= h(t('footer_community')) ?></h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="<?= h(community_url('section_discord')) ?>" target="_blank" rel="noopener" class="hover:text-blue-400">Discord</a></li>
                    <li><a href="<?= h(community_url('section_vk')) ?>" target="_blank" rel="noopener" class="hover:text-blue-400">VK</a></li>
                    <li><a href="<?= h(community_url('section_telegram')) ?>" target="_blank" rel="noopener" class="hover:text-blue-400">Telegram</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center text-gray-500 text-sm mt-8 pt-6 border-t border-white/10">
            <a href="https://powerpuff.pro/" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition-colors"><?= h(t('footer_developed_by')) ?></a>
        </div>
    </div>
</footer>
