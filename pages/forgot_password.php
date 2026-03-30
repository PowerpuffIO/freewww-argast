<?php

declare(strict_types=1);


/**
 * This website is provided free of charge.
 * Author: Powerpuff — https://powerpuff.pro/
 * Discord: https://discord.gg/QwCsWtP99A
 * GitHub: https://github.com/PowerpuffIO
 */

$pageTitle = t('forgot_password_title') . ' — ' . setting('meta_title');
require dirname(__DIR__) . '/views/layout/auth_page_start.php';
?>
<div class="w-full max-w-md">
    <div class="bg-blue-950/40 border border-blue-500/30 rounded-2xl p-8 shadow-2xl backdrop-blur-md">
        <h1 class="text-2xl font-beaufort mb-6 text-center" style="color: #F5E6D3;"><?= h(t('forgot_password_title')) ?></h1>
        <p class="text-gray-300 text-sm text-center leading-relaxed mb-6"><?= h(t('forgot_password_notice')) ?></p>
        <p class="text-center text-gray-400 text-sm"><a href="<?= h(url_path('/login')) ?>" class="text-blue-400 hover:underline"><?= h(t('forgot_password_back_login')) ?></a></p>
    </div>
</div>
<?php require dirname(__DIR__) . '/views/layout/auth_page_end.php';
